<?php

namespace App\Livewire\Warehouse;

use App\Models\PurchaseRequest;
use App\Models\RawMaterial;
use App\Models\MaterialRequest;
use App\Models\MaterialStockOut; // Correct model name
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DemandControl extends Component
{
    use WithPagination;

    public $activeTab = 'rm';

    #[Layout('components.layouts.app')]
    public function render()
    {
        $rmDemands = PurchaseRequest::with(['rawMaterial', 'requestedBy'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $rmRequests = MaterialRequest::with(['rawMaterial', 'requestedBy', 'productionRequest.product'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('livewire.warehouse.demand-control', [
            'rmDemands' => $rmDemands,
            'rmRequests' => $rmRequests,
        ]);
    }

    public function authorizePurchase($requestId)
    {
        $request = PurchaseRequest::findOrFail($requestId);
        $request->update(['status' => 'approved']);

        session()->flash('success', 'Purchase Requisition approved for Finance.');
    }

    public function stockOutMaterial($requestId)
    {
        DB::transaction(function () use ($requestId) {
            $request = MaterialRequest::lockForUpdate()->findOrFail($requestId);
            $material = RawMaterial::lockForUpdate()->findOrFail($request->raw_material_id);

            if ($material->quantity < $request->quantity) {
                throw new \Exception("Insufficient stock for {$material->name}. Have: {$material->quantity}, Need: {$request->quantity}");
            }

            // Create Material Stock Out record
            $stockOut = MaterialStockOut::create([
                'raw_material_id' => $material->id,
                'quantity' => $request->quantity,
                'batch_number' => 'MR-' . $requestId . '-' . now()->format('YmdHis'),
                'issued_date' => now(),
                'issued_by' => Auth::id(),
                'status' => 'material_on_process',
                'notes' => "Issued from Material Request #$requestId | Plan #{$request->production_request_id}",
            ]);

            // Update material request status
            $request->update(['status' => 'issued']);

            // Note: The stock quantity decrement and transaction logging 
            // will be handled automatically by the MaterialStockOut model's booted method
        });

        session()->flash('success', 'Material issued successfully! Stock Out record created.');
    }

    public function forwardToProcurement($requestId)
    {
        DB::transaction(function () use ($requestId) {
            $request = MaterialRequest::findOrFail($requestId);

            PurchaseRequest::create([
                'raw_material_id' => $request->raw_material_id,
                'production_request_id' => $request->production_request_id,
                'quantity' => $request->quantity,
                'status' => 'pending',
                'requested_by' => Auth::id(),
                'notes' => "Auto-forwarded from Warehouse due to shortage. Ref: Material Request #$requestId",
            ]);

            $request->update(['status' => 'purchase_raised']);
        });

        session()->flash('success', 'Purchase Requisition sent to Procurement!');
    }
}