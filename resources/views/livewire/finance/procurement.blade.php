<div class="p-6">
    {{-- Header --}}
    <div class="mb-8 px-2 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black text-zinc-900 dark:text-white uppercase tracking-tighter">Procurement</h2>
            <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">PR → Approve → PO → GRN → Payment</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-amber-500 px-5 py-3 rounded-2xl shadow-lg text-white text-center">
                <span class="block text-[8px] font-black uppercase opacity-70 tracking-widest leading-none mb-1">Pending PRs</span>
                <span class="text-2xl font-black">{{ $stats['pending_count'] }}</span>
            </div>
            <div class="bg-blue-600 px-5 py-3 rounded-2xl shadow-lg text-white text-center">
                <span class="block text-[8px] font-black uppercase opacity-70 tracking-widest leading-none mb-1">Active POs</span>
                <span class="text-2xl font-black">{{ $stats['active_pos'] }}</span>
            </div>
            <div class="bg-emerald-600 px-5 py-3 rounded-2xl shadow-lg text-white text-center">
                <span class="block text-[8px] font-black uppercase opacity-70 tracking-widest leading-none mb-1">Pending GRN</span>
                <span class="text-2xl font-black">{{ $stats['pending_grn'] }}</span>
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

    {{-- Tab Nav --}}
    <div class="flex gap-1 mb-6 px-2">
        @foreach(['pending' => '① Pending PRs', 'active_pos' => '② Active POs', 'pending_grn' => '③ Pending GRN', 'history' => '④ History'] as $tab => $label)
            <button wire:click="setTab('{{ $tab }}')"
                class="px-5 py-2 rounded-xl text-[11px] font-black uppercase tracking-widest transition-all
                {{ $activeTab === $tab ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-lg' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                {{ $label }}
                @if($tab === 'pending' && $stats['pending_count'] > 0)
                    <span class="ml-1 bg-amber-500 text-white rounded-full px-2 py-0.5 text-[9px]">{{ $stats['pending_count'] }}</span>
                @elseif($tab === 'pending_grn' && $stats['pending_grn'] > 0)
                    <span class="ml-1 bg-emerald-500 text-white rounded-full px-2 py-0.5 text-[9px]">{{ $stats['pending_grn'] }}</span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- ── TAB 1: PENDING PRs ── --}}
    @if($activeTab === 'pending')
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-amber-50 dark:bg-amber-900/10">
            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-amber-600">Purchase Requests Awaiting Procurement Approval</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <th class="bg-transparent pl-8">Material</th>
                        <th class="bg-transparent">Qty Required</th>
                        <th class="bg-transparent">Raised By</th>
                        <th class="bg-transparent">Linked Plan</th>
                        <th class="bg-transparent">Date Raised</th>
                        <th class="bg-transparent text-right pr-8">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                    @forelse($pendingPRs as $pr)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="pl-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-zinc-900 dark:text-white uppercase">{{ $pr->rawMaterial->name }}</span>
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase">Unit: {{ $pr->rawMaterial->unit }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="text-xl font-black text-amber-600">{{ number_format($pr->quantity, 2) }}</span>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase ml-1">{{ $pr->rawMaterial->unit }}</span>
                            </td>
                            <td class="text-sm font-bold text-zinc-500">{{ $pr->requestedBy->name ?? 'N/A' }}</td>
                            <td>
                                @if($pr->production_request_id)
                                    <span class="badge badge-outline h-5 text-[8px] font-bold uppercase">Plan #{{ $pr->production_request_id }}</span>
                                @else
                                    <span class="text-zinc-300 text-xs">Manual</span>
                                @endif
                            </td>
                            <td class="text-xs font-bold text-zinc-400 uppercase">{{ $pr->created_at->format('d M Y') }}</td>
                            <td class="text-right pr-8">
                                <button wire:click="openApproveModal({{ $pr->id }})"
                                    class="btn btn-warning btn-sm px-6 font-black uppercase text-[10px] text-white border-none">
                                    Approve PR
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-20 text-zinc-300 font-bold uppercase tracking-widest opacity-50">No pending purchase requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── TAB 2: ACTIVE POs ── --}}
    @if($activeTab === 'active_pos')
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-blue-50 dark:bg-blue-900/10">
            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-blue-600">Approved PRs — Issue PO & Track Delivery</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <th class="bg-transparent pl-8">Material</th>
                        <th class="bg-transparent">Qty</th>
                        <th class="bg-transparent">PO #</th>
                        <th class="bg-transparent">Supplier</th>
                        <th class="bg-transparent">Unit Price</th>
                        <th class="bg-transparent">Total Value</th>
                        <th class="bg-transparent">Exp. Delivery</th>
                        <th class="bg-transparent">Status</th>
                        <th class="bg-transparent text-right pr-8">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                    @forelse($activePos as $pr)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="pl-8 py-5">
                                <span class="text-sm font-black text-zinc-900 dark:text-white uppercase">{{ $pr->rawMaterial->name }}</span>
                            </td>
                            <td>
                                <span class="font-black text-zinc-700 dark:text-zinc-300">{{ number_format($pr->quantity, 2) }}</span>
                                <span class="text-[10px] text-zinc-400 uppercase ml-1">{{ $pr->rawMaterial->unit }}</span>
                            </td>
                            <td>
                                @if($pr->po_number)
                                    <span class="font-mono text-xs font-bold text-blue-600">{{ $pr->po_number }}</span>
                                @else
                                    <span class="text-zinc-300 text-xs italic">Not issued</span>
                                @endif
                            </td>
                            <td class="font-bold text-zinc-600 text-sm">{{ $pr->supplier->name ?? '—' }}</td>
                            <td>
                                @if($pr->unit_price)
                                    <span class="font-black text-zinc-700">{{ number_format($pr->unit_price, 2) }}</span>
                                @else
                                    <span class="text-zinc-300">—</span>
                                @endif
                            </td>
                            <td>
                                @if($pr->unit_price)
                                    <span class="font-black text-emerald-600">{{ number_format($pr->total_amount, 2) }}</span>
                                @else
                                    <span class="text-zinc-300">—</span>
                                @endif
                            </td>
                            <td class="text-xs font-bold text-zinc-400 uppercase">
                                {{ $pr->expected_delivery_date ? $pr->expected_delivery_date->format('d M Y') : '—' }}
                            </td>
                            <td>
                                @if($pr->status === 'approved')
                                    <span class="badge badge-warning h-6 px-3 text-[9px] font-black uppercase text-white">Approved</span>
                                @elseif($pr->status === 'po_issued')
                                    <span class="badge badge-info h-6 px-3 text-[9px] font-black uppercase text-white">PO Issued</span>
                                @endif
                            </td>
                            <td class="text-right pr-8">
                                <div class="flex justify-end gap-2">
                                    @if($pr->status === 'approved')
                                        <button wire:click="openPoModal({{ $pr->id }})"
                                            class="btn btn-primary btn-sm px-5 font-black uppercase text-[10px] border-none">
                                            Issue PO
                                        </button>
                                    @elseif($pr->status === 'po_issued')
                                        {{-- Printable RFQ (Preview in Modal) --}}
                                        <button wire:click="openRfqModal({{ $pr->id }})"
                                            class="btn btn-outline btn-sm px-4 font-black uppercase text-[10px]">
                                             🖨 Preview PO
                                        </button>
                                        <button wire:click="openDeliverModal({{ $pr->id }})"
                                            onclick="confirm('Confirm supplier has delivered this order?') || event.stopImmediatePropagation()"
                                            class="btn btn-success btn-sm px-5 font-black uppercase text-[10px] text-white border-none">
                                            Mark Delivered
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center py-20 text-zinc-300 font-bold uppercase tracking-widest opacity-50">No active purchase orders.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── TAB 3: PENDING GRN ── --}}
    @if($activeTab === 'pending_grn')
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-emerald-50 dark:bg-emerald-900/10">
            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-emerald-600">Delivered POs — Awaiting Warehouse GRN Confirmation</h3>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Warehouse must confirm receipt via Stock-In before stock is updated.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <th class="bg-transparent pl-8">PO Number</th>
                        <th class="bg-transparent">Material</th>
                        <th class="bg-transparent">Qty</th>
                        <th class="bg-transparent">Supplier</th>
                        <th class="bg-transparent">Total Value</th>
                        <th class="bg-transparent">Delivered At</th>
                        <th class="bg-transparent text-right pr-8">GRN Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                    @forelse($pendingGrns as $pr)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="pl-8 py-5">
                                <span class="font-mono font-black text-blue-600">{{ $pr->po_number }}</span>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-zinc-900 dark:text-white uppercase">{{ $pr->rawMaterial->name }}</span>
                                    <span class="text-[9px] font-bold text-zinc-400 uppercase">{{ $pr->rawMaterial->unit }}</span>
                                </div>
                            </td>
                            <td><span class="font-black text-xl text-emerald-600">{{ number_format($pr->quantity, 2) }}</span></td>
                            <td class="font-bold text-zinc-600">{{ $pr->supplier->name ?? '—' }}</td>
                            <td class="font-black text-emerald-600">{{ number_format($pr->total_amount, 2) }}</td>
                            <td class="text-xs font-bold text-zinc-400 uppercase">{{ $pr->delivered_at ? $pr->delivered_at->format('d M Y H:i') : '—' }}</td>
                            <td class="text-right pr-8">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="badge badge-warning h-7 px-3 text-[9px] font-black uppercase text-white animate-pulse">
                                        ⏳ Awaiting Warehouse GRN
                                    </span>
                                    <a href="{{ route('warehouse.stock-in') }}"
                                        class="btn btn-outline btn-xs font-black uppercase text-[9px]">
                                        Go to Stock-In →
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-20 text-zinc-300 font-bold uppercase tracking-widest opacity-50">No pending GRN confirmations.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── TAB 4: HISTORY ── --}}
    @if($activeTab === 'history')
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm opacity-80 hover:opacity-100 transition-opacity">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-zinc-500">Completed Purchase History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <th class="bg-transparent pl-8">PO #</th>
                        <th class="bg-transparent">Material</th>
                        <th class="bg-transparent">Qty</th>
                        <th class="bg-transparent">Supplier</th>
                        <th class="bg-transparent">Total Value</th>
                        <th class="bg-transparent">Total Paid</th>
                        <th class="bg-transparent">Balance Due</th>
                        <th class="bg-transparent text-right pr-8">Payment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                    @forelse($history as $pr)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="pl-8 py-4 font-mono text-xs font-bold text-blue-600">{{ $pr->po_number }}</td>
                            <td class="text-sm font-black text-zinc-700 dark:text-zinc-300 uppercase">{{ $pr->rawMaterial->name }}</td>
                            <td class="font-bold text-zinc-600">{{ number_format($pr->quantity, 2) }}</td>
                            <td class="font-bold text-zinc-600 text-sm">{{ $pr->supplier->name ?? '—' }}</td>
                            <td class="font-black text-zinc-700">{{ number_format($pr->total_amount, 2) }}</td>
                            <td class="font-black text-emerald-600">{{ number_format($pr->total_paid, 2) }}</td>
                            <td>
                                @if($pr->balance_due > 0)
                                    <span class="font-black text-red-500">{{ number_format($pr->balance_due, 2) }}</span>
                                @else
                                    <span class="badge badge-success badge-sm font-black">Paid</span>
                                @endif
                            </td>
                            <td class="text-right pr-8">
                                @if($pr->balance_due > 0)
                                    <button wire:click="openPaymentModal({{ $pr->id }})"
                                        class="btn btn-xs btn-accent font-black uppercase text-[9px] px-4">
                                        + Record Payment
                                    </button>
                                @else
                                    <span class="text-zinc-300 text-[10px] uppercase font-bold">Fully Paid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-20 text-zinc-300 font-bold uppercase tracking-widest opacity-50">No completed purchases.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ═══════ MODALS ═══════ --}}

    {{-- Approve PR Modal --}}
    <dialog class="modal" @if($showApproveModal) open @endif>
        <div class="modal-box rounded-2xl border border-zinc-200 dark:border-zinc-700">
            <h3 class="font-black text-lg mb-2 uppercase tracking-tight">Approve Purchase Request?</h3>
            <p class="text-sm text-zinc-500 mb-6">This will move the PR to the procurement queue for PO issuance. You can then select a supplier and issue a Purchase Order.</p>
            <div class="modal-action gap-2">
                <button wire:click="$set('showApproveModal', false)" class="btn btn-ghost font-black uppercase text-xs">Cancel</button>
                <button wire:click="approvePR" class="btn btn-warning font-black uppercase text-xs text-white">✓ Approve</button>
            </div>
        </div>
    </dialog>

    {{-- Issue PO Modal --}}
    <dialog class="modal" @if($showPoModal) open @endif>
        <div class="modal-box w-full max-w-2xl rounded-2xl border border-zinc-200 dark:border-zinc-700">
            <h3 class="font-black text-xl mb-6 uppercase tracking-tight">Issue Purchase Order</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Supplier <span class="text-red-500">*</span></label>
                    <select wire:model="po_supplier_id" class="select select-bordered w-full">
                        <option value="">— Select Supplier —</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->code }})</option>
                        @endforeach
                    </select>
                    @error('po_supplier_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">PO Number <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="po_number" class="input input-bordered w-full font-mono" />
                    @error('po_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Unit Price (per {{ optional(optional(\App\Models\PurchaseRequest::find($poRequestId))->rawMaterial)->unit ?? 'unit' }}) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.0001" wire:model="po_unit_price" class="input input-bordered w-full" placeholder="0.00" />
                    @error('po_unit_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Expected Delivery Date <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="po_expected_date" class="input input-bordered w-full" />
                    @error('po_expected_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="modal-action gap-2 mt-6">
                <button wire:click="$set('showPoModal', false)" class="btn btn-ghost font-black uppercase text-xs">Cancel</button>
                <button wire:click="issuePO" class="btn btn-primary font-black uppercase text-xs">Issue PO →</button>
            </div>
        </div>
    </dialog>

    {{-- Deliver Modal --}}
    <dialog class="modal" @if($showDeliverModal) open @endif>
        <div class="modal-box rounded-2xl border border-zinc-200 dark:border-zinc-700">
            <h3 class="font-black text-lg mb-2 uppercase tracking-tight">Confirm Delivery</h3>
            <p class="text-sm text-zinc-500 mb-4">Confirm that the supplier has delivered this order to the factory gate. <strong>Warehouse will then confirm GRN and update stock.</strong></p>
            <div class="modal-action gap-2">
                <button wire:click="$set('showDeliverModal', false)" class="btn btn-ghost font-black uppercase text-xs">Cancel</button>
                <button wire:click="markDelivered" class="btn btn-success font-black uppercase text-xs text-white">✓ Confirm Delivered</button>
            </div>
        </div>
    </dialog>

    {{-- Payment Modal --}}
    <dialog class="modal" @if($showPaymentModal) open @endif>
        <div class="modal-box w-full max-w-lg rounded-2xl border border-zinc-200 dark:border-zinc-700">
            <h3 class="font-black text-xl mb-6 uppercase tracking-tight">Record Purchase Payment</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Amount <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" wire:model="pay_amount" class="input input-bordered w-full" placeholder="0.00" />
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
                    <input type="text" wire:model="pay_reference" class="input input-bordered w-full" placeholder="Bank slip or cheque number" />
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" wire:model="pay_date" class="input input-bordered w-full" />
                    @error('pay_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Notes</label>
                    <textarea wire:model="pay_notes" class="textarea textarea-bordered w-full" rows="2" placeholder="Optional notes"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-zinc-500 mb-1">Receipt / Proof of Payment</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-zinc-300 border-dashed rounded-xl cursor-pointer bg-zinc-50 dark:hover:bg-zinc-800 dark:bg-zinc-800 hover:bg-zinc-100 transition-all">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-zinc-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                </svg>
                                <p class="mb-2 text-xs text-zinc-500 uppercase font-black tracking-widest">
                                    @if($pay_receipt)
                                        <span class="text-emerald-500">Photo Selected ✓</span>
                                    @else
                                        <span>Click to upload Receipt</span>
                                    @endif
                                </p>
                                <p class="text-[9px] text-zinc-400">PNG, JPG or PDF (MAX. 2MB)</p>
                            </div>
                            <input type="file" wire:model="pay_receipt" class="hidden" accept="image/*" />
                        </label>
                    </div>
                    @error('pay_receipt') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    
                    <div wire:loading wire:target="pay_receipt" class="mt-2 text-xs font-bold text-zinc-400 animate-pulse">
                        Uploading digital receipt...
                    </div>

                    @if ($pay_receipt)
                        <div class="mt-4 p-2 bg-zinc-100 dark:bg-zinc-800 rounded-xl relative group">
                            <img src="{{ $pay_receipt->temporaryUrl() }}" class="w-full h-32 object-cover rounded-lg shadow-sm">
                            <button type="button" wire:click="$set('pay_receipt', null)" class="absolute top-4 right-4 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-action gap-2 mt-4">
                <button wire:click="$set('showPaymentModal', false)" class="btn btn-ghost font-black uppercase text-xs">Cancel</button>
                <button wire:click="recordPayment" class="btn btn-accent font-black uppercase text-xs">Save Payment</button>
            </div>
        </div>
    {{-- RFQ / PO Preview Modal --}}
    <dialog class="modal" @if($showRfqModal) open @endif>
        <div class="modal-box w-11/12 max-w-5xl h-[90vh] p-0 flex flex-col rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
            <div class="p-4 bg-zinc-900 text-white flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-zinc-800 rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z" /></svg>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest">Purchase Order Preview</span>
                </div>
                <button wire:click="$set('showRfqModal', false)" class="btn btn-circle btn-ghost btn-sm text-zinc-400 hover:text-white">✕</button>
            </div>
            <div class="flex-1 bg-zinc-100 dark:bg-zinc-800 p-4">
                @if($viewingRfqId)
                    <iframe src="{{ route('finance.procurement.rfq', $viewingRfqId) }}" class="w-full h-full rounded-xl shadow-2xl border-none bg-white"></iframe>
                @endif
            </div>
            <div class="p-4 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-3 font-bold">
                 <button wire:click="$set('showRfqModal', false)" class="btn btn-ghost btn-sm font-black uppercase text-[10px]">Close Preview</button>
                 <a href="{{ $viewingRfqId ? route('finance.procurement.rfq', $viewingRfqId) : '#' }}" target="_blank" class="btn btn-primary btn-sm px-6 font-black uppercase text-[10px]">
                    Open Full Page
                 </a>
            </div>
        </div>
    </dialog>
</div>
