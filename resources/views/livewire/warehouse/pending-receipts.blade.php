<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">Warehouse Receipts</h2>
        <p class="text-zinc-500 dark:text-zinc-400">Incoming goods from production that need physical verification.</p>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Batch</th>
                        <th>Produced Qty</th>
                        <th>Produced By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($receipts as $receipt)
                        <tr>
                            <td>{{ $receipt->production_date->format('Y-m-d') }}</td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ $receipt->product->name }}</span>
                                    <span class="text-xs text-zinc-500">{{ $receipt->product->code }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded text-xs font-mono">{{ $receipt->batch_number }}</span>
                            </td>
                            <td>
                                <span class="font-bold text-blue-600">{{ number_format($receipt->quantity, 2) }}</span>
                            </td>
                            <td>{{ $receipt->producedBy->name ?? 'System' }}</td>
                            <td>
                                <button wire:click="openConfirmModal({{ $receipt->id }})" class="btn btn-primary btn-sm">
                                    Verify & Accept
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-zinc-500">
                                No pending receipts from production.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-800">
            {{ $receipts->links() }}
        </div>
    </div>

    <!-- Confirmation Modal (using DaisyUI) -->
    <dialog class="modal @if($showConfirmModal) modal-open @endif">
        <div class="modal-box w-full max-w-lg">
            <h3 class="font-bold text-lg mb-2">Confirm Receipt</h3>
            <p class="text-sm text-zinc-500 mb-4">Verify the physical amount received for <strong>{{ $product_name }}</strong> (Batch: {{ $batch_number }})</p>

            <div class="grid grid-cols-2 gap-4 p-4 bg-zinc-50 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 mb-6">
                <div>
                    <p class="text-xs text-zinc-500 uppercase tracking-widest font-semibold">Production Reported</p>
                    <p class="text-xl font-bold">{{ number_format($production_quantity, 2) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-zinc-500 uppercase tracking-widest font-semibold">Status</p>
                    <span class="badge badge-warning badge-sm">Pending Arrival</span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Actual Received Quantity</span>
                    </label>
                    <input wire:model="received_quantity" type="number" step="0.01" class="input input-bordered w-full" />
                    @error('received_quantity') <span class="text-error text-xs">{{ $message }}</span> @enderror
                    <p class="text-xs text-zinc-500 mt-1">Modify this if the physical count is different from what production reported (e.g., 98 instead of 100).</p>
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Receipt Notes (Optional)</span>
                    </label>
                    <textarea wire:model="receipt_notes" class="textarea textarea-bordered h-24" placeholder="Enter reason for discrepancy, damage notes, or location tags..."></textarea>
                    @error('receipt_notes') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="modal-action">
                <button wire:click="$set('showConfirmModal', false)" class="btn btn-ghost">Cancel</button>
                <button wire:click="confirmReceipt" class="btn btn-primary">Confirm & Add to Stock</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button wire:click="$set('showConfirmModal', false)">close</button>
        </form>
    </dialog>
</div>
