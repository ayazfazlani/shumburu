<div class="p-6">
    <div class="mb-8 px-2">
        <h2 class="text-3xl font-black text-zinc-900 dark:text-white uppercase tracking-tighter">Material Fulfillment Center</h2>
        <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mt-1">Review and process raw material requests incoming from planning.</p>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 px-2">
            <div class="alert alert-success shadow-lg border-none bg-green-500 text-white py-3 rounded-xl font-bold flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-5 w-5" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="space-y-8">
        @if ($selectedOrderNumber)
            <!-- Detail View: Specific Order Demands (Unified Table Theme) -->
            <div class="animate-in fade-in slide-in-from-left-4 duration-500">
                <div class="mb-4 flex items-center justify-between px-2">
                    <button wire:click="backToList" class="btn btn-ghost btn-sm gap-2 text-zinc-500 hover:text-zinc-900 font-black">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        Back to Queue
                    </button>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Order Fulfillment</span>
                        <span class="badge badge-primary h-7 px-4 font-black uppercase text-[10px]">Order #{{ $selectedOrderNumber }}</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden mb-10">
                    <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/30">
                        <h3 class="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-tighter">Material Requests for Order: #{{ $selectedOrderNumber }}</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                                <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                                    <th class="bg-transparent pl-8">Material Requirement</th>
                                    <th class="bg-transparent">Production Plan</th>
                                    <th class="bg-transparent">Required Qty</th>
                                    <th class="bg-transparent">Stock Status</th>
                                    <th class="bg-transparent pr-8 text-right">Fulfillment Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                @php
                                    $currentOrderRequests = $orderWiseRequests[$selectedOrderNumber] ?? collect();
                                @endphp
                                @foreach ($currentOrderRequests as $planId => $materialRequests)
                                    @php
                                        $firstReq = $materialRequests->first();
                                    @endphp
                                    @foreach($materialRequests as $request)
                                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                            <td class="pl-8 py-5">
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-black text-zinc-900 dark:text-white tracking-tight">{{ $request->rawMaterial->name }}</span>
                                                    <span class="text-[8px] font-black text-zinc-400 uppercase tracking-widest">Unit: {{ $request->rawMaterial->unit }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Plan #{{ $planId }}</span>
                                                    <span class="text-xs font-bold text-zinc-400 truncate max-w-[200px]">{{ $firstReq->product_name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-lg font-black text-blue-600">{{ number_format($request->quantity, 2) }}</span>
                                            </td>
                                            <td>
                                                @if($request->rawMaterial->quantity >= $request->quantity)
                                                    <div class="flex items-center gap-2 text-green-600">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                                        <span class="text-[10px] font-black uppercase">In Stock ({{ number_format($request->rawMaterial->quantity, 1) }})</span>
                                                    </div>
                                                @else
                                                    <div class="flex items-center gap-2 text-red-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                                        <span class="text-[10px] font-black uppercase tracking-tighter">Shortage: {{ number_format($request->quantity - $request->rawMaterial->quantity, 1) }}</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="pr-8 text-right">
                                                <div class="flex justify-end gap-2">
                                                    @if($request->status === 'pending' || $request->status === 'purchase_raised')
                                                        @if($request->rawMaterial->quantity >= $request->quantity)
                                                            <button 
                                                                wire:click="issueStock({{ $request->id }})" 
                                                                onclick="confirm('Authorize stock issuance for this plan?') || event.stopImmediatePropagation()"
                                                                class="btn btn-success btn-xs font-black uppercase text-[9px] px-4 text-white border-none shadow-sm"
                                                            >Issue Stock</button>
                                                        @else
                                                            <span class="badge badge-error h-6 px-4 text-[9px] font-black uppercase text-white border-none opacity-80">Out of Stock</span>
                                                            @if($request->status === 'purchase_raised')
                                                                <span class="badge badge-zinc h-6 px-4 text-[9px] font-black uppercase opacity-50">PR RAISED</span>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <span class="badge badge-ghost h-6 px-4 text-[9px] font-black uppercase opacity-50">{{ str_replace('_', ' ', $request->status) }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <!-- Grid View: List of Orders (Standard Table Theme) -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                    <h3 class="font-black text-xs uppercase tracking-[0.2em] text-zinc-500">Material Fulfillment Queue</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-zinc-50/50 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800">
                            <tr class="text-[10px] font-black uppercase tracking-widest text-zinc-400">
                                <th class="bg-transparent pl-8">Order Identification</th>
                                <th class="bg-transparent">Customer</th>
                                <th class="bg-transparent text-center">Plan Count</th>
                                <th class="bg-transparent text-right pr-8">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                            @forelse ($orderWiseRequests as $orderNumber => $plans)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="pl-8 font-black text-zinc-900 dark:text-white uppercase tracking-tighter text-xl py-6">#{{ $orderNumber }}</td>
                                    <td class="font-bold text-zinc-500 uppercase text-xs">
                                        {{ $plans->first()->first()->customer_name }}
                                    </td>
                                    <td class="text-center font-black">
                                        <span class="badge badge-zinc h-7 px-4 font-black uppercase text-[10px] bg-zinc-100 dark:bg-zinc-800 border-none">{{ $plans->count() }} Profiles</span>
                                    </td>
                                    <td class="text-right pr-8">
                                        <button wire:click="selectOrder('{{ $orderNumber }}')" class="btn btn-primary btn-sm px-6 font-black uppercase text-[10px]">Analyze Demands</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-20 text-zinc-300 font-bold uppercase tracking-widest opacity-50">No material requests in queue.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
