<?php

namespace App\Livewire\Sales;

use App\Models\Payment;
use Livewire\Component;
use App\Models\Customer;
use App\Models\Delivery;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\ProductionOrder;
use Illuminate\Support\Facades\Auth;

class Payments extends Component
{
    use WithPagination, WithFileUploads;

    // CRUD State
    public $showPaymentModal = false;
    public $isPaymentEdit = false;
    public $paymentId = null;
    public $order_id = '';
    public $customer_id = '';
    public $amount = '';
    public $payment_method = '';
    public $bank_slip_reference ;
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
            'order_id' => 'required|exists:production_orders,id',
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:255',
            // 'bank_slip_reference' => 'nullable|string|max:255',
            'proforma_invoice_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
            'bank_slip_reference' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
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
        $this->order_id = $payment->production_order_id;
        $this->customer_id = $payment->customer_id;
        $this->amount = $payment->amount;
        $this->payment_method = $payment->payment_method;
        $this->bank_slip_reference = $payment->bank_slip_reference;
        $this->proforma_invoice_number = $payment->proforma_invoice_number;
        $this->payment_date = $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '';
        $this->notes = $payment->notes;
        $this->existing_slip_file = $payment->bank_slip_reference ?? null;
        $this->isPaymentEdit = true;
        $this->showPaymentModal = true;
    }

    public function savePayment()
    {
        $this->validate();
        $user = Auth::user();
        $slipPath = $this->existing_slip_file;
        if ($this->bank_slip_reference) {
            $slipPath = $this->bank_slip_reference->store('payment_slips', 'public');
        }
        if ($this->isPaymentEdit && $this->paymentId) {
            $payment = Payment::findOrFail($this->paymentId);
            $payment->update([
                'production_order_id' => $this->order_id,
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
                'production_order_id' => $this->order_id,
                'customer_id' => $this->customer_id,
                'amount' => $this->amount,
                'payment_method' => $this->payment_method,
                'bank_slip_reference' => $slipPath,
                'proforma_invoice_number' => $this->proforma_invoice_number,
                'payment_date' => $this->payment_date,
                'recorded_by' => $user ? $user->id : null,
                'notes' => $this->notes,
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
        $this->order_id = '';
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
        $payments = Payment::with([
            'productionOrder', 
            'customer'])
            ->when($this->paymentSearch, function ($q) {
                $q->where('bank_slip_reference', 'like', "%{$this->paymentSearch}%");
            })
            ->latest()
            ->paginate($this->paymentPerPage);
        $orders = ProductionOrder::with(['customer'])->get();
        $customers = Customer::where('is_active', true)->get();
        return view('livewire.sales.payments', [
            'payments' => $payments,
            'orders' => $orders,
            'customers' => $customers,
        ]);
    }
}
