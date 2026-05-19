<?php

namespace App\Livewire\Warehouse;

use App\Models\FinishedGood;
use App\Models\FgStock;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class PendingReceipts extends Component
{
    use WithPagination;

    public $showConfirmModal = false;
    public $selectedReceiptId;
    public $production_quantity;
    public $received_quantity;
    public $receipt_notes;
    public $batch_number;
    public $product_name;

    #[Layout('components.layouts.app')]
    public function render()
    {
        $pendingReceipts = FinishedGood::with(['product', 'producedBy'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('livewire.warehouse.pending-receipts', [
            'receipts' => $pendingReceipts,
        ]);
    }

    public function openConfirmModal($id)
    {
        $receipt = FinishedGood::with('product')->findOrFail($id);
        $this->selectedReceiptId = $id;
        $this->product_name = $receipt->product->name;
        $this->batch_number = $receipt->batch_number;
        $this->production_quantity = $receipt->quantity;
        $this->received_quantity = $receipt->quantity; // Default to production quantity
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
            
            // 1. Update the production log
            $receipt->update([
                'status' => 'received',
                'received_quantity' => $this->received_quantity,
                'receipt_notes' => $this->receipt_notes,
            ]);

            // 2. Increment physical stock
            $stock = FgStock::firstOrNew([
                'product_id' => $receipt->product_id,
                'batch_number' => $receipt->batch_number,
            ]);
            
            $stock->quantity += $this->received_quantity;
            $stock->status = 'available';
            $stock->save();
        });

        $this->showConfirmModal = false;
        session()->flash('success', 'Stock receipt confirmed and inventory updated!');
    }
}
