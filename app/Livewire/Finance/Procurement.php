<?php

namespace App\Livewire\Finance;

use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\MaterialStockIn;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Procurement extends Component
{
    use WithPagination, WithFileUploads;

    #[Layout('components.layouts.app')]

    public $activeTab = 'pending'; // pending | active_pos | pending_grn | history

    // Modal: Approve PR
    public $showApproveModal = false;
    public $approvingId = null;

    // Modal: Issue PO (RFQ → PO)
    public $showPoModal = false;
    public $poRequestId = null;
    public $po_supplier_id = '';
    public $po_number = '';
    public $po_unit_price = '';
    public $po_expected_date = '';

    // Modal: Record Delivery (mark delivered)
    public $showDeliverModal = false;
    public $deliverRequestId = null;

    // Modal: Purchase Payment
    public $showPaymentModal = false;
    public $paymentRequestId = null;
    public $pay_amount = '';
    public $pay_method = 'bank_transfer';
    public $pay_reference = '';
    public $pay_date = '';
    public $pay_notes = '';
    public $pay_receipt;

    // Modal: RFQ Preview
    public $showRfqModal = false;
    public $viewingRfqId = null;

    public function render()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        $pendingPRs = PurchaseRequest::with(['rawMaterial', 'requestedBy', 'productionRequest'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $activePos = PurchaseRequest::with(['rawMaterial', 'supplier', 'approvedBy'])
            ->whereIn('status', ['approved', 'po_issued'])
            ->latest()
            ->get();

        $pendingGrns = PurchaseRequest::with(['rawMaterial', 'supplier'])
            ->where('status', 'delivered')
            ->latest()
            ->get();

        $history = PurchaseRequest::with(['rawMaterial', 'supplier', 'purchasePayments'])
            ->where('status', 'received')
            ->latest()
            ->take(50)
            ->get();

        $stats = [
            'pending_count'  => $pendingPRs->count(),
            'active_pos'     => $activePos->count(),
            'pending_grn'    => $pendingGrns->count(),
            'total_ap'       => PurchaseRequest::where('status', 'received')
                ->with('purchasePayments')
                ->get()
                ->sum(fn($pr) => $pr->balance_due),
        ];

        return view('livewire.finance.procurement', compact(
            'suppliers', 'pendingPRs', 'activePos', 'pendingGrns', 'history', 'stats'
        ));
    }

    // ─── Approve PR ──────────────────────────────────────────
    public function openApproveModal($id)
    {
        $this->approvingId = $id;
        $this->showApproveModal = true;
    }

    public function approvePR()
    {
        $pr = PurchaseRequest::findOrFail($this->approvingId);
        $pr->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        $this->showApproveModal = false;
        $this->approvingId = null;
        session()->flash('success', 'Purchase Request approved successfully.');
    }

    // ─── Issue PO ────────────────────────────────────────────
    public function openPoModal($id)
    {
        $this->poRequestId = $id;
        $this->po_supplier_id = '';
        $this->po_number = 'PO-' . strtoupper(uniqid());
        $this->po_unit_price = '';
        $this->po_expected_date = now()->addDays(7)->format('Y-m-d');
        $this->showPoModal = true;
    }

    public function issuePO()
    {
        $this->validate([
            'po_supplier_id'   => 'required|exists:suppliers,id',
            'po_number'        => 'required|string|max:100|unique:purchase_requests,po_number,' . $this->poRequestId,
            'po_unit_price'    => 'required|numeric|min:0.0001',
            'po_expected_date' => 'required|date|after_or_equal:today',
        ]);

        PurchaseRequest::findOrFail($this->poRequestId)->update([
            'status'               => 'po_issued',
            'supplier_id'          => $this->po_supplier_id,
            'po_number'            => $this->po_number,
            'unit_price'           => $this->po_unit_price,
            'expected_delivery_date' => $this->po_expected_date,
            'po_issued_at'         => now(),
        ]);

        $this->showPoModal = false;
        session()->flash('success', 'Purchase Order issued. Ready for printable RFQ.');
    }

    // ─── Mark Delivered (supplier delivered to gate) ─────────
    public function openDeliverModal($id)
    {
        $this->deliverRequestId = $id;
        $this->showDeliverModal = true;
    }

    public function markDelivered()
    {
        PurchaseRequest::findOrFail($this->deliverRequestId)->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);
        $this->showDeliverModal = false;
        $this->deliverRequestId = null;
        session()->flash('success', 'PO marked as delivered. Warehouse can now confirm GRN.');
    }

    // ─── Record Payment ───────────────────────────────────────
    public function openPaymentModal($id)
    {
        $this->paymentRequestId = $id;
        $pr = PurchaseRequest::findOrFail($id);
        $this->pay_amount    = $pr->balance_due;
        $this->pay_method    = 'bank_transfer';
        $this->pay_reference = '';
        $this->pay_date      = now()->format('Y-m-d');
        $this->pay_notes     = '';
        $this->pay_receipt   = null;
        $this->showPaymentModal = true;
    }

    public function recordPayment()
    {
        $this->validate([
            'pay_amount'    => 'required|numeric|min:0.01',
            'pay_method'    => 'required|string',
            'pay_date'      => 'required|date',
            'pay_receipt'   => 'nullable|image|max:2048', // max 2MB
        ]);

        $pr = PurchaseRequest::findOrFail($this->paymentRequestId);

        $receiptPath = null;
        if ($this->pay_receipt) {
            $receiptPath = $this->pay_receipt->store('receipts', 'public');
        }

        \App\Models\PurchasePayment::create([
            'purchase_request_id' => $pr->id,
            'supplier_id'         => $pr->supplier_id,
            'amount'              => $this->pay_amount,
            'payment_method'      => $this->pay_method,
            'reference_number'    => $this->pay_reference,
            'payment_date'        => $this->pay_date,
            'recorded_by'         => Auth::id(),
            'notes'               => $this->pay_notes,
            'receipt_path'        => $receiptPath,
        ]);

        $this->showPaymentModal = false;
        session()->flash('success', 'Payment recorded against PO #' . $pr->po_number);
    }

    public function openRfqModal($id)
    {
        $this->viewingRfqId = $id;
        $this->showRfqModal = true;
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }
}
