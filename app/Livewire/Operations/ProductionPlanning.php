<?php

namespace App\Livewire\Operations;

use App\Models\ProductionPlan;
use App\Models\ProductionPlanItem;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class ProductionPlanning extends Component
{
    public $activeFilter = 'active'; // active, historical

    public $selectedOrderId = null;
    public $recentDowntime = [];

    // Plan schedule fields
    public $productionLineId;
    public $startDate;
    public $endDate;
    public $planNotes;

    // Add / Edit raw material plan item modal
    public $showMaterialModal = false;
    public $editingPlanItemId = null; // null = new, otherwise = editing
    public $materialId;
    public $materialQty;

    public function mount()
    {
        abort_unless(auth()->user()->can('operations.production-planning'), 403);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        // 1. Orders list
        $query = \App\Models\ProductionOrder::with([
            'customer',
            'items.product',
            'plan.items.rawMaterial',
            'plan.productionLine',
        ]);

        if ($this->activeFilter === 'active') {
            $query->whereIn('status', ['pending', 'pending_production', 'approved']);
        } else {
            $query->whereIn('status', ['in_production', 'completed']);
        }

        $ordersWithDemands = $query->latest()->get();

        // 2. Global material demand summary across all active plans
        $globalMaterialSummary = ProductionPlanItem::with('rawMaterial')
            ->whereHas('plan', fn($q) => $q->whereIn('status', ['draft', 'approved']))
            ->get()
            ->groupBy('raw_material_id')
            ->map(fn($group) => [
                'name'           => $group->first()->rawMaterial->name,
                'unit'           => $group->first()->rawMaterial->unit,
                'total_quantity' => $group->sum('planned_quantity'),
                'in_stock'       => $group->first()->rawMaterial->quantity,
            ]);

        // 3. Detail for selected order
        $viewingOrder          = null;
        $orderItems            = collect();
        $aggregatedMaterialSummary = collect();

        if ($this->selectedOrderId) {
            $viewingOrder = \App\Models\ProductionOrder::with([
                'customer',
                'items.product',
                'plan.items.rawMaterial',
                'plan.productionLine',
            ])->find($this->selectedOrderId);

            if ($viewingOrder) {
                // Load plan schedule into form fields (only if not dirty)
                if ($viewingOrder->plan && !$this->productionLineId) {
                    $this->productionLineId = $viewingOrder->plan->production_line_id;
                    $this->startDate = $viewingOrder->plan->start_date
                        ? $viewingOrder->plan->start_date->format('Y-m-d\TH:i')
                        : null;
                    $this->endDate = $viewingOrder->plan->end_date
                        ? $viewingOrder->plan->end_date->format('Y-m-d\TH:i')
                        : null;
                    $this->planNotes = $viewingOrder->plan->notes;
                }

                // Order items = what products need to be produced
                $orderItems = $viewingOrder->items;

                // Planned materials for this order's plan
                if ($viewingOrder->plan) {
                    $aggregatedMaterialSummary = $viewingOrder->plan->items
                        ->groupBy('raw_material_id')
                        ->map(fn($group) => [
                            'id'             => $group->first()->id,
                            'plan_item_ids'  => $group->pluck('id'),
                            'material_id'    => $group->first()->raw_material_id,
                            'name'           => $group->first()->rawMaterial->name,
                            'unit'           => $group->first()->rawMaterial->unit,
                            'total_quantity' => $group->sum('planned_quantity'),
                            'in_stock'       => $group->first()->rawMaterial->quantity,
                        ]);
                }
            }
        }

        return view('livewire.operations.production-planning', [
            'ordersWithDemands'        => $ordersWithDemands,
            'globalMaterialSummary'    => $globalMaterialSummary,
            'viewingOrder'             => $viewingOrder,
            'orderItems'               => $orderItems,
            'aggregatedMaterialSummary'=> $aggregatedMaterialSummary,
            'productionLines'          => \App\Models\ProductionLine::all(),
            'rawMaterials'             => \App\Models\RawMaterial::orderBy('name')->get(),
        ]);
    }

    // ── Navigation ───────────────────────────────────────────────────────────

    public function selectOrder($id)
    {
        $this->selectedOrderId  = $id;
        $this->productionLineId = null;
        $this->startDate        = null;
        $this->endDate          = null;
        $this->planNotes        = null;
    }

    public function backToList()
    {
        $this->selectedOrderId  = null;
        $this->productionLineId = null;
        $this->startDate        = null;
        $this->endDate          = null;
        $this->planNotes        = null;
    }

    // ── Material modal ───────────────────────────────────────────────────────

    public function openAddMaterialModal()
    {
        $this->editingPlanItemId = null;
        $this->materialId        = null;
        $this->materialQty       = null;
        $this->showMaterialModal = true;
    }

    public function openEditMaterialModal($planItemId)
    {
        $item = ProductionPlanItem::findOrFail($planItemId);
        $this->editingPlanItemId = $planItemId;
        $this->materialId        = $item->raw_material_id;
        $this->materialQty       = $item->planned_quantity;
        $this->showMaterialModal = true;
    }

    public function saveMaterialItem()
    {
        $this->validate([
            'materialId'  => 'required|exists:raw_materials,id',
            'materialQty' => 'required|numeric|min:0.01',
        ]);

        // Ensure a plan exists
        $plan = ProductionPlan::firstOrCreate(
            ['production_order_id' => $this->selectedOrderId],
            ['status' => 'draft', 'created_by' => Auth::id()]
        );

        if ($this->editingPlanItemId) {
            // Update existing
            ProductionPlanItem::where('id', $this->editingPlanItemId)->update([
                'raw_material_id'  => $this->materialId,
                'planned_quantity' => $this->materialQty,
            ]);
            $msg = 'Material requirement updated.';
        } else {
            // Create new — merge with existing same material if any
            $existing = ProductionPlanItem::where('production_plan_id', $plan->id)
                ->where('raw_material_id', $this->materialId)
                ->first();

            if ($existing) {
                $existing->increment('planned_quantity', $this->materialQty);
            } else {
                ProductionPlanItem::create([
                    'production_plan_id' => $plan->id,
                    'raw_material_id'    => $this->materialId,
                    'planned_quantity'   => $this->materialQty,
                ]);
            }
            $msg = 'Material added to plan.';
        }

        $this->showMaterialModal = false;
        $this->reset(['editingPlanItemId', 'materialId', 'materialQty']);
        session()->flash('success', $msg);
    }

    public function deletePlanItem($planItemId)
    {
        ProductionPlanItem::destroy($planItemId);
        session()->flash('success', 'Material removed from plan.');
    }

    // ── Save / Approve plan ──────────────────────────────────────────────────

    public function savePlan()
    {
        $plan = ProductionPlan::updateOrCreate(
            ['production_order_id' => $this->selectedOrderId],
            [
                'production_line_id' => $this->productionLineId ?: null,
                'start_date'         => $this->startDate ?: null,
                'end_date'           => $this->endDate ?: null,
                'notes'              => $this->planNotes,
                'created_by'         => Auth::id(),
            ]
        );

        \App\Models\ProductionOrder::where('id', $this->selectedOrderId)
            ->update(['status' => 'pending_production']);

        session()->flash('success', 'Plan saved as draft.');
    }

    public function approvePlan()
    {
        $this->savePlan();

        $plan = ProductionPlan::where('production_order_id', $this->selectedOrderId)->first();

        if (!$plan || $plan->items->isEmpty()) {
            session()->flash('error', 'Add at least one raw material before releasing the plan.');
            return;
        }

        $plan->update(['status' => 'approved']);
        \App\Models\ProductionOrder::where('id', $this->selectedOrderId)
            ->update(['status' => 'approved']);

        session()->flash('success', 'Plan released to production floor!');
        $this->selectedOrderId = null;
    }
}