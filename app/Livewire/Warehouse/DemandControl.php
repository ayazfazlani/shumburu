<?php

namespace App\Livewire\Warehouse;

use App\Models\StockDemand;
use App\Models\ProductionRequest;
use App\Models\PurchaseRequest;
use App\Models\RawMaterial;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemandControl extends Component
{
    use WithPagination;

    // Filter states
    public $activeTab = 'fg'; // fg (Finished Goods), rm (Raw Materials)
    public $selectedOrderId;

    #[Layout('components.layouts.app')]
    public function render()
    {
        // 1. Get unique orders that have pending stock demands
        $ordersWithStockDemands = \App\Models\ProductionOrder::whereHas('orderItems.stockDemands', function($q) {
            $q->where('status', 'pending');
        })->with(['customer'])->get();

        // Stock Demands not linked to an order
        $individualFgDemands = StockDemand::with(['product', 'requestedBy'])
            ->where('status', 'pending')
            ->whereNull('order_item_id')
            ->latest()
            ->get();

        // Demands for selected order
        $selectedOrderFgDemands = [];
        $viewingOrder = null;
        if ($this->selectedOrderId) {
            $viewingOrder = \App\Models\ProductionOrder::with('customer')->find($this->selectedOrderId);
            $selectedOrderFgDemands = StockDemand::with(['product', 'requestedBy'])
                ->where('status', 'pending')
                ->whereHas('orderItem', function($q) {
                    $q->where('production_order_id', $this->selectedOrderId);
                })
                ->get();
        }

        $rmDemands = PurchaseRequest::with(['rawMaterial', 'requestedBy'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('livewire.warehouse.demand-control', [
            'ordersWithStockDemands' => $ordersWithStockDemands,
            'individualFgDemands' => $individualFgDemands,
            'selectedOrderFgDemands' => $selectedOrderFgDemands,
            'viewingOrder' => $viewingOrder,
            'rmDemands' => $rmDemands,
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

    public function authorizeProduction($demandId)
    {
        DB::transaction(function() use ($demandId) {
            $demand = StockDemand::findOrFail($demandId);
            
            // Raise to Planning (Production)
            ProductionRequest::create([
                'product_id' => $demand->product_id,
                'order_item_id' => $demand->order_item_id,
                'quantity' => $demand->quantity,
                'status' => 'pending',
                'priority' => 'medium',
                'requested_by' => Auth::id(), // Authorized by Warehouse
                'notes' => 'Authorized by Warehouse based on Sales Shortfall.',
            ]);

            $demand->update(['status' => 'raised']);
        });

        session()->flash('success', 'Production Request raised successfully!');
    }

    public function authorizePurchase($requestId)
    {
        $request = PurchaseRequest::findOrFail($requestId);
        $request->update(['status' => 'approved']); // Now finance can see it
        
        session()->flash('success', 'Purchase Requisition approved for Finance.');
    }
}
