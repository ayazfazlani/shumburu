<div class="p-6 md:p-8 bg-zinc-50/50 dark:bg-zinc-950 min-h-screen">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 print:hidden">
        <div>
            <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Production Planning</h2>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mt-1 uppercase tracking-wider">Engineering Execution & Material Control</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex bg-zinc-100 dark:bg-zinc-900 p-1 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <button wire:click="$set('activeFilter', 'active')" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $activeFilter === 'active' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}">Active</button>
                <button wire:click="$set('activeFilter', 'historical')" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $activeFilter === 'historical' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}">Historical</button>
            </div>
            <div class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-1.5 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="px-4 py-2 text-center border-r border-zinc-100 dark:border-zinc-800">
                    <span class="block text-[10px] font-bold text-zinc-400 uppercase leading-none mb-1">Active Batches</span>
                    <span class="text-lg font-black text-zinc-900 dark:text-white leading-none">{{ $ordersWithDemands->count() }}</span>
                </div>
                <div class="px-4 py-2 text-center">
                    <span class="block text-[10px] font-bold text-zinc-400 uppercase leading-none mb-1">Stock Readiness</span>
                    <span class="text-lg font-black text-emerald-500 leading-none">94%</span>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success bg-emerald-500 text-white border-0 shadow-lg shadow-emerald-500/20 mb-8 animate-in slide-in-from-top-4 duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="font-bold uppercase text-xs tracking-widest">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Area (8 cols) -->
        <div class="lg:col-span-8 space-y-8">

            @if($viewingOrder)
                {{-- ══════════════════════════════════════════════════════════
                     ORDER DETAIL: Planning Report
                ══════════════════════════════════════════════════════════ --}}
                <div class="animate-in fade-in slide-in-from-left-4 duration-500">
                    <div class="mb-4 flex items-center justify-between px-2">
                        <button wire:click="backToList" class="group flex items-center gap-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                            <div class="w-8 h-8 rounded-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center group-hover:border-zinc-300 dark:group-hover:border-zinc-700 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest">Back to Queue</span>
                        </button>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 bg-blue-500/10 text-blue-600 rounded-full text-[10px] font-black uppercase">{{ count($selectedOrderDemands) }} Items in Batch</span>
                        </div>
                    </div>

                    <div id="planning-report" class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-xl shadow-zinc-200/50 dark:shadow-none overflow-hidden mb-8">
                        {{-- Report Header --}}
                        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 bg-zinc-900 dark:bg-white rounded-2xl flex items-center justify-center text-white dark:text-black shadow-lg shadow-zinc-900/10 dark:shadow-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">Report: #{{ $viewingOrder->order_number }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs font-bold text-zinc-500 uppercase tracking-widest">{{ $viewingOrder->customer->name ?? 'N/A' }}</span>
                                        <span class="w-1 h-1 rounded-full bg-zinc-300"></span>
                                        <span class="text-xs font-medium text-zinc-400">{{ now()->format('D, d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 w-full md:w-auto print:hidden">
                                <button onclick="window.print()" class="btn btn-ghost bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex-1 md:flex-none h-11 px-6 rounded-xl font-bold uppercase text-[10px] tracking-widest transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download PDF
                                </button>
                                <button wire:click="savePlan" class="btn btn-ghost bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex-1 md:flex-none h-11 px-6 rounded-xl font-bold uppercase text-[10px] tracking-widest transition-all">
                                    Save Draft
                                </button>
                                <button wire:click="approvePlan" class="btn btn-primary bg-zinc-900 dark:bg-white dark:text-black hover:bg-zinc-800 dark:hover:bg-zinc-100 border-0 flex-1 md:flex-none h-11 px-8 rounded-xl font-black uppercase text-[10px] tracking-[0.2em] shadow-lg shadow-zinc-900/20 dark:shadow-none transition-all">
                                    Release to Floor
                                </button>
                            </div>
                        </div>

                        {{-- Schedule Section --}}
                        <div class="p-8 bg-zinc-50/30 dark:bg-zinc-800/20 border-b border-zinc-100 dark:border-zinc-800">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] ml-1">Production Line</label>
                                    <select wire:model="productionLineId" class="select select-bordered w-full h-12 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 focus:ring-4 focus:ring-zinc-900/5 dark:focus:ring-white/5 rounded-2xl font-bold text-sm">
                                        <option value="">Choose Assembly Line</option>
                                        @foreach($productionLines as $line)
                                            <option value="{{ $line->id }}">{{ $line->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] ml-1 text-center md:text-left">Planned Start</label>
                                    <div class="relative">
                                        <input type="datetime-local" wire:model="startDate" class="input input-bordered w-full h-12 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 focus:ring-4 focus:ring-zinc-900/5 dark:focus:ring-white/5 rounded-2xl font-bold text-sm">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] ml-1 text-center md:text-left text-emerald-500">Target End</label>
                                    <div class="relative">
                                        <input type="datetime-local" wire:model="endDate" class="input input-bordered w-full h-12 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 focus:ring-4 focus:ring-emerald-500/10 rounded-2xl font-bold text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Line Items Table --}}
                        <div class="p-8">
                            <h4 class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-zinc-900 dark:bg-white animate-pulse"></div>
                                Batch Configuration Details
                            </h4>
                            <div class="overflow-x-auto bg-zinc-50/50 dark:bg-zinc-800/10 rounded-3xl border border-zinc-100 dark:border-zinc-800 p-1">
                                <table class="table w-full border-separate border-spacing-y-2">
                                    <thead class="text-[10px] uppercase font-black text-zinc-400 tracking-widest">
                                        <tr>
                                            <th class="bg-transparent pl-8 py-4">Assembly Profile</th>
                                            <th class="bg-transparent text-center">Spec (OD)</th>
                                            <th class="bg-transparent text-center">Batch Vol.</th>
                                            <th class="bg-transparent text-center">Status</th>
                                            <th class="bg-transparent text-right pr-8">Config</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm">
                                        @foreach ($selectedOrderDemands as $request)
                                            <tr class="bg-white dark:bg-zinc-900/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all shadow-sm rounded-2xl">
                                                <td class="pl-8 py-5">
                                                    <span class="font-black text-zinc-900 dark:text-white uppercase tracking-tight">{{ $request->product->name }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($request->orderItem && $request->orderItem->od)
                                                        <span class="inline-flex items-center px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-lg text-[10px] font-black uppercase">{{ $request->orderItem->od }} MM</span>
                                                    @else
                                                        <span class="text-[10px] text-zinc-300 italic font-bold uppercase">Standard</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="text-base font-black text-zinc-900 dark:text-white">{{ number_format($request->quantity, 0) }}</span>
                                                    <span class="text-[9px] font-black text-zinc-400 uppercase ml-1">Units</span>
                                                </td>
                                                <td class="text-center">
                                                    <select wire:change="updateStatus({{ $request->id }}, $event.target.value)"
                                                            class="select select-ghost select-xs font-black uppercase text-[9px] tracking-widest bg-zinc-50 dark:bg-zinc-800 h-8 cursor-pointer rounded-lg border-0 focus:ring-0">
                                                        <option value="pending"   {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="approved"  {{ $request->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                        <option value="scheduled" {{ $request->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                        <option value="completed" {{ $request->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                </td>
                                                <td class="pr-8 text-right print:hidden">
                                                    <button wire:click="openDemandModal({{ $request->id }})"
                                                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md shadow-blue-500/20 transition-all">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                        </svg>
                                                        Planned Mat.
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Material Requirements Section --}}
                        <div class="p-8 bg-zinc-50/50 dark:bg-zinc-800/10 border-t border-zinc-100 dark:border-zinc-800">
                            <div class="flex justify-between items-center mb-8">
                                <h4 class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-[0.2em] flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]"></div>
                                    Calculated Material Demand
                                </h4>
                                <div class="px-4 py-1.5 bg-blue-500/10 text-blue-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-blue-500/20">
                                    Forecasted vs. Available
                                </div>
                            </div>

                            @if(count($aggregatedMaterialSummary) > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach($aggregatedMaterialSummary as $matId => $summary)
                                        <div class="group p-5 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm hover:shadow-xl hover:shadow-zinc-200/50 dark:hover:shadow-none transition-all duration-300 relative overflow-hidden">
                                            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-110 transition-transform duration-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                            </div>
                                            
                                            <div class="flex justify-between items-start relative z-10">
                                                <div>
                                                    <span class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5">Requirement</span>
                                                    <h5 class="text-sm font-black text-zinc-900 dark:text-zinc-100 uppercase tracking-tight truncate leading-none mb-4">{{ $summary['name'] }}</h5>
                                                    
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-2 h-2 rounded-full shadow-sm {{ $summary['in_stock'] < $summary['total_quantity'] ? 'bg-red-500' : 'bg-emerald-500' }}"></div>
                                                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                                            Stock: <span class="text-zinc-900 dark:text-zinc-300">{{ number_format($summary['in_stock'], 1) }}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="block text-[9px] font-black text-blue-500 uppercase tracking-[0.2em] mb-1">Batch Load</span>
                                                    <div class="text-2xl font-black text-zinc-900 dark:text-white leading-none tabular-nums">
                                                        {{ number_format($summary['total_quantity'], 1) }}
                                                        <span class="text-[10px] font-bold text-zinc-400 ml-0.5 uppercase">{{ $summary['unit'] }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-8 flex items-center gap-3 bg-zinc-900/5 dark:bg-white/5 p-4 rounded-2xl border border-zinc-200/50 dark:border-white/5">
                                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 leading-relaxed uppercase tracking-widest">
                                        These values are planning projections only. Execution quantities will be released by the Plant Manager on a daily basis to optimize floor inventory.
                                    </p>
                                </div>
                            @else
                                <div class="p-16 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2.5rem]">
                                    <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-zinc-300 mx-auto mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <h5 class="text-xs font-black text-zinc-400 uppercase tracking-[0.2em]">Requirement analysis Pending</h5>
                                    <p class="text-[10px] text-zinc-400 mt-2 uppercase tracking-widest">Use the "+ Material" action in the item table above to build the plan.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Footer Section --}}
                        <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div class="flex flex-col md:flex-row justify-between items-center md:items-end gap-10 opacity-70">
                                <div class="w-full md:w-60 border-t-2 border-zinc-200 dark:border-zinc-700 pt-3 text-center">
                                    <span class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em]">Authorized Planner</span>
                                </div>
                                <div class="hidden md:block text-[9px] font-black text-zinc-300 dark:text-zinc-600 uppercase tracking-[0.5em] mb-1">
                                    SHUMBURU • ERP • {{ now()->year }}
                                </div>
                                <div class="w-full md:w-60 border-t-2 border-zinc-200 dark:border-zinc-700 pt-3 text-center">
                                    <span class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em]">Operations Approval</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                {{-- ══════════════════════════════════════════════════════════
                     LIST VIEW: Active Queue
                ══════════════════════════════════════════════════════════ --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse ($ordersWithDemands as $order)
                        <div class="group bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 p-6 shadow-sm hover:shadow-2xl hover:shadow-zinc-200/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden relative"
                             wire:click="selectOrder({{ $order->id }})">
                            <div class="absolute -right-8 -top-8 w-24 h-24 bg-blue-500/5 rounded-full blur-3xl group-hover:bg-blue-500/10 transition-colors"></div>
                            
                            <div class="flex justify-between items-start mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-zinc-900 dark:text-white font-black group-hover:bg-zinc-900 group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-black transition-all">
                                        #
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">Order #{{ $order->order_number }}</h4>
                                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-1">{{ $order->customer->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <div class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-full text-[9px] font-black uppercase tracking-widest text-zinc-500">
                                        {{ $order->pending_requests_count ?? 0 }} Demands
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mt-auto">
                                <div class="flex -space-x-2">
                                    <div class="w-7 h-7 rounded-full bg-zinc-200 dark:bg-zinc-800 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-[8px] font-bold">M</div>
                                    <div class="w-7 h-7 rounded-full bg-blue-500 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-[8px] font-bold text-white shadow-lg">P</div>
                                </div>
                                <span class="text-[10px] font-black uppercase text-blue-600 tracking-[0.2em] group-hover:translate-x-2 transition-transform">Initialize Plan →</span>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 p-16 text-center bg-zinc-100/50 dark:bg-zinc-800/20 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2.5rem]">
                            <div class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-3xl flex items-center justify-center text-zinc-300 mx-auto mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h5 class="text-sm font-black text-zinc-400 uppercase tracking-[0.3em] mb-2">Production Queue Empty</h5>
                            <p class="text-xs text-zinc-400 uppercase tracking-widest">Awaiting new production orders from sales.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Global Material Summary --}}
                <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm mt-12">
                    <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h3 class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <span class="text-md font-black text-zinc-900 dark:text-white uppercase tracking-[0.2em]">Global Planned Materials</span>
                        </h3>
                        <span class="px-5 py-2 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-[10px] font-black uppercase text-zinc-500 tracking-widest">{{ count($globalMaterialSummary) }} Active Materials</span>
                    </div>
                    
                    <div class="p-8">
                        @if(count($globalMaterialSummary) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($globalMaterialSummary as $summary)
                                    <div class="p-6 bg-zinc-50 dark:bg-zinc-800/30 rounded-3xl border border-zinc-100 dark:border-zinc-800 shadow-sm flex flex-col justify-between">
                                        <div class="flex justify-between items-start mb-6">
                                            <div class="flex flex-col">
                                                <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Catalog Name</span>
                                                <h6 class="text-sm font-black text-zinc-900 dark:text-zinc-200 uppercase tracking-tight">{{ $summary['name'] }}</h6>
                                            </div>
                                            <div class="w-8 h-8 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-xs">📦</div>
                                        </div>
                                        
                                        <div class="flex justify-between items-end">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <div class="w-2 h-2 rounded-full {{ $summary['in_stock'] < $summary['total_quantity'] ? 'bg-red-500 animate-pulse' : 'bg-emerald-500' }}"></div>
                                                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Available</span>
                                                </div>
                                                <span class="text-lg font-black text-zinc-900 dark:text-zinc-300 tabular-nums">{{ number_format($summary['in_stock'], 1) }}</span>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-1">Total Plan</div>
                                                <div class="text-2xl font-black text-zinc-900 dark:text-white leading-none tabular-nums">
                                                    {{ number_format($summary['total_quantity'], 1) }}
                                                    <span class="text-[10px] font-black text-zinc-400 ml-0.5 uppercase tracking-widest">{{ $summary['unit'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 text-zinc-300 font-black uppercase text-[11px] tracking-[0.4em] opacity-50">
                                No cumulative material requirements calculated.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        <!-- Sidebar Area (4 cols) -->
        <div class="lg:col-span-4 space-y-8 print:hidden">
            {{-- Maintenance Monitor --}}
            <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 bg-red-500/5 dark:bg-red-500/5 flex justify-between items-center">
                    <h3 class="font-black text-xs uppercase tracking-[0.2em] text-red-600 flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-600 animate-ping"></div>
                        Equipment Downtime Log
                    </h3>
                </div>
                <div class="p-6 space-y-5">
                    @forelse($recentDowntime as $record)
                        <div class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-3xl border border-zinc-100 dark:border-zinc-700 hover:border-red-200 dark:hover:border-red-900/30 transition-all group">
                            <div class="flex justify-between items-start mb-3">
                                <span class="px-3 py-1 bg-white dark:bg-zinc-900 rounded-lg shadow-sm text-[9px] font-black text-zinc-400 uppercase tracking-widest">
                                    {{ \Carbon\Carbon::parse($record->downtime_date)->format('M d, Y') }}
                                </span>
                                <div class="flex items-center gap-1.5 px-2 py-1 bg-red-500/10 text-red-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $record->duration_minutes }}m
                                </div>
                            </div>
                            <div class="font-black text-sm text-zinc-900 dark:text-zinc-100 uppercase tracking-tight">{{ $record->reason }}</div>
                            @if($record->notes)
                                <div class="text-[10px] text-zinc-500 mt-2 bg-white dark:bg-zinc-900 p-3 rounded-2xl italic leading-relaxed shadow-inner border border-zinc-100 dark:border-zinc-900">
                                    "{{ $record->notes }}"
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12 flex flex-col items-center">
                            <div class="w-20 h-20 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-500 mb-4 animate-pulse">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h5 class="text-xs font-black text-emerald-600 uppercase tracking-widest">Awaiting Logs</h5>
                            <p class="text-[10px] text-zinc-400 mt-2 uppercase tracking-[0.2em]">All Systems Nominal ✅</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Planning Readiness Card --}}
            <div class="bg-zinc-900 text-white rounded-[3rem] p-10 shadow-2xl shadow-zinc-900/40 relative overflow-hidden group">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-600/10 rounded-full blur-[100px] group-hover:bg-blue-600/20 transition-all duration-1000"></div>
                <div class="absolute -left-20 -top-20 w-60 h-60 bg-emerald-600/10 rounded-full blur-[100px] group-hover:bg-emerald-600/20 transition-all duration-1000"></div>
                
                <div class="relative z-10">
                    <h4 class="text-zinc-500 text-xs font-black uppercase tracking-[0.4em] mb-10">System Readiness</h4>
                    
                    <div class="space-y-8">
                        <div class="flex justify-between items-end border-b border-zinc-800 pb-2">
                            <span class="text-sm font-bold text-zinc-400 uppercase tracking-widest">Active Batch Processing</span>
                            <span class="text-3xl font-black tabular-nums leading-none">{{ $ordersWithDemands->count() }}</span>
                        </div>
                        <div class="flex justify-between items-end border-b border-zinc-800 pb-2">
                            <span class="text-sm font-bold text-zinc-400 uppercase tracking-widest">Planned Catalog Depth</span>
                            <span class="text-3xl font-black text-blue-400 tabular-nums leading-none">{{ count($globalMaterialSummary) }}</span>
                        </div>
                        
                        <div class="pt-6">
                            <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500 mb-2">
                                <span>Optimization Score</span>
                                <span class="text-blue-500">92%</span>
                            </div>
                            <div class="w-full bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-blue-500 h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(59,130,246,0.6)]" style="width: 92%"></div>
                            </div>
                        </div>
                        
                        <p class="text-[10px] font-bold text-zinc-500 leading-relaxed uppercase tracking-widest pt-4 opacity-60">
                            Planning precision directly impacts factory floor throughput and overall material wastage.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Material Modal --}}
    @if($showDemandModal)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-[100] p-6 animate-in fade-in duration-500"
             wire:click.self="$set('showDemandModal', false)">
            <div class="bg-white dark:bg-zinc-900 rounded-[3rem] p-10 w-full max-w-lg shadow-2xl scale-100 animate-in zoom-in-95 duration-300 relative overflow-hidden border border-zinc-100 dark:border-zinc-800">
                <div class="absolute -top-32 -left-32 w-64 h-64 bg-blue-500/5 rounded-full blur-[80px]"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-16 h-16 bg-blue-600 text-white rounded-3xl flex items-center justify-center shadow-xl shadow-blue-600/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">Material Config</h3>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1">Order Planning Layer</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-3 ml-1">Catalog Item Selection</label>
                            <select wire:model="demandMaterialId" class="select select-bordered w-full h-14 bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 focus:ring-4 focus:ring-blue-600/10 rounded-2xl font-black text-sm transition-all">
                                <option value="">Identify Raw Material...</option>
                                @foreach($rawMaterials as $material)
                                    <option value="{{ $material->id }}">
                                        {{ $material->name }} (Stock: {{ number_format($material->quantity, 0) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-3 ml-1">Allocated Volume</label>
                            <div class="relative">
                                <input type="number" wire:model="demandQuantity" class="input input-bordered w-full h-16 bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-3xl font-black focus:ring-4 focus:ring-blue-600/10 rounded-2xl tabular-nums transition-all" step="0.01" min="0.01" placeholder="0.0">
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 text-sm font-black text-zinc-400 uppercase tracking-widest">KG</div>
                            </div>
                            <p class="text-[9px] font-bold text-zinc-400 mt-4 bg-zinc-50 dark:bg-zinc-800 p-4 rounded-2xl uppercase tracking-widest leading-relaxed border border-zinc-200/50 dark:border-zinc-700">
                                ⚠️ These quantities represent total order requirement. Daily floor releases are managed subsequently at the factory level.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-12">
                        <button wire:click="submitPlannedDemand" class="btn btn-primary flex-1 h-14 bg-zinc-900 border-0 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-xl shadow-zinc-900/20">Commit to Plan</button>
                        <button wire:click="$set('showDemandModal', false)" class="btn btn-ghost h-14 px-8 font-black uppercase text-xs tracking-widest rounded-2xl">Dismiss</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23d1d5db' stroke-width='3' %3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19.5 8.25l-7.5 7.5-7.5-7.5' /%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1rem; }
        ::-webkit-calendar-picker-indicator { opacity: 0.4; filter: invert(0.5); cursor: pointer; }
        .dark ::-webkit-calendar-picker-indicator { filter: invert(1); }
        @media print {
            body * { visibility: hidden; }
            #planning-report, #planning-report * { visibility: visible; }
            #planning-report { 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%; 
                margin: 0 !important; 
                padding: 0 !important; 
                border: none !important; 
                box-shadow: none !important; 
                border-radius: 0 !important; 
            }
            .print\:hidden { display: none !important; }
        }
    </style>
</div>