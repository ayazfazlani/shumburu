<?php

namespace App\Livewire\Warehouse;

use App\Models\MaterialRequest;
use App\Models\RawMaterial;
use App\Models\PurchaseRequest;
use App\Models\StockTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialIssueRequests extends Component
{
    use WithPagination;

    public $selectedOrderNumber = null;
    public $activeTab = 'order-wise'; // order-wise | aggregation

    public function mount()
    {
        abort_unless(auth()->user()->can('warehouse.material-issue-requests'), 403);
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function selectOrder($orderNumber)
    {
        $this->selectedOrderNumber = $orderNumber;
    }

    public function backToList()
    {
        $this->selectedOrderNumber = null;
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        // ─── 1. Order-Wise Logic ───
        $requests = MaterialRequest::with([
                'rawMaterial', 
                'requestedBy', 
                'productionRequest.product', 
                'productionRequest.orderItem.productionOrder.customer'
            ])
            ->whereIn('status', ['pending', 'purchase_raised'])
            ->latest()
            ->get()
            ->groupBy(function($item) {
                return $item->productionRequest->orderItem->productionOrder->order_number ?? 'Manual Planning';
            })
            ->map(function($orderGroup) {
                return $orderGroup->groupBy('production_request_id');
            });

        // ─── 2. Aggregation Logic ───
        $aggregatedDemands = [];
        if ($this->activeTab === 'aggregation') {
            $aggregatedDemands = MaterialRequest::with('rawMaterial')
                ->where('status', 'pending')
                ->get()
                ->groupBy('raw_material_id')
                ->map(function ($group) {
                    $material = $group->first()->rawMaterial;
                    $totalRequired = $group->sum('quantity');
                    $available = $material->quantity ?? 0;
                    $shortage = max(0, $totalRequired - $available);

                    return [
                        'material_id' => $material->id,
                        'name' => $material->name,
                        'unit' => $material->unit,
                        'available' => $available,
                        'total_required' => $totalRequired,
                        'shortage' => $shortage,
                        'request_ids' => $group->pluck('id')->toArray(),
                    ];
                });
        }

        return view('livewire.warehouse.material-issue-requests', [
            'orderWiseRequests' => $requests,
            'aggregatedDemands' => $aggregatedDemands
        ]);
    }

    public function issueStock($requestId)
    {
        DB::transaction(function() use ($requestId) {
            $request = MaterialRequest::lockForUpdate()->findOrFail($requestId);

            // Double Injection Prevention
            if ($request->status === 'issued') {
                $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => "This material has already been issued for this order."
                ]);
                return;
            }

            $material = RawMaterial::lockForUpdate()->findOrFail($request->raw_material_id);
            
            if ($material->quantity < $request->quantity) {
                $this->dispatch('alert', [
                    'type' => 'error',
                    'message' => "Insufficient stock for {$material->name}. Please raise a PR instead."
                ]);
                return;
            }

            // Decrement Stock
            RawMaterial::$skipAutoTransaction = true;
            $material->decrement('quantity', $request->quantity);
            RawMaterial::$skipAutoTransaction = false;

            $orderNumber = $request->productionRequest->orderItem->productionOrder->order_number ?? 'N/A';
            
            // Log Stock Out
            \App\Models\MaterialStockOut::create([
                'raw_material_id' => $material->id,
                'quantity' => $request->quantity,
                'batch_number' => $orderNumber,
                'issued_date' => now(),
                'issued_by' => Auth::id(),
                'status' => 'completed',
                'notes' => "Official Release for Order #{$orderNumber} | Plan #{$request->production_request_id}",
            ]);

            $request->update(['status' => 'issued']);
        });

        session()->flash('success', 'Material issued successfully.');
    }

    public function raiseBulkPurchase($materialId, $quantity, $requestIds)
    {
        DB::transaction(function() use ($materialId, $quantity, $requestIds) {
            $material = RawMaterial::findOrFail($materialId);
            
            // Create one bulk PR
            PurchaseRequest::create([
                'raw_material_id' => $materialId,
                'quantity' => $quantity,
                'status' => 'pending',
                'requested_by' => Auth::id(),
                'notes' => "Bulk Aggregated PR for " . count($requestIds) . " pending order demands. Total shortfall: {$quantity} {$material->unit}.",
            ]);

            // Update all linked Planning requests so they aren't processed individually
            MaterialRequest::whereIn('id', $requestIds)->update(['status' => 'purchase_raised']);
        });

        session()->flash('success', 'Bulk Purchase Requisition sent for aggregation.');
    }
}
