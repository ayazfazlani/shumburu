<?php

namespace App\Livewire\Operations;

use App\Models\ProductionRequest;
use App\Models\MaterialRequest;
use App\Models\RawMaterial;
use App\Models\ProductionPlan;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class ProductionPlanning extends Component
{
    use WithPagination;

    public $activeFilter = 'active'; // active, historical

    public function mount()
    {
        abort_unless(auth()->user()->can('operations.production-planning'), 403);
    }
    public $selectedOrderId = null;
    public $recentDowntime = [];

    // New Planning State
    public $productionLineId;
    public $startDate;
    public $endDate;
    public $notes;
    public $planItems = []; // [{material_id, quantity}]

    // For "Add Demand" modal (plan-level material quantity entry)
    public $showDemandModal = false;
    public $demandRequestId;
    public $demandMaterialId;
    public $demandQuantity;

    #[Layout('components.layouts.app')]
    public function render()
    {
        // 1. Orders that need planning or are in planning
        $query = \App\Models\ProductionOrder::with(['customer', 'plan.items.rawMaterial', 'plan.productionLine']);
        
        if ($this->activeFilter === 'active') {
            $query->whereIn('status', ['pending', 'pending_production']);
        } else {
            $query->whereIn('status', ['approved', 'in_production', 'completed']);
        }

        $ordersWithDemands = $query->latest()->get();

        // 2. Global aggregated PLANNED material demands (from ProductionPlanItem)
        $globalMaterialSummary = \App\Models\ProductionPlanItem::with('rawMaterial')
            ->whereHas('plan', function($q) {
                $q->where('status', 'draft');
            })
            ->get()
            ->groupBy('raw_material_id')
            ->map(function ($group) {
                return [
                    'name' => $group->first()->rawMaterial->name,
                    'unit' => $group->first()->rawMaterial->unit,
                    'total_quantity' => $group->sum('planned_quantity'),
                    'in_stock' => $group->first()->rawMaterial->quantity,
                    'status' => 'planned',
                ];
            });

        $viewingOrder = null;
        $selectedOrderDemands = [];
        $aggregatedMaterialSummary = [];

        if ($this->selectedOrderId) {
            $viewingOrder = \App\Models\ProductionOrder::with(['customer', 'plan.items.rawMaterial', 'plan.productionLine'])->find($this->selectedOrderId);
            if ($viewingOrder) {
                // If plan exists, load its data into form
                if ($viewingOrder->plan) {
                    $this->productionLineId = $viewingOrder->plan->production_line_id;
                    $this->startDate = $viewingOrder->plan->start_date ? $viewingOrder->plan->start_date->format('Y-m-d\TH:i') : null;
                    $this->endDate = $viewingOrder->plan->end_date ? $viewingOrder->plan->end_date->format('Y-m-d\TH:i') : null;
                    $this->notes = $viewingOrder->plan->notes;
                }

                $selectedOrderDemands = \App\Models\ProductionRequest::where('production_order_id', $this->selectedOrderId)->get();
                
                // Aggregated material summary for THIS specific order (from its PLAN)
                if ($viewingOrder->plan) {
                    $aggregatedMaterialSummary = $viewingOrder->plan->items
                        ->groupBy('raw_material_id')
                        ->map(function ($group) {
                            return [
                                'name' => $group->first()->rawMaterial->name,
                                'unit' => $group->first()->rawMaterial->unit,
                                'total_quantity' => $group->sum('planned_quantity'),
                                'in_stock' => $group->first()->rawMaterial->quantity,
                            ];
                        });
                }
            }
        }

        return view('livewire.operations.production-planning', [
            'ordersWithDemands' => $ordersWithDemands,
            'globalMaterialSummary' => $globalMaterialSummary,
            'viewingOrder' => $viewingOrder,
            'selectedOrderDemands' => $selectedOrderDemands,
            'aggregatedMaterialSummary' => $aggregatedMaterialSummary,
            'productionLines' => \App\Models\ProductionLine::all(),
            'rawMaterials' => \App\Models\RawMaterial::all(),
        ]);
    }

    public function selectOrder($id)
    {
        $this->selectedOrderId = $id;
    }

    public function backToList()
    {
        $this->selectedOrderId = null;
    }

    public function openDemandModal($requestId)
    {
        $this->demandRequestId = $requestId;
        $this->demandMaterialId = null;
        $this->demandQuantity = null;
        $this->showDemandModal = true;
    }

    public function submitPlannedDemand()
    {
        $this->validate([
            'demandMaterialId' => 'required',
            'demandQuantity' => 'required|numeric|min:0.01',
        ]);

        $order = \App\Models\ProductionOrder::find($this->selectedOrderId);
        $plan = \App\Models\ProductionPlan::firstOrCreate(
            ['production_order_id' => $this->selectedOrderId],
            ['status' => 'draft', 'created_by' => Auth::id()]
        );

        \App\Models\ProductionPlanItem::create([
            'production_plan_id' => $plan->id,
            'production_request_id' => $this->demandRequestId,
            'raw_material_id' => $this->demandMaterialId,
            'planned_quantity' => $this->demandQuantity,
        ]);

        $this->showDemandModal = false;
        session()->flash('success', 'Plan item added.');
    }

    public function savePlan()
    {
        $plan = \App\Models\ProductionPlan::updateOrCreate(
            ['production_order_id' => $this->selectedOrderId],
            [
                'production_line_id' => $this->productionLineId,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'notes' => $this->notes,
                'created_by' => Auth::id(),
            ]
        );

        \App\Models\ProductionOrder::where('id', $this->selectedOrderId)->update(['status' => 'pending_production']);

        session()->flash('success', 'Plan details saved as draft.');
    }

    public function approvePlan()
    {
        $this->savePlan();

        $plan = \App\Models\ProductionPlan::where('production_order_id', $this->selectedOrderId)->first();
        $plan->update(['status' => 'approved']);

        \App\Models\ProductionOrder::where('id', $this->selectedOrderId)->update(['status' => 'approved']);

        session()->flash('success', 'Plan released to production.');
        $this->selectedOrderId = null;
    }

    public function updateStatus($requestId, $status)
    {
        \App\Models\ProductionRequest::where('id', $requestId)->update(['status' => $status]);
        session()->flash('success', 'Request status updated.');
    }
}