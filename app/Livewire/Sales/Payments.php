<?php

namespace App\Livewire\Sales;

use App\Models\Payment;
use App\Models\Delivery;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Payments extends Component
{
  public $delivery_id;
  public $customer_id;
  public $amount;
  public $payment_method;
  public $bank_slip_reference;
  public $proforma_invoice_number;
  public $payment_date;
  public $notes;

  protected $rules = [
    'delivery_id' => 'required|exists:deliveries,id',
    'customer_id' => 'required|exists:customers,id',
    'amount' => 'required|numeric|min:0',
    'payment_method' => 'nullable|string|max:255',
    'bank_slip_reference' => 'nullable|string|max:255',
    'proforma_invoice_number' => 'nullable|string|max:255',
    'payment_date' => 'required|date',
    'notes' => 'nullable|string',
  ];

  public function mount()
  {
    $this->payment_date = now()->format('Y-m-d');
  }

  public function save()
  {
    $this->validate();

    $user = Auth::user();

    if (!$user->hasRole(['admin', 'sales'])) {
      session()->flash('error', 'Unauthorized to record payments.');
      return;
    }

    Payment::create([
      'delivery_id' => $this->delivery_id,
      'customer_id' => $this->customer_id,
      'amount' => $this->amount,
      'payment_method' => $this->payment_method,
      'bank_slip_reference' => $this->bank_slip_reference,
      'proforma_invoice_number' => $this->proforma_invoice_number,
      'payment_date' => $this->payment_date,
      'recorded_by' => $user->id,
      'notes' => $this->notes,
    ]);

    session()->flash('message', 'Payment recorded successfully.');

    $this->reset(['delivery_id', 'customer_id', 'amount', 'payment_method', 'bank_slip_reference', 'proforma_invoice_number', 'notes']);
  }

  public function render()
  {
    $deliveries = Delivery::with(['customer', 'product'])->get();
    $customers = Customer::where('is_active', true)->get();

    return view('livewire.sales.payments', [
      'deliveries' => $deliveries,
      'customers' => $customers,
    ]);
  }
}
