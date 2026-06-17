<?php

namespace App\Livewire\Warehouse;

use App\Models\MaterialRequest;
use App\Models\RawMaterial;
use App\Models\PurchaseRequest;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemandAggregation extends Component
{
    public $selectedMaterialId;
    public $bulkQuantity;
    public $bulkNotes;
    public $showPrModal = false;

    public function mount()
    {
        abort_unless(auth()->user()->can('warehouse.demand-aggregation'), 403);
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        // 1. Get all materials that have "pending" fulfillments from Planning
        $demands = MaterialRequest::with('rawMaterial')
            ->where('status', 'pending')
            ->get()
            ->groupBy('raw_material_id')
            ->map(function ($group) {
                $material = $group->first()->rawMaterial;
                $totalRequired = $group->sum('quantity');
                $inStock = $material->quantity ?? 0;
                $shortage = max(0, $totalRequired - $inStock);

                return [
                    'material_id' => $material->id,
                    'name' => $material->name,
                    'unit' => $material->unit,
                    'in_stock' => $inStock,
                    'total_required' => $totalRequired,
                    'shortage' => $shortage,
                    'order_count' => $group->unique('production_request_id')->count(),
                    'request_ids' => $group->pluck('id')->toArray(),
                ];
            })
            ->filter(function($item) {
                return $item['shortage'] > 0; // Only show what we actually need to buy
            });

        return view('livewire.warehouse.demand-aggregation', [
            'aggregatedDemands' => $demands
        ]);
    }

    public function openPrModal($materialId, $shortage)
    {
        $material = RawMaterial::findOrFail($materialId);
        $this->selectedMaterialId = $materialId;
        $this->bulkQuantity = $shortage;
        $this->bulkNotes = "Bulk Aggregation Request for pending production demands.";
        $this->showPrModal = true;
    }

    public function raiseBulkPR()
    {
        $this->validate([
            'bulkQuantity' => 'required|numeric|min:0.01',
            'bulkNotes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function() {
            // 1. Create the Purchase Request in Finance
            PurchaseRequest::create([
                'raw_material_id' => $this->selectedMaterialId,
                'quantity' => $this->bulkQuantity,
                'status' => 'pending',
                'requested_by' => Auth::id(),
                'notes' => $this->bulkNotes,
            ]);

            // 2. Mark all individual material requests as "purchase_raised" 
            // so they don't show up in the aggregation list again
            MaterialRequest::where('raw_material_id', $this->selectedMaterialId)
                ->where('status', 'pending')
                ->update(['status' => 'purchase_raised']);
        });

        $this->showPrModal = false;
        session()->flash('success', 'Bulk PR successfully sent to Finance for processing.');
    }
}
