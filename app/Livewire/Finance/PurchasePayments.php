<?php

namespace App\Livewire\Finance;

use App\Models\PurchaseRequest;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

class PurchasePayments extends Component
{
    use WithPagination;

    public function mount()
    {
        abort_unless(auth()->user()->can('finance.purchase-payments'), 403);
    }

    #[Layout('components.layouts.app')]

    public $search = '';
    public $filterSupplier = '';

    // New payment modal
    public $showModal = false;
    public $selectedPrId = '';
    public $pay_amount = '';
    public $pay_method = 'bank_transfer';
    public $pay_reference = '';
    public $pay_date = '';
    public $pay_notes = '';

    public function render()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        // Received POs that can have payments recorded
        $receivedPOs = PurchaseRequest::with(['rawMaterial', 'supplier', 'purchasePayments'])
            ->where('status', 'received')
            ->when($this->filterSupplier, fn($q) => $q->where('supplier_id', $this->filterSupplier))
            ->when($this->search, fn($q) => $q->whereHas('rawMaterial', fn($q2) =>
                $q2->where('name', 'like', "%{$this->search}%")
            )->orWhere('po_number', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        // Payment history
        $payments = PurchasePayment::with(['purchaseRequest.rawMaterial', 'supplier', 'recordedBy'])
            ->when($this->filterSupplier, fn($q) => $q->where('supplier_id', $this->filterSupplier))
            ->latest()
            ->take(30)
            ->get();

        $stats = [
            'total_paid'        => PurchasePayment::sum('amount'),
            'total_outstanding' => PurchaseRequest::where('status', 'received')
                ->with('purchasePayments')
                ->get()
                ->sum(fn($pr) => $pr->balance_due),
            'payment_count'     => PurchasePayment::count(),
        ];

        return view('livewire.finance.purchase-payments', compact('suppliers', 'receivedPOs', 'payments', 'stats'));
    }

    public function openPaymentModal($prId)
    {
        $pr = PurchaseRequest::findOrFail($prId);
        $this->selectedPrId  = $prId;
        $this->pay_amount    = number_format($pr->balance_due, 2, '.', '');
        $this->pay_method    = 'bank_transfer';
        $this->pay_reference = '';
        $this->pay_date      = now()->format('Y-m-d');
        $this->pay_notes     = '';
        $this->showModal     = true;
    }

    public function recordPayment()
    {
        $this->validate([
            'pay_amount'    => 'required|numeric|min:0.01',
            'pay_method'    => 'required|string',
            'pay_date'      => 'required|date',
        ]);

        $pr = PurchaseRequest::findOrFail($this->selectedPrId);

        PurchasePayment::create([
            'purchase_request_id' => $pr->id,
            'supplier_id'         => $pr->supplier_id,
            'amount'              => $this->pay_amount,
            'payment_method'      => $this->pay_method,
            'reference_number'    => $this->pay_reference,
            'payment_date'        => $this->pay_date,
            'recorded_by'         => Auth::id(),
            'notes'               => $this->pay_notes,
        ]);

        $this->showModal = false;
        session()->flash('success', 'Payment recorded for PO #' . $pr->po_number);
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterSupplier() { $this->resetPage(); }
}
