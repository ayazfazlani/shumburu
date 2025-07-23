<section class="w-full p-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">Payments</h1>
            <p class="text-gray-500">Record, edit, and delete payments. Upload slip images or PDFs.</p>
        </div>
        <div class="flex gap-2">
            <input type="text" wire:model.debounce.300ms="paymentSearch" placeholder="Search slip ref..." class="input input-bordered" />
            <select wire:model="paymentPerPage" class="select select-bordered">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <button class="btn btn-primary" wire:click="openPaymentCreateModal">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Payment
            </button>
        </div>
    </div>

    @if (session('message'))
        <div class="alert alert-success mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-error mb-4">{{ session('error') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Delivery</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Slip Ref</th>
                    <th>Slip File</th>
                    <th>Proforma Invoice</th>
                    <th>Payment Date</th>
                    <th>Notes</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>{{ $payment->delivery->id ?? '-' }}</td>
                        <td>{{ $payment->customer->name ?? '-' }}</td>
                        <td>{{ $payment->amount }}</td>
                        <td>{{ $payment->payment_method }}</td>
                        <td>{{ $payment->bank_slip_reference }}</td>
                        <td>
                            @if($payment->slip_file)
                                <a href="{{ Storage::disk('public')->url($payment->slip_file) }}" target="_blank" class="text-blue-600 underline">View</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td>{{ $payment->proforma_invoice_number }}</td>
                        <td>{{ $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '' }}</td>
                        <td>{{ $payment->notes }}</td>
                        <td class="text-right flex gap-2 justify-end">
                            <button class="btn btn-xs btn-outline" wire:click="openPaymentEditModal({{ $payment->id }})">Edit</button>
                            <button class="btn btn-xs btn-error" wire:click="confirmPaymentDelete({{ $payment->id }})">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-gray-400 py-6">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $payments->links() }}</div>

    <!-- Create/Edit Modal -->
    <dialog id="payment-modal" class="modal" @if ($showPaymentModal) open @endif>
        <form method="dialog" class="modal-box w-full max-w-2xl" wire:submit.prevent="savePayment" enctype="multipart/form-data">
            <h3 class="font-bold text-lg mb-4">{{ $isPaymentEdit ? 'Edit Payment' : 'Record Payment' }}</h3>
            <div class="mb-4">
                <label class="label">Delivery</label>
                <select wire:model.defer="delivery_id" class="select select-bordered w-full">
                    <option value="">Select Delivery</option>
                    @foreach ($deliveries as $delivery)
                        <option value="{{ $delivery->id }}">
                            {{ $delivery->customer->name ?? '' }} - {{ $delivery->product->name ?? '' }} ({{ $delivery->id }})
                        </option>
                    @endforeach
                </select>
                @error('delivery_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Customer</label>
                <select wire:model.defer="customer_id" class="select select-bordered w-full">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Amount</label>
                <input type="number" wire:model.defer="amount" class="input input-bordered w-full" min="0" step="0.01" placeholder="Amount" />
                @error('amount')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Payment Method</label>
                <input type="text" wire:model.defer="payment_method" class="input input-bordered w-full" placeholder="Payment Method" />
                @error('payment_method')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Bank Slip Reference</label>
                <input type="text" wire:model.defer="bank_slip_reference" class="input input-bordered w-full" placeholder="Bank Slip Reference" />
                @error('bank_slip_reference')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Slip File (Image/PDF)</label>
                <input type="file" wire:model="slip_file" class="file-input file-input-bordered w-full" accept=".jpg,.jpeg,.png,.pdf" />
                @if($existing_slip_file)
                    <div class="mt-2">
                        <a href="{{ Storage::disk('public')->url($existing_slip_file) }}" target="_blank" class="text-blue-600 underline">Current File</a>
                    </div>
                @endif
                @error('slip_file')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Proforma Invoice Number</label>
                <input type="text" wire:model.defer="proforma_invoice_number" class="input input-bordered w-full" placeholder="Proforma Invoice Number" />
                @error('proforma_invoice_number')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Payment Date</label>
                <input type="date" wire:model.defer="payment_date" class="input input-bordered w-full" />
                @error('payment_date')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Notes</label>
                <textarea wire:model.defer="notes" class="textarea textarea-bordered w-full" placeholder="Additional notes"></textarea>
                @error('notes')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showPaymentModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">{{ $isPaymentEdit ? 'Update' : 'Record' }}</button>
            </div>
        </form>
    </dialog>

    <!-- Delete Confirmation Modal -->
    <dialog id="delete-modal" class="modal" @if ($showPaymentDeleteModal) open @endif>
        <form method="dialog" class="modal-box">
            <h3 class="font-bold text-lg mb-4">Delete Payment?</h3>
            <p class="mb-4">Are you sure you want to delete this payment? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showPaymentDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deletePayment">Delete</button>
            </div>
        </form>
    </dialog>
</section>
