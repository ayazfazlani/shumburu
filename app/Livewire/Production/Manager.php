<?php

namespace App\Livewire\Production;

use App\Models\ProductionRequest;
use App\Models\MaterialRequest;
use App\Models\RawMaterial;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Manager extends Component
{
    public $activeTab = 'plans';

    public function mount()
    {
        abort_unless(auth()->user()->can('production.manager'), 403);
    }

    // Plan review
    public $activePlanRequestId = null;
    public $activePlanRequest = null;

    // Daily warehouse request form (sent from Manager to Warehouse)
    public $showWarehouseRequestForm = false;
    public $warehouseRequestPlanId;   // The ProductionPlan id
    public $warehouseRequestMaterialId;
    public $warehouseRequestQty;
    public $warehouseRequestProductionId; // The ProductionOrder id

    // Production execution
    public $activeRequestId = null;
    public $activeRequest = null;
    public $showProductionForm = false;
    public $productionNotes;
    public $actualProduced;

    #[Layout('components.layouts.app')]
    public function render()
    {
        // ── TAB: PLANS (APPROVED ORDERS) ──────────────────────────────────
        // Production orders that are 'approved' (meaning plan is ready)
        $plannedProductionRequests = \App\Models\ProductionOrder::with([
            'customer',
            'plan.productionLine',
            'plan.items.rawMaterial',
        ])
            ->whereIn('status', ['approved', 'in_production'])
            ->latest()
            ->get();

        // ── TAB: WAREHOUSE REQUESTS ──────────────────────────────────────
        // MaterialRequests already sent to warehouse (status = 'pending' — awaiting warehouse action)
        $pendingWarehouseRequests = MaterialRequest::with([
            'rawMaterial',
            'productionPlan.productionOrder.customer',
        ])
            ->where('status', 'pending')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $issuedMaterialsToday = MaterialRequest::with([
            'rawMaterial',
            'productionPlan.productionOrder.customer',
        ])
            ->where('status', 'issued')
            ->whereDate('updated_at', today())
            ->latest()
            ->get();

        // ── TAB: IN PRODUCTION ───────────────────────────────────────────
        $inProgressRequests = \App\Models\ProductionOrder::with([
            'customer',
            'plan.productionLine',
        ])
            ->where('status', 'in_production')
            ->latest()
            ->get();

        // ── TAB: COMPLETED ───────────────────────────────────────────────
        $completedRequests = \App\Models\ProductionOrder::with(['customer'])
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->get();

        // ── SELECTED PLAN DETAIL ─────────────────────────────────────────
        $planMaterialSummary = collect();
        $totalPlannedMaterials = 0;
        $totalSentToWarehouse = 0;

        if ($this->activePlanRequestId) {
            $viewingOrder = \App\Models\ProductionOrder::with(['customer', 'plan.items.rawMaterial'])
                ->find($this->activePlanRequestId);

            if ($viewingOrder && $viewingOrder->plan) {
                $this->activePlanRequest = $viewingOrder;

                // Summarize based on Plan Items
                $planMaterialSummary = $viewingOrder->plan->items->map(function ($item) use ($viewingOrder) {
                    $material = $item->rawMaterial;

                    // How much has already been sent to warehouse for this plan + material
                    $alreadySent = MaterialRequest::where('production_plan_id', $viewingOrder->plan->id)
                        ->where('raw_material_id', $item->raw_material_id)
                        ->whereIn('status', ['pending', 'approved', 'issued', 'consumed', 'purchase_raised'])
                        ->sum('quantity');

                    $plannedQty = $item->planned_quantity;
                    $remaining = $plannedQty - $alreadySent;

                    return [
                        'plan_id' => $viewingOrder->plan->id,
                        'material_id' => $material->id,
                        'material_name' => $material->name,
                        'unit' => $material->unit,
                        'in_stock' => $material->quantity,
                        'planned_qty' => $plannedQty,
                        'already_sent' => $alreadySent,
                        'remaining' => $remaining,
                    ];
                });

                $totalPlannedMaterials = $planMaterialSummary->sum('planned_qty');
                $totalSentToWarehouse = $planMaterialSummary->sum('already_sent');
            }
        }

        return view('livewire.production.manager', [
            'plannedProductionRequests' => $plannedProductionRequests,
            'pendingWarehouseRequests' => $pendingWarehouseRequests,
            'issuedMaterialsToday' => $issuedMaterialsToday,
            'inProgressRequests' => $inProgressRequests,
            'completedRequests' => $completedRequests,
            'planMaterialSummary' => $planMaterialSummary,
            'totalPlannedMaterials' => $totalPlannedMaterials,
            'totalSentToWarehouse' => $totalSentToWarehouse,
        ]);
    }

    // ── Plan selection ────────────────────────────────────────────────────

    public function selectPlan($orderId)
    {
        $this->activePlanRequestId = $orderId;
        $this->activePlanRequest = \App\Models\ProductionOrder::with(['customer', 'plan.items.rawMaterial'])
            ->find($orderId);
    }

    public function backToPlans()
    {
        $this->activePlanRequestId = null;
        $this->activePlanRequest = null;
    }

    // ── Send daily warehouse request ──────────────────────────────────────

    public function openWarehouseRequestForm($orderId, $materialId, $suggestedQty)
    {
        $order = \App\Models\ProductionOrder::with('plan')->find($orderId);
        $this->warehouseRequestProductionId = $orderId;
        $this->warehouseRequestPlanId = $order->plan->id;
        $this->warehouseRequestMaterialId = $materialId;
        $this->warehouseRequestQty = round($suggestedQty > 0 ? $suggestedQty : 0, 2);
        $this->showWarehouseRequestForm = true;
    }

    public function sendWarehouseRequest()
    {
        $this->validate([
            'warehouseRequestMaterialId' => 'required|exists:raw_materials,id',
            'warehouseRequestQty' => 'required|numeric|min:0.01',
        ]);

        $existing = MaterialRequest::where('production_plan_id', $this->warehouseRequestPlanId)
            ->where('raw_material_id', $this->warehouseRequestMaterialId)
            ->where('status', 'pending')
            ->whereDate('created_at', today())
            ->first();

        if ($existing) {
            $existing->update([
                'quantity' => $this->warehouseRequestQty,
                'notes' => 'Updated by Manager — ' . now()->format('d M Y H:i'),
            ]);
            $msg = 'Warehouse request updated for today.';
        } else {
            MaterialRequest::create([
                'production_plan_id' => $this->warehouseRequestPlanId,
                'raw_material_id' => $this->warehouseRequestMaterialId,
                'quantity' => $this->warehouseRequestQty,
                'status' => 'pending',
                'requested_by' => Auth::id(),
                'notes' => 'Daily release by Manager — ' . now()->format('d M Y'),
            ]);
            $msg = 'Request sent to warehouse!';
        }

        $this->showWarehouseRequestForm = false;
        $this->reset(['warehouseRequestMaterialId', 'warehouseRequestQty', 'warehouseRequestProductionId', 'warehouseRequestPlanId']);
        session()->flash('success', $msg);

        $this->selectPlan($this->activePlanRequestId);
    }

    public function cancelWarehouseRequest()
    {
        $this->showWarehouseRequestForm = false;
        $this->reset(['warehouseRequestMaterialId', 'warehouseRequestQty', 'warehouseRequestProductionId', 'warehouseRequestPlanId']);
    }

    // ── Production start ──────────────────────────────────────────────────

    public function startProduction($orderId)
    {
        $order = \App\Models\ProductionOrder::with('plan')->findOrFail($orderId);

        $hasIssuedMaterials = MaterialRequest::where('production_plan_id', $order->plan->id)
            ->where('status', 'issued')
            ->exists();

        if (!$hasIssuedMaterials) {
            session()->flash('error', 'Cannot start — no materials issued by warehouse yet.');
            return;
        }

        $order->update(['status' => 'in_production']);
        session()->flash('success', 'Production started for Order #' . $order->order_number);
        $this->activeTab = 'production';
        $this->activePlanRequestId = null;
        $this->activePlanRequest = null;
    }

    // ── Production completion ─────────────────────────────────────────────

    public function openCompleteForm($orderId)
    {
        $this->activeRequestId = $orderId;
        $order = \App\Models\ProductionOrder::find($orderId);
        $this->actualProduced = $order->total_quantity;
        $this->showProductionForm = true;
    }

    public function completeProduction()
    {
        $this->validate([
            'actualProduced' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () {
            $order = \App\Models\ProductionOrder::with('plan')->findOrFail($this->activeRequestId);

            $order->update([
                'status' => 'completed',
                'notes' => ($order->notes ? $order->notes . "\n" : '') .
                    'Completed: ' . now()->format('d M Y H:i') .
                    ($this->productionNotes ? ' — ' . $this->productionNotes : ''),
            ]);

            if ($order->plan) {
                MaterialRequest::where('production_plan_id', $order->plan->id)
                    ->where('status', 'issued')
                    ->update(['status' => 'consumed']);
            }
        });

        $this->showProductionForm = false;
        $this->reset(['actualProduced', 'productionNotes', 'activeRequestId']);
        session()->flash('success', 'Production completed!');
    }

    public function cancelProduction()
    {
        $this->showProductionForm = false;
        $this->reset(['actualProduced', 'productionNotes']);
    }
}