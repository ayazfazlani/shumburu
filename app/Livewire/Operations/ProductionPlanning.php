<?php

namespace App\Livewire\Operations;

use App\Models\ProductionRequest;
use App\Models\PurchaseRequest;
use App\Models\RawMaterial;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class ProductionPlanning extends Component
{
    use WithPagination;

    public $showPurchaseModal = false;
    public $selectedOrderId;
    public $selectedRequestId;
    public $raw_material_id;
    public $purchase_quantity;
    public $purchase_notes;

    #[Layout('components.layouts.app')]
    public function render()
    {
        // 1. Get orders that have pending production demands
        $ordersWithDemands = \App\Models\ProductionOrder::whereHas('orderItems.productionRequests', function($q) {
                $q->whereIn('status', ['pending', 'approved', 'scheduled']);
            })
            ->with(['customer'])
            ->withCount(['orderItems as pending_requests_count' => function($q) {
                $q->whereHas('productionRequests', function($sq) {
                    $sq->whereIn('status', ['pending', 'approved', 'scheduled']);
                });
            }])
            ->latest()
            ->get();

        // 2. Get direct stock replenishment demands (no order attached)
        $replenishmentRequests = ProductionRequest::with(['product', 'requestedBy'])
            ->whereNull('order_item_id')
            ->whereIn('status', ['pending', 'approved', 'scheduled'])
            ->latest()
            ->get();

        // 3. If an order is selected, get its demands
        $selectedOrderDemands = [];
        $viewingOrder = null;
        if ($this->selectedOrderId) {
            $viewingOrder = \App\Models\ProductionOrder::with('customer')->find($this->selectedOrderId);
            $selectedOrderDemands = ProductionRequest::with(['product', 'orderItem', 'requestedBy'])
                ->whereHas('orderItem', function($q) {
                    $q->where('production_order_id', $this->selectedOrderId);
                })
                ->whereIn('status', ['pending', 'approved', 'scheduled'])
                ->get();
        }

        $rawMaterials = RawMaterial::orderBy('name')->get();

        return view('livewire.operations.production-planning', [
            'ordersWithDemands' => $ordersWithDemands,
            'replenishmentRequests' => $replenishmentRequests,
            'selectedOrderDemands' => $selectedOrderDemands,
            'viewingOrder' => $viewingOrder,
            'rawMaterials' => $rawMaterials,
        ]);
    }

    public function selectOrder($orderId)
    {
        $this->selectedOrderId = $orderId;
    }

    public function backToList()
    {
        $this->selectedOrderId = null;
    }

    public function openPurchaseModal($requestId = null)
    {
        $this->selectedRequestId = $requestId;
        $this->raw_material_id = '';
        $this->purchase_quantity = '';
        $this->purchase_notes = $requestId ? "Triggered for Production Request #$requestId" : '';
        $this->showPurchaseModal = true;
    }

    public function raisePurchaseRequest()
    {
        $this->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'purchase_quantity' => 'required|numeric|min:0.01',
            'purchase_notes' => 'nullable|string',
        ]);

        PurchaseRequest::create([
            'raw_material_id' => $this->raw_material_id,
            'production_request_id' => $this->selectedRequestId,
            'quantity' => $this->purchase_quantity,
            'status' => 'pending',
            'requested_by' => Auth::id(),
            'notes' => $this->purchase_notes,
        ]);

        $this->showPurchaseModal = false;
        session()->flash('success', 'Purchase Requisition sent to Finance/Procurement!');
    }

    public function updateStatus($requestId, $status)
    {
        $request = ProductionRequest::findOrFail($requestId);
        $request->update(['status' => $status]);
        session()->flash('success', "Request #$requestId marked as $status.");
    }
}
