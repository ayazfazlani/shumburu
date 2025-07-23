<?php

namespace App\Livewire\Sales;

use App\Models\Payment;
use App\Models\Delivery;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Payments extends Component
{
    use WithPagination, WithFileUploads;

    // CRUD State
    public $showPaymentModal = false;
    public $isPaymentEdit = false;
    public $paymentId = null;
    public $delivery_id = '';
    public $customer_id = '';
    public $amount = '';
    public $payment_method = '';
    public $bank_slip_reference = '';
    public $proforma_invoice_number = '';
    public $payment_date = '';
    public $notes = '';
    public $slip_file = null;
    public $existing_slip_file = null;
    public $showPaymentDeleteModal = false;
    public $deletePaymentId = null;
    public $paymentSearch = '';
    public $paymentPerPage = 10;

    protected function rules()
    {
        return [
            'delivery_id' => 'required|exists:deliveries,id',
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            'bank_slip_reference' => 'nullable|string|max:255',
            'proforma_invoice_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
            'slip_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function mount()
    {
        $this->payment_date = now()->format('Y-m-d');
    }

    public function openPaymentCreateModal()
    {
        $this->resetPaymentForm();
        $this->isPaymentEdit = false;
        $this->showPaymentModal = true;
        $this->payment_date = now()->format('Y-m-d');
    }

    public function openPaymentEditModal($id)
    {
        $payment = Payment::findOrFail($id);
        $this->paymentId = $payment->id;
        $this->delivery_id = $payment->delivery_id;
        $this->customer_id = $payment->customer_id;
        $this->amount = $payment->amount;
        $this->payment_method = $payment->payment_method;
        $this->bank_slip_reference = $payment->bank_slip_reference;
        $this->proforma_invoice_number = $payment->proforma_invoice_number;
        $this->payment_date = $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '';
        $this->notes = $payment->notes;
        $this->existing_slip_file = $payment->slip_file ?? null;
        $this->isPaymentEdit = true;
        $this->showPaymentModal = true;
    }

    public function savePayment()
    {
        $this->validate();
        $user = Auth::user();
        $slipPath = $this->existing_slip_file;
        if ($this->slip_file) {
            $slipPath = $this->slip_file->store('payment_slips', 'public');
        }
        if ($this->isPaymentEdit && $this->paymentId) {
            $payment = Payment::findOrFail($this->paymentId);
            $payment->update([
                'delivery_id' => $this->delivery_id,
                'customer_id' => $this->customer_id,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'bank_slip_reference' => $this->bank_slip_reference,
                'proforma_invoice_number' => $this->proforma_invoice_number,
                'payment_date' => $this->payment_date,
                'recorded_by' => $user ? $user->id : null,
                'notes' => $this->notes,
                'slip_file' => $slipPath,
            ]);
            session()->flash('message', 'Payment updated.');
        } else {
            Payment::create([
                'delivery_id' => $this->delivery_id,
                'customer_id' => $this->customer_id,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'bank_slip_reference' => $this->bank_slip_reference,
                'proforma_invoice_number' => $this->proforma_invoice_number,
                'payment_date' => $this->payment_date,
                'recorded_by' => $user ? $user->id : null,
                'notes' => $this->notes,
                'slip_file' => $slipPath,
            ]);
            session()->flash('message', 'Payment recorded.');
        }
        $this->showPaymentModal = false;
        $this->resetPaymentForm();
    }

    public function confirmPaymentDelete($id)
    {
        $this->deletePaymentId = $id;
        $this->showPaymentDeleteModal = true;
    }

    public function deletePayment()
    {
        $payment = Payment::findOrFail($this->deletePaymentId);
        $payment->delete();
        $this->showPaymentDeleteModal = false;
        $this->deletePaymentId = null;
        session()->flash('message', 'Payment deleted.');
    }

    public function resetPaymentForm()
    {
        $this->paymentId = null;
        $this->delivery_id = '';
        $this->customer_id = '';
        $this->amount = '';
        $this->payment_method = '';
        $this->bank_slip_reference = '';
        $this->proforma_invoice_number = '';
        $this->payment_date = now()->format('Y-m-d');
        $this->notes = '';
        $this->slip_file = null;
        $this->existing_slip_file = null;
    }

    public function render()
    {
        $payments = Payment::with(['delivery', 'customer'])
            ->when($this->paymentSearch, function ($q) {
                $q->where('bank_slip_reference', 'like', "%{$this->paymentSearch}%");
            })
            ->latest()
            ->paginate($this->paymentPerPage);
        $deliveries = Delivery::with(['customer', 'product'])->get();
        $customers = Customer::where('is_active', true)->get();
        return view('livewire.sales.payments', [
            'payments' => $payments,
            'deliveries' => $deliveries,
            'customers' => $customers,
        ]);
    }
}
