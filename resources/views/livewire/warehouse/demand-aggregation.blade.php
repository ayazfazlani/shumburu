<div class="p-6">
    <div class="mb-8 px-2">
        <h2 class="text-3xl font-black text-zinc-900 dark:text-white uppercase tracking-tighter">Inventory Demand Aggregator</h2>
        <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">Combine shortages from multiple production orders into bulk procurement requests.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 px-2">
            <div class="bg-green-500 text-white py-3 px-4 rounded-xl font-bold flex items-center gap-2 shadow-lg animate-in fade-in zoom-in duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 flex justify-between items-center">
            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-zinc-500">Materials with Active Shortages</h3>
            <span class="badge badge-error h-6 px-4 font-black uppercase text-[9px] text-white border-none">{{ $aggregatedDemands->count() }} Items Needed</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                    <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <th class="bg-transparent pl-8 py-4">Raw Material</th>
                        <th class="bg-transparent text-center">In Stock</th>
                        <th class="bg-transparent text-center">Total Demand</th>
                        <th class="bg-transparent text-center">Orders Waiting</th>
                        <th class="bg-transparent text-center">Shortage</th>
                        <th class="bg-transparent text-right pr-8">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                    @forelse ($aggregatedDemands as $item)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors group">
                            <td class="pl-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-base font-black text-zinc-900 dark:text-white uppercase tracking-tight group-hover:text-blue-600 transition-colors">{{ $item['name'] }}</span>
                                    <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Base Unit: {{ $item['unit'] }}</span>
                                </div>
                            </td>
                            <td class="text-center font-bold text-zinc-500">{{ number_format($item['in_stock'], 2) }}</td>
                            <td class="text-center font-bold text-zinc-500">{{ number_format($item['total_required'], 2) }}</td>
                            <td class="text-center">
                                <span class="bg-zinc-100 dark:bg-zinc-800 px-3 py-1 rounded-full text-[10px] font-black text-zinc-600 dark:text-zinc-400 uppercase tracking-widest">
                                    {{ $item['order_count'] }} Orders
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="text-2xl font-black text-red-500 tracking-tighter">-{{ number_format($item['shortage'], 2) }}</span>
                            </td>
                            <td class="text-right pr-8">
                                <button wire:click="openPrModal({{ $item['material_id'] }}, {{ $item['shortage'] }})" 
                                    class="btn btn-primary btn-sm px-6 font-black uppercase text-[10px] shadow-md border-none">
                                    Create Bulk PR
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-32 text-center">
                                <div class="flex flex-col items-center opacity-30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span class="font-black uppercase tracking-[0.3em] text-sm">All Inventory Demands Fulfilled</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- PR Modal --}}
    <dialog class="modal @if($showPrModal) modal-open @endif">
        <div class="modal-box w-full max-w-lg rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-2xl">
            <h3 class="font-black text-2xl mb-2 uppercase tracking-tighter">Issue Requisition</h3>
            <p class="text-sm text-zinc-500 mb-8 font-medium">Combining multiple orders into one bulk procurement request for Finance.</p>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Quantity to Purchase</label>
                    <div class="relative">
                        <input wire:model="bulkQuantity" type="number" step="0.01" class="input input-bordered w-full font-black text-2xl h-16 bg-zinc-50 dark:bg-zinc-800 border-zinc-200" />
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 font-black text-zinc-400 uppercase text-xs">UNIT</div>
                    </div>
                    @error('bulkQuantity') <span class="text-red-500 text-xs mt-1 block font-bold uppercase tracking-tight">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-2">Procurement Notes</label>
                    <textarea wire:model="bulkNotes" class="textarea textarea-bordered w-full h-32 bg-zinc-50 dark:bg-zinc-800 border-zinc-200 font-medium" placeholder="E.g. urgent requirement for export orders..."></textarea>
                    @error('bulkNotes') <span class="text-red-500 text-xs mt-1 block font-bold uppercase tracking-tight">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="modal-action gap-3 mt-10">
                <button wire:click="$set('showPrModal', false)" class="btn btn-ghost font-black uppercase text-xs">Cancel</button>
                <button wire:click="raiseBulkPR" class="btn btn-primary px-8 font-black uppercase text-xs shadow-lg border-none">Send to Finance</button>
            </div>
        </div>
    </dialog>
</div>
