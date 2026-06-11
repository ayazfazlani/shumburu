<div class="p-6">
    {{-- Header --}}
    <div class="mb-8 px-2 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black text-zinc-900 dark:text-white uppercase tracking-tighter">Purchase Payments</h2>
            <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">Accounts Payable — Track supplier payments against Purchase Orders</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-emerald-600 px-5 py-3 rounded-2xl shadow-lg text-white text-center">
                <span class="block text-[8px] font-black uppercase opacity-70 tracking-widest leading-none mb-1">Total Paid</span>
                <span class="text-2xl font-black">{{ number_format($stats['total_paid'], 0) }}</span>
            </div>
            <div class="bg-red-500 px-5 py-3 rounded-2xl shadow-lg text-white text-center">
                <span class="block text-[8px] font-black uppercase opacity-70 tracking-widest leading-none mb-1">Outstanding AP</span>
                <span class="text-2xl font-black">{{ number_format($stats['total_outstanding'], 0) }}</span>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 px-2">
            <div class="bg-green-500 text-white py-3 px-4 rounded-xl font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Filters --}}
    <div class="flex gap-3 mb-6 px-2">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search by material or PO #..."
            class="input input-bordered flex-1 max-w-xs" />
        <select wire:model="filterSupplier" class="select select-bordered">
            <option value="">All Suppliers</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Received POs Table --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm mb-8">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-zinc-500">Received POs — Payment Ledger</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <th class="bg-transparent pl-8">PO Number</th>
                        <th class="bg-transparent">Material</th>
                        <th class="bg-transparent">Supplier</th>
                        <th class="bg-transparent">Total Value</th>
                        <th class="bg-transparent">Total Paid</th>
                        <th class="bg-transparent">Balance Due</th>
                        <th class="bg-transparent">Payments</th>
                        <th class="bg-transparent text-right pr-8">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                    @forelse($receivedPOs as $pr)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="pl-8 py-5 font-mono text-sm font-black text-blue-600">{{ $pr->po_number }}</td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-zinc-900 dark:text-white uppercase">{{ $pr->rawMaterial->name }}</span>
                                    <span class="text-[9px] font-bold text-zinc-400">{{ number_format($pr->quantity, 2) }} {{ $pr->rawMaterial->unit }}</span>
                                </div>
                            </td>
                            <td class="font-bold text-zinc-600 text-sm">{{ $pr->supplier->name ?? '—' }}</td>
                            <td class="font-black text-zinc-700">{{ number_format($pr->total_amount, 2) }}</td>
                            <td class="font-black text-emerald-600">{{ number_format($pr->total_paid, 2) }}</td>
                            <td>
                                @if($pr->balance_due > 0)
                                    <span class="font-black text-red-500">{{ number_format($pr->balance_due, 2) }}</span>
                                @else
                                    <span class="badge badge-success badge-sm font-black">Fully Paid</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-outline h-5 text-[9px] font-black">{{ $pr->purchasePayments->count() }} Payments</span>
                            </td>
                            <td class="text-right pr-8">
                                @if($pr->balance_due > 0)
                                    <button wire:click="openPaymentModal({{ $pr->id }})"
                                        class="btn btn-accent btn-sm px-5 font-black uppercase text-[10px]">
                                        + Record Payment
                                    </button>
                                @else
                                    <span class="text-zinc-300 text-[10px] uppercase font-bold">Settled</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-20 text-zinc-300 font-bold uppercase tracking-widest opacity-50">No received POs found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $receivedPOs->links() }}</div>
    </div>

    {{-- Recent Payments Log --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm opacity-70 hover:opacity-100 transition-opacity">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-zinc-500">Recent Payment Transactions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <th class="bg-transparent pl-8">PO #</th>
                        <th class="bg-transparent">Supplier</th>
                        <th class="bg-transparent">Amount</th>
                        <th class="bg-transparent">Method</th>
                        <th class="bg-transparent">Reference</th>
                        <th class="bg-transparent">Date</th>
                        <th class="bg-transparent">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="pl-8 py-4 font-mono text-xs font-bold text-blue-600">
                                {{ $payment->purchaseRequest->po_number ?? '—' }}
                            </td>
                            <td class="font-bold text-zinc-600 text-sm">{{ $payment->supplier->name ?? '—' }}</td>
                            <td class="font-black text-emerald-600 text-lg">{{ number_format($payment->amount, 2) }}</td>
                            <td>
                                <span class="badge badge-info badge-sm font-bold uppercase">{{ str_replace('_', ' ', $payment->payment_method ?? 'N/A') }}</span>
                            </td>
                            <td class="font-mono text-xs text-zinc-500">{{ $payment->reference_number ?? '—' }}</td>
                            <td class="text-xs font-bold text-zinc-400 uppercase">{{ $payment->payment_date->format('d M Y') }}</td>
                            <td class="text-xs font-bold text-zinc-500">{{ $payment->recordedBy->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-12 text-zinc-300 font-bold uppercase tracking-widest opacity-50">No payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Payment Modal --}}
    <dialog class="modal" @if($showModal) open @endif>
        <div class="modal-box w-full max-w-lg rounded-2xl border border-zinc-200 dark:border-zinc-700">
            <h3 class="font-black text-xl mb-6 uppercase tracking-tight">Record Purchase Payment</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Amount <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" wire:model="pay_amount" class="input input-bordered w-full" />
                    @error('pay_amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Payment Method <span class="text-red-500">*</span></label>
                    <select wire:model="pay_method" class="select select-bordered w-full">
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="online">Online Payment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Reference / Slip No.</label>
                    <input type="text" wire:model="pay_reference" class="input input-bordered w-full" placeholder="Bank slip, cheque number..." />
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="pay_date" class="input input-bordered w-full" />
                    @error('pay_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Notes</label>
                    <textarea wire:model="pay_notes" class="textarea textarea-bordered w-full" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-action gap-2 mt-4">
                <button wire:click="$set('showModal', false)" class="btn btn-ghost font-black uppercase text-xs">Cancel</button>
                <button wire:click="recordPayment" class="btn btn-accent font-black uppercase text-xs">Save Payment</button>
            </div>
        </div>
    </dialog>
</div>
