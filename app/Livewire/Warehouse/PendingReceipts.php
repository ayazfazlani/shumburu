<?php

namespace App\Livewire\Warehouse;

use App\Models\FinishedGood;
use App\Models\FgStock;
use App\Models\PurchaseRequest;
use App\Models\MaterialStockIn;
use App\Models\RawMaterial;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PendingReceipts extends Component
{
    use WithPagination;

    #[Url]
    public $tab = 'fg'; // default to fg, but can be 'rm' from URL

    public $showConfirmModal = false;
    public $selectedReceiptId;
    public $production_quantity;
    public $received_quantity;
    public $receipt_notes;
    public $batch_number;
    public $product_name;
    public $is_qc_passed = false;

    // For RM
    public $showRmModal = false;
    public $selectedPrId;
    public $rm_name;
    public $rm_po_number;
    public $rm_expected_qty;
    public $rm_received_qty;
    public $rm_notes;

    #[Layout('components.layouts.app')]
    public function render()
    {
        $fgReceipts = FinishedGood::with(['product', 'producedBy'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15, ['*'], 'fgPage');

        // Update: Show "po_issued" (Active POs) and "delivered" (At the gate)
        $rmReceipts = PurchaseRequest::with(['rawMaterial', 'supplier'])
            ->whereIn('status', ['po_issued', 'delivered'])
            ->latest()
            ->paginate(15, ['*'], 'rmPage');

        return view('livewire.warehouse.pending-receipts', [
            'receipts' => $fgReceipts,
            'rmReceipts' => $rmReceipts,
            'activeTab' => $this->tab,
        ]);
    }

    public function setTab($tab)
    {
        $this->tab = $tab;
        $this->resetPage('fgPage');
        $this->resetPage('rmPage');
    }

    // ─── FG LOGIC ───
    public function openConfirmModal($id)
    {
        $receipt = FinishedGood::with('product')->findOrFail($id);
        $this->selectedReceiptId = $id;
        $this->product_name = $receipt->product->name;
        $this->batch_number = $receipt->batch_number;
        $this->production_quantity = $receipt->quantity;
        $this->received_quantity = $receipt->quantity;
        $this->receipt_notes = '';
        $this->showConfirmModal = true;
    }

    public function confirmReceipt()
    {
        $this->validate([
            'received_quantity' => 'required|numeric|min:0',
            'receipt_notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () {
            $receipt = FinishedGood::findOrFail($this->selectedReceiptId);
            
            $receipt->update([
                'status' => 'received',
                'received_quantity' => $this->received_quantity,
                'receipt_notes' => $this->receipt_notes,
            ]);

            $stock = FgStock::firstOrNew([
                'product_id' => $receipt->product_id,
                'batch_number' => $receipt->batch_number,
            ]);
            
            $stock->quantity += (float)$this->received_quantity;
            $stock->status = 'available';
            $stock->is_qc_passed = $this->is_qc_passed;
            $stock->save();
        });

        $this->showConfirmModal = false;
        session()->flash('success', 'FG stock receipt confirmed!');
    }

    // ─── RM LOGIC ───
    public function openRmModal($id)
    {
        $pr = PurchaseRequest::with('rawMaterial')->findOrFail($id);
        
        if ($pr->status !== 'delivered') {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => "Finance must mark this PO as 'Delivered' (gate arrived) before you can confirm the GRN."
            ]);
            return;
        }

        $this->selectedPrId = $id;
        $this->rm_name = $pr->rawMaterial->name;
        $this->rm_po_number = $pr->po_number;
        $this->rm_expected_qty = $pr->quantity;
        $this->rm_received_qty = $pr->quantity;
        $this->rm_notes = '';
        $this->showRmModal = true;
    }

    public function confirmRmReceipt()
    {
        $this->validate([
            'rm_received_qty' => 'required|numeric|min:0.001',
            'rm_notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () {
            $pr = PurchaseRequest::findOrFail($this->selectedPrId);
            
            // 1. Create the official StockIn record
            MaterialStockIn::create([
                'raw_material_id' => $pr->raw_material_id,
                'purchase_request_id' => $pr->id,
                'quantity' => (float)$this->rm_received_qty,
                'batch_number' => $pr->po_number ?? 'PO-DELIVERY',
                'received_date' => now(),
                'received_by' => Auth::id(),
                'notes' => "Confirmed via GRN from PO #{$pr->po_number}. " . $this->rm_notes,
            ]);

            // 2. Increment raw material stock
            RawMaterial::$skipAutoTransaction = true;
            $material = RawMaterial::lockForUpdate()->find($pr->raw_material_id);
            $material->increment('quantity', (float)$this->rm_received_qty);
            RawMaterial::$skipAutoTransaction = false;

            // 3. Update Purchase Request status
            $pr->update([
                'status' => 'received',
                'received_at' => now(),
            ]);
        });

        $this->showRmModal = false;
        session()->flash('success', 'Raw Material GRN confirmed and stock updated!');
    }
}
