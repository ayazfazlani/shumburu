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
                    <th>Orders</th>
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
                        <td>{{ $payment->productionOrder->order_number ?? '-' }}</td>
                        <td>{{ $payment->customer->name ?? '-' }}</td>
                        <td>{{ $payment->amount }}</td>
                        <td>{{ $payment->payment_method }}</td>
                        <td>{{ $payment->bank_slip_reference }}</td>
                        <td>
                            @if ($payment->bank_slip_reference && Storage::disk('public')->exists($payment->bank_slip_reference))
                                @php
                                    $fileUrl = asset('storage/' . $payment->bank_slip_reference);
                                    $isImage = in_array(strtolower(pathinfo($payment->bank_slip_reference, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                @if($isImage)
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $fileUrl }}" alt="Slip" class="w-16 h-16 object-cover rounded border cursor-pointer hover:opacity-80 transition-opacity" 
                                             wire:click="viewFile('{{ $payment->bank_slip_reference }}')" />
                                        <button wire:click="viewFile('{{ $payment->bank_slip_reference }}')" class="text-blue-600 underline text-xs hover:text-blue-800">View</button>
                                    </div>
                                @else
                                    <button wire:click="viewFile('{{ $payment->bank_slip_reference }}')" class="text-blue-600 underline flex items-center gap-1 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        View PDF
                                    </button>
                                @endif
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
    @if($showPaymentModal)
    <dialog id="payment-modal" class="modal modal-open">
        <div class="modal-box w-full max-w-2xl">
            <form wire:submit.prevent="savePayment" enctype="multipart/form-data">
                <h3 class="font-bold text-lg mb-4">{{ $isPaymentEdit ? 'Edit Payment' : 'Record Payment' }}</h3>
                
                @if(session('message'))
                    <div class="alert alert-success mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('message') }}</span>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-error mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-error mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h3 class="font-bold">Please fix the following errors:</h3>
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            <div class="mb-4">
                <label class="label">Production Order <span class="text-red-500">*</span></label>
                <select wire:model="order_id" class="select select-bordered w-full @error('order_id') select-error @enderror">
                    <option value="">Select Production Order</option>
                    @foreach ($orders as $order)
                        <option value="{{ $order->id }}">
                            Order #{{ $order->order_number }} - {{ $order->customer->display_name ?? 'N/A' }} ({{ $order->status ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('order_id')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Customer <span class="text-red-500">*</span></label>
                <select wire:model="customer_id" class="select select-bordered w-full @error('customer_id') select-error @enderror">
                    <option value="">Select Customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
                @error('customer_id')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Amount <span class="text-red-500">*</span></label>
                <input type="number" wire:model="amount" class="input input-bordered w-full @error('amount') input-error @enderror" min="0" step="0.01" placeholder="0.00" />
                @error('amount')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Payment Method</label>
                <input type="text" wire:model="payment_method" class="input input-bordered w-full @error('payment_method') input-error @enderror" placeholder="e.g., Bank Transfer, Cash, etc." />
                @error('payment_method')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Bank Slip Reference (Optional Text Reference)</label>
                <input type="text" wire:model="bank_slip_reference" class="input input-bordered w-full @error('bank_slip_reference') input-error @enderror" placeholder="Bank Slip Reference" />
                @error('bank_slip_reference')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Slip File (Image/PDF)</label>
                <input type="file" wire:model="slip_file" class="file-input file-input-bordered w-full" accept=".jpg,.jpeg,.png,.pdf" />
                @error('slip_file')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
                
                <!-- Image Preview for New Upload -->
                @if($slip_file)
                    <div class="mt-4 p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Preview:</span>
                            <button type="button" wire:click="removeSlipFile" class="btn btn-xs btn-error">Remove</button>
                        </div>
                        @php
                            $isImage = in_array(strtolower($slip_file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        @if($isImage)
                            <div wire:loading.remove wire:target="slip_file">
                                @php
                                    try {
                                        $previewUrl = $slip_file->isPreviewable() ? $slip_file->temporaryUrl() : null;
                                    } catch (\Exception $e) {
                                        $previewUrl = null;
                                    }
                                @endphp
                                @if($previewUrl)
                                    <div class="space-y-2">
                                        <img src="{{ $previewUrl }}" alt="Preview" class="max-w-full h-auto max-h-64 rounded border cursor-pointer hover:opacity-80 transition-opacity" 
                                             onclick="window.open('{{ $previewUrl }}', '_blank')" />
                                        <button onclick="window.open('{{ $previewUrl }}', '_blank')" class="text-blue-600 underline text-xs hover:text-blue-800">View Full Size</button>
                                    </div>
                                @else
                                    <div class="p-4 bg-white dark:bg-gray-700 rounded border">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $slip_file->getClientOriginalName() }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Image ready to upload ({{ number_format($slip_file->getSize() / 1024, 2) }} KB)</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div wire:loading wire:target="slip_file" class="flex items-center justify-center p-4">
                                <span class="loading loading-spinner loading-md"></span>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Uploading...</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 p-4 bg-white dark:bg-gray-700 rounded">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $slip_file->getClientOriginalName() }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF File ({{ number_format($slip_file->getSize() / 1024, 2) }} KB)</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
                
                <!-- Existing File Display -->
                @if($existing_slip_file && !$slip_file && Storage::disk('public')->exists($existing_slip_file))
                    <div class="mt-4 p-4 border rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Current File:</span>
                            <button type="button" wire:click="removeSlipFile" class="btn btn-xs btn-error">Remove</button>
                        </div>
                        @php
                            $fileUrl = asset('storage/' . $existing_slip_file);
                            $isImage = in_array(strtolower(pathinfo($existing_slip_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        @endphp
                        @if($isImage)
                            <img src="{{ $fileUrl }}" alt="Current Slip" class="max-w-full h-auto max-h-64 rounded border cursor-pointer hover:opacity-80 transition-opacity" 
                                 wire:click="viewFile('{{ $existing_slip_file }}')" />
                            <button wire:click="viewFile('{{ $existing_slip_file }}')" class="text-blue-600 underline text-xs mt-2 hover:text-blue-800">View Full Size</button>
                        @else
                            <div class="flex items-center gap-2 p-4 bg-white dark:bg-gray-700 rounded">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ basename($existing_slip_file) }}</p>
                                    <button wire:click="viewFile('{{ $existing_slip_file }}')" class="text-blue-600 underline text-xs hover:text-blue-800">View PDF</button>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            <div class="mb-4">
                <label class="label">Proforma Invoice Number</label>
                <input type="text" wire:model="proforma_invoice_number" class="input input-bordered w-full @error('proforma_invoice_number') input-error @enderror" placeholder="Proforma Invoice Number" />
                @error('proforma_invoice_number')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Payment Date <span class="text-red-500">*</span></label>
                <input type="date" wire:model="payment_date" class="input input-bordered w-full @error('payment_date') input-error @enderror" />
                @error('payment_date')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-4">
                <label class="label">Notes</label>
                <textarea wire:model="notes" class="textarea textarea-bordered w-full @error('notes') textarea-error @enderror" placeholder="Additional notes"></textarea>
                @error('notes')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
                <div class="modal-action flex gap-2">
                    <button type="button" class="btn" wire:click="$set('showPaymentModal', false)" wire:loading.attr="disabled">Cancel</button>
                    <button type="submit" wire:loading.attr="disabled" wire:target="savePayment" class="btn btn-primary">
                        <span wire:loading.remove wire:target="savePayment">{{ $isPaymentEdit ? 'Update' : 'Record' }}</span>
                        <span wire:loading wire:target="savePayment" class="flex items-center gap-2">
                            <span class="loading loading-spinner loading-sm"></span>
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button wire:click="$set('showPaymentModal', false)">close</button>
        </form>
    </dialog>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showPaymentDeleteModal)
    <dialog id="delete-modal" class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">Delete Payment?</h3>
            <p class="mb-4">Are you sure you want to delete this payment? This action cannot be undone.</p>
            <div class="modal-action flex gap-2">
                <button type="button" class="btn" wire:click="$set('showPaymentDeleteModal', false)">Cancel</button>
                <button type="button" class="btn btn-error" wire:click="deletePayment">Delete</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button wire:click="$set('showPaymentDeleteModal', false)">close</button>
        </form>
    </dialog>
    @endif

    <!-- File Viewer Modal - Matches Dashboard Theme -->
    @if($showImageViewer)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75" wire:click="closeImageViewer">
        <div class="relative max-w-6xl w-full mx-4 bg-white dark:bg-gray-800 rounded-lg shadow-2xl" wire:click.stop>
            <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $viewerFileName }}</h3>
                <div class="flex items-center gap-2">
                    @if($viewerFileUrl)
                        <a href="{{ $viewerFileUrl }}" download class="btn btn-sm btn-primary">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download
                        </a>
                    @endif
                    <button wire:click="closeImageViewer" class="btn btn-sm btn-ghost">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6 max-h-[80vh] overflow-auto">
                @if($viewerIsImage && $viewerFileUrl)
                    <div class="flex justify-center">
                        <img src="{{ $viewerFileUrl }}" alt="{{ $viewerFileName }}" class="max-w-full h-auto rounded-lg shadow-lg" />
                    </div>
                @elseif($viewerFileUrl)
                    <div class="flex flex-col items-center justify-center min-h-[400px]">
                        <svg class="w-24 h-24 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">{{ $viewerFileName }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">PDF files cannot be previewed inline</p>
                        <a href="{{ $viewerFileUrl }}" target="_blank" class="btn btn-primary">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            Open PDF in New Tab
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</section>
