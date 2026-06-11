<div class="p-6">
    <div class="mb-8 px-2 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black text-zinc-900 dark:text-white uppercase tracking-tighter">Goods Receipt Center</h2>
            <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">Accept incoming materials into the physical warehouse inventory.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="setTab('fg')" 
                class="px-6 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all
                {{ $activeTab === 'fg' ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-lg' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500' }}">
                ① Production Receipts (FG)
            </button>
            <button wire:click="setTab('rm')" 
                class="px-6 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all
                {{ $activeTab === 'rm' ? 'bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 shadow-lg' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500' }}">
                ② Supplier Receipts (RM)
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 px-2 animate-in fade-in zoom-in duration-300">
            <div class="bg-green-500 text-white py-3 px-4 rounded-xl font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- ── FINISHED GOODS TABLE ── --}}
    @if($activeTab === 'fg')
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm animate-in slide-in-from-bottom-2 duration-500">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-zinc-500">Pending FG Arrivals from Production</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <th class="bg-transparent pl-8">Date</th>
                        <th class="bg-transparent">Product</th>
                        <th class="bg-transparent">Batch #</th>
                        <th class="bg-transparent text-center">Produced Qty</th>
                        <th class="bg-transparent pr-8 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                    @forelse ($receipts as $receipt)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="pl-8 py-5 text-sm font-bold text-zinc-500">{{ $receipt->production_date->format('Y-m-d') }}</td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-zinc-900 dark:text-white uppercase">{{ $receipt->product->name }}</span>
                                    <span class="text-[10px] font-bold text-zinc-400">{{ $receipt->product->code }}</span>
                                </div>
                            </td>
                            <td><span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded text-xs font-mono font-bold">{{ $receipt->batch_number }}</span></td>
                            <td class="text-center"><span class="text-xl font-black text-blue-600">{{ number_format($receipt->quantity, 2) }}</span></td>
                            <td class="text-right pr-8">
                                <button wire:click="openConfirmModal({{ $receipt->id }})" class="btn btn-primary btn-sm px-6 font-black uppercase text-[10px]">
                                    Verify & Accept
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-20 text-zinc-300 font-bold uppercase tracking-widest opacity-50">No pending production receipts.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $receipts->links() }}</div>
    </div>
    @endif

    {{-- ── RAW MATERIAL TABLE ── --}}
    @if($activeTab === 'rm')
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm animate-in slide-in-from-bottom-2 duration-500">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-emerald-50 dark:bg-emerald-900/10 flex justify-between items-center">
            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-emerald-600">Pending Supplier Shipments (PO)</h3>
            <span class="text-[9px] font-black uppercase text-zinc-400 tracking-widest">Finance Approved → Warehouse Verified</span>
        </div>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <th class="bg-transparent pl-8 py-4 whitespace-nowrap">PO / Status</th>
                        <th class="bg-transparent">Material Details</th>
                        <th class="bg-transparent">Supplier</th>
                        <th class="bg-transparent text-center">Expected Qty</th>
                        <th class="bg-transparent pr-8 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                    @forelse ($rmReceipts as $pr)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="pl-8 py-5">
                                <div class="flex flex-col">
                                    <span class="font-mono text-sm font-black text-blue-600">#{{ $pr->po_number }}</span>
                                    @if($pr->status === 'delivered')
                                        <span class="text-[9px] font-black uppercase text-emerald-600 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> At Gate (Delivered)
                                        </span>
                                    @else
                                        <span class="text-[9px] font-black uppercase text-amber-500 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span> In Transit (Approved)
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-zinc-900 dark:text-white uppercase">{{ $pr->rawMaterial->name }}</span>
                                    <span class="text-[10px] font-bold text-zinc-400 tracking-widest">UNIT: {{ $pr->rawMaterial->unit }}</span>
                                </div>
                            </td>
                            <td class="font-bold text-zinc-500 text-xs">{{ $pr->supplier->name ?? 'Unknown Vendor' }}</td>
                            <td class="text-center font-black text-xl text-emerald-600">{{ number_format($pr->quantity, 2) }}</td>
                            <td class="text-right pr-8">
                                @if($pr->status === 'delivered')
                                    <button wire:click="openRmModal({{ $pr->id }})" class="btn btn-success btn-sm px-6 font-black uppercase text-[10px] text-white border-none shadow-md">
                                        Confirm GRN
                                    </button>
                                @else
                                    <button disabled class="btn btn-zinc btn-sm px-6 font-black uppercase text-[10px] opacity-30 cursor-not-allowed">
                                        Pending Delivery
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-32 text-zinc-300 font-bold uppercase tracking-widest opacity-50">No incoming deliveries tracked.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $rmReceipts->links() }}</div>
    </div>
    @endif

    {{-- FG Modal --}}
    <dialog class="modal @if($showConfirmModal) modal-open @endif">
        <div class="modal-box w-full max-w-lg rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-2xl">
            <h3 class="font-black text-2xl mb-2 uppercase tracking-tighter">Confirm FG Receipt</h3>
            <p class="text-sm text-zinc-500 mb-6">Physically verifying production output for <strong>{{ $product_name }}</strong></p>
            
            <div class="space-y-4">
                <div class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                    <span class="block text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Batch Reference</span>
                    <span class="text-lg font-mono font-bold">{{ $batch_number }}</span>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1">Received Quantity</label>
                    <input wire:model="received_quantity" type="number" step="0.01" class="input input-bordered w-full font-black text-2xl h-14" />
                    @error('received_quantity') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1">Receipt Notes</label>
                    <textarea wire:model="receipt_notes" class="textarea textarea-bordered w-full h-24 font-medium" placeholder="Shelf location, damage report..."></textarea>
                </div>
                <label class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/10 rounded-xl cursor-pointer border border-blue-100 dark:border-blue-900/50">
                    <input type="checkbox" wire:model="is_qc_passed" class="checkbox checkbox-primary" />
                    <span class="text-sm font-black uppercase tracking-tight text-blue-700 dark:text-blue-400">Quality Control (QC) Passed</span>
                </label>
            </div>

            <div class="modal-action gap-3 mt-8">
                <button wire:click="$set('showConfirmModal', false)" class="btn btn-ghost font-black uppercase text-xs">Cancel</button>
                <button wire:click="confirmReceipt" class="btn btn-primary px-8 font-black uppercase text-xs shadow-lg border-none">Add to Stock</button>
            </div>
        </div>
    </dialog>

    {{-- RM Modal --}}
    <dialog class="modal @if($showRmModal) modal-open @endif">
        <div class="modal-box w-full max-w-lg rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-2xl">
            <h3 class="font-black text-2xl mb-2 uppercase tracking-tighter">Raw Material GRN</h3>
            <p class="text-sm text-zinc-500 mb-6">Confirm and authorize arrivals of supplier materials.</p>
            
            <div class="bg-emerald-50 dark:bg-emerald-900/10 p-5 rounded-2xl border border-emerald-100 dark:border-emerald-800/50 mb-6 flex justify-between items-center">
                <div>
                    <span class="block text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">Expected Amount</span>
                    <span class="text-3xl font-black text-emerald-700 tracking-tighter">{{ number_format($rm_expected_qty, 2) }}</span>
                </div>
                <div class="text-right">
                    <span class="block text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">Material</span>
                    <span class="text-base font-black text-zinc-600 dark:text-zinc-300 uppercase">{{ $rm_name }}</span>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Physical Quantity Received</label>
                    <input wire:model="rm_received_qty" type="number" step="0.001" class="input input-bordered w-full font-black text-3xl h-18 text-emerald-600 bg-zinc-50 dark:bg-zinc-800 border-emerald-200" />
                    @error('rm_received_qty') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Internal Notes / Location</label>
                    <textarea wire:model="rm_notes" class="textarea textarea-bordered w-full h-24 font-medium" placeholder="Any weight variations..."></textarea>
                </div>
            </div>

            <div class="modal-action gap-3 mt-10">
                <button wire:click="$set('showRmModal', false)" class="btn btn-ghost font-black uppercase text-xs">Cancel</button>
                <button wire:click="confirmRmReceipt" class="btn btn-success px-10 font-black uppercase text-xs text-white border-none shadow-lg">Authorize Arrival</button>
            </div>
        </div>
    </dialog>
</div>
