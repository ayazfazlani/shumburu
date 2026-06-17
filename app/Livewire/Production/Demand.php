<?php

namespace App\Livewire\Production;

use App\Models\ProductionRequest;
use App\Models\StockDemand;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class Demand extends Component
{
    use WithPagination;

    public $selectedOrderId;
    public $activeTab = 'fg';

    public function mount()
    {
        abort_unless(auth()->user()->can('operations.demand-control'), 403);
    }

    public function backToList()
    {
        $this->selectedOrderId = null;
    }

    public function selectOrder($orderId)
    {
        $this->selectedOrderId = $orderId;
    }

    public function authorizeProduction($demandId)
    {
        DB::transaction(function () use ($demandId) {
            $demand = StockDemand::findOrFail($demandId);

            ProductionRequest::create([
                'product_id' => $demand->product_id,
                'order_item_id' => $demand->order_item_id,
                'quantity' => $demand->quantity,
                'status' => 'pending',
                'priority' => 'medium',
                'requested_by' => Auth::id(),
                'notes' => 'Authorized by Production based on Sales Shortfall.',
            ]);

            $demand->update(['status' => 'raised']);
        });

        session()->flash('success', 'Production Request raised successfully!');
        $this->selectedOrderId = null; // Reset after authorization
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        // Get unique orders that have pending stock demands (FG)
        $ordersWithStockDemands = \App\Models\ProductionOrder::whereHas('orderItems.stockDemands', function ($q) {
            $q->where('status', 'pending');
        })->with(['customer'])->get();

        $viewingOrder = null;
        $selectedOrderFgDemands = [];

        if ($this->selectedOrderId) {
            $viewingOrder = \App\Models\ProductionOrder::with('customer')->find($this->selectedOrderId);
            $selectedOrderFgDemands = StockDemand::with(['product', 'requestedBy'])
                ->where('status', 'pending')
                ->whereHas('orderItem', function ($q) {
                    $q->where('production_order_id', $this->selectedOrderId);
                })
                ->get();
        }

        // Stock Demands not linked to an order (Manual/Individual FG Demands)
        $individualFgDemands = StockDemand::with(['product', 'requestedBy'])
            ->where('status', 'pending')
            ->whereNull('order_item_id')
            ->latest()
            ->get();

        return view('livewire.production.demand', [
            'ordersWithStockDemands' => $ordersWithStockDemands,
            'viewingOrder' => $viewingOrder,
            'selectedOrderFgDemands' => $selectedOrderFgDemands,
            'individualFgDemands' => $individualFgDemands,
        ]);
    }
}