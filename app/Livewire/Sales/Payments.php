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
use Illuminate\Support\Facades\Storage;

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
    public $bank_slip_reference = '';
    public $proforma_invoice_number = '';
    public $payment_date = '';
    public $notes = '';
    public $slip_file = null;
    public $existing_slip_file = null;
    public $showImageViewer = false;
    public $viewerFileUrl = null;
    public $viewerFileName = null;
    public $viewerIsImage = false;
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
            'bank_slip_reference' => 'nullable|string|max:255',
            'proforma_invoice_number' => 'nullable|string|max:255',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
            'slip_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
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
        $this->bank_slip_reference = ''; // Clear text reference - it's stored in bank_slip_reference field but we'll keep it separate
        $this->proforma_invoice_number = $payment->proforma_invoice_number;
        $this->payment_date = $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '';
        $this->notes = $payment->notes;
        $this->existing_slip_file = $payment->bank_slip_reference ?? null; // File path stored here
        $this->slip_file = null; // Reset new file upload
        $this->isPaymentEdit = true;
        $this->showPaymentModal = true;
    }

    public function savePayment()
    {
        try {
            $this->validate();
            $user = Auth::user();
            $slipPath = $this->existing_slip_file;
            
            // Handle file upload
            if ($this->slip_file) {
                // Delete old file if exists
                if ($this->existing_slip_file && Storage::disk('public')->exists($this->existing_slip_file)) {
                    Storage::disk('public')->delete($this->existing_slip_file);
                }
                $slipPath = $this->slip_file->store('payment_slips', 'public');
            }
            
            if ($this->isPaymentEdit && $this->paymentId) {
                $payment = Payment::findOrFail($this->paymentId);
                $payment->update([
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
                session()->flash('message', 'Payment updated successfully.');
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
                session()->flash('message', 'Payment recorded successfully.');
            }
            
            $this->resetPaymentForm();
            $this->showPaymentModal = false;
            $this->dispatch('payment-saved');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors are automatically handled by Livewire
            session()->flash('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred: ' . $e->getMessage());
            \Log::error('Payment save error: ' . $e->getMessage());
        }
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
        $this->resetErrorBag();
    }
    
    public function removeSlipFile()
    {
        $this->slip_file = null;
        $this->existing_slip_file = null;
    }
    
    public function viewFile($filePath)
    {
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            session()->flash('error', 'File not found.');
            return;
        }
        
        // Use asset() helper for reliable URL generation
        $this->viewerFileUrl = asset('storage/' . $filePath);
        $this->viewerFileName = basename($filePath);
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $this->viewerIsImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $this->showImageViewer = true;
    }
    
    public function closeImageViewer()
    {
        $this->showImageViewer = false;
        $this->viewerFileUrl = null;
        $this->viewerFileName = null;
        $this->viewerIsImage = false;
    }

    public function render()
    {
        $payments = Payment::with([
            'productionOrder', 
            'customer'])
            ->when($this->paymentSearch, function ($q) {
                $q->where('bank_slip_reference', 'like', "%{$this->paymentSearch}%")
                  ->orWhere('proforma_invoice_number', 'like', "%{$this->paymentSearch}%");
            })
            ->latest()
            ->paginate($this->paymentPerPage);
        $orders = ProductionOrder::with(['customer', 'orderItems.product'])->latest()->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('livewire.sales.payments', [
            'payments' => $payments,
            'orders' => $orders,
            'customers' => $customers,
        ]);
    }
}
