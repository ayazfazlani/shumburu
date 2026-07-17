<div class="p-6 md:p-8 bg-zinc-50/50 dark:bg-zinc-950 min-h-screen">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8 print:hidden">
        <div>
            <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Production Planning</h2>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400 mt-1 uppercase tracking-wider">Engineering
                Execution & Material Control</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex bg-zinc-100 dark:bg-zinc-900 p-1 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <button wire:click="$set('activeFilter', 'active')"
                    class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $activeFilter === 'active' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}">Active</button>
                <button wire:click="$set('activeFilter', 'historical')"
                    class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $activeFilter === 'historical' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-sm' : 'text-zinc-400 hover:text-zinc-600' }}">Historical</button>
            </div>
            <div
                class="flex items-center gap-3 bg-white dark:bg-zinc-900 p-1.5 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="px-4 py-2 text-center border-r border-zinc-100 dark:border-zinc-800">
                    <span class="block text-[10px] font-bold text-zinc-400 uppercase leading-none mb-1">Active
                        Batches</span>
                    <span
                        class="text-lg font-black text-zinc-900 dark:text-white leading-none">{{ $ordersWithDemands->count() }}</span>
                </div>
                <div class="px-4 py-2 text-center">
                    <span class="block text-[10px] font-bold text-zinc-400 uppercase leading-none mb-1">Materials
                        Planned</span>
                    <span
                        class="text-lg font-black text-emerald-500 leading-none">{{ count($globalMaterialSummary) }}</span>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div
            class="alert alert-success bg-emerald-500 text-white border-0 shadow-lg shadow-emerald-500/20 mb-8 animate-in slide-in-from-top-4 duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-bold uppercase text-xs tracking-widest">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-error mb-8 animate-in slide-in-from-top-4 duration-300">
            <span class="font-bold text-xs tracking-widest">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- ── Main Area ──────────────────────────────────────── --}}
        <div class="lg:col-span-8 space-y-8">

            @if($viewingOrder)
                {{-- ══ ORDER DETAIL: Planning View ══ --}}
                <div class="animate-in fade-in slide-in-from-left-4 duration-500">
                    {{-- Back nav --}}
                    <div class="mb-4 flex items-center justify-between px-2">
                        <button wire:click="backToList"
                            class="group flex items-center gap-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                            <div
                                class="w-8 h-8 rounded-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center group-hover:border-zinc-300 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-widest">Back to Queue</span>
                        </button>
                        <div class="flex items-center gap-2">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-500/10 text-amber-600 border border-amber-500/20',
                                    'pending_production' => 'bg-blue-500/10 text-blue-600 border border-blue-500/20',
                                    'approved' => 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20',
                                ];
                                $sc = $statusColors[$viewingOrder->status] ?? 'bg-zinc-100 text-zinc-500';
                            @endphp
                            <span
                                class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $sc }}">{{ ucfirst(str_replace('_', ' ', $viewingOrder->status)) }}</span>
                        </div>
                    </div>

                    <div id="planning-report"
                        class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden">

                        {{-- Report Header --}}
                        <div
                            class="p-8 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-5">
                                <div
                                    class="w-14 h-14 bg-zinc-900 dark:bg-white rounded-2xl flex items-center justify-center text-white dark:text-black shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                        Order #{{ $viewingOrder->order_number }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="text-xs font-bold text-zinc-500 uppercase tracking-widest">{{ $viewingOrder->customer->name ?? 'N/A' }}</span>
                                        <span class="w-1 h-1 rounded-full bg-zinc-300"></span>
                                        <span
                                            class="text-xs font-medium text-zinc-400">{{ now()->format('D, d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 w-full md:w-auto print:hidden">
                                <button onclick="window.print()"
                                    class="btn btn-ghost bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 flex-1 md:flex-none h-11 px-6 rounded-xl font-bold uppercase text-[10px] tracking-widest transition-all">
                                    Download PDF
                                </button>
                                @if(!($viewingOrder->plan && $viewingOrder->plan->status === 'approved'))
                                    <button wire:click="savePlan"
                                        class="btn btn-ghost bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 flex-1 md:flex-none h-11 px-6 rounded-xl font-bold uppercase text-[10px] tracking-widest transition-all">
                                        Save Draft
                                    </button>
                                    <button wire:click="approvePlan"
                                        class="btn btn-primary bg-zinc-900 dark:bg-white dark:text-black border-0 flex-1 md:flex-none h-11 px-8 rounded-xl font-black uppercase text-[10px] tracking-[0.2em] shadow-lg transition-all">
                                        Release to Floor →
                                    </button>
                                @else
                                    <div
                                        class="px-6 py-2.5 bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                        ✓ Plan Approved & Released
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Schedule --}}
                        <div class="p-8 bg-zinc-50/30 dark:bg-zinc-800/20 border-b border-zinc-100 dark:border-zinc-800">
                            <h4 class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-5">Production
                                Schedule</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] ml-1">Production
                                        Line</label>
                                    <select wire:model="productionLineId" {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'disabled' : '' }}
                                        class="select select-bordered w-full h-12 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-2xl font-bold text-sm {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'opacity-60' : '' }}">
                                        <option value="">Choose Line</option>
                                        @foreach($productionLines as $line)
                                            <option value="{{ $line->id }}">{{ $line->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] ml-1">Planned
                                        Start</label>
                                    <input type="datetime-local" wire:model="startDate" {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'disabled' : '' }}
                                        class="input input-bordered w-full h-12 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-2xl font-bold text-sm {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'opacity-60' : '' }}">
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] ml-1 text-emerald-500">Target
                                        End</label>
                                    <input type="datetime-local" wire:model="endDate" {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'disabled' : '' }}
                                        class="input input-bordered w-full h-12 bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 rounded-2xl font-bold text-sm {{ ($viewingOrder->plan && $viewingOrder->plan->status === 'approved') ? 'opacity-60' : '' }}">
                                </div>
                            </div>
                        </div>

                        {{-- ── SECTION 1: What to Produce (Order Items) ── --}}
                        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800">
                            <h4
                                class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-[0.2em] mb-6 flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-zinc-900 dark:bg-white"></div>
                                Production Demand — What to Make
                            </h4>

                            @if($orderItems->isEmpty())
                                <div
                                    class="p-10 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl">
                                    <p class="text-xs font-black text-zinc-300 uppercase tracking-widest">No order items found
                                        for this production order.</p>
                                    <p class="text-[10px] text-zinc-400 mt-2">Add items via Sales → Orders.</p>
                                </div>
                            @else
                                <div
                                    class="overflow-x-auto bg-zinc-50/50 dark:bg-zinc-800/10 rounded-3xl border border-zinc-100 dark:border-zinc-800 p-1">
                                    <table class="table w-full border-separate border-spacing-y-2">
                                        <thead class="text-[10px] uppercase font-black text-zinc-400 tracking-widest">
                                            <tr>
                                                <th class="bg-transparent pl-6 py-3">Product</th>
                                                <th class="bg-transparent text-center">OD / Spec</th>
                                                <th class="bg-transparent text-center">SDR</th>
                                                <th class="bg-transparent text-center">Quantity</th>
                                                <th class="bg-transparent text-center">Unit Price</th>
                                                <th class="bg-transparent text-right pr-6">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-sm">
                                            @foreach($orderItems as $item)
                                                <tr
                                                    class="bg-white dark:bg-zinc-900/50 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-all shadow-sm rounded-2xl">
                                                    <td class="pl-6 py-4">
                                                        <div
                                                            class="font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                                            {{ $item->product->name ?? 'Unknown Product' }}
                                                        </div>
                                                        @if($item->pn)
                                                            <div class="text-[10px] font-bold text-zinc-400 uppercase mt-0.5">PN:
                                                                {{ $item->pn }}</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($item->od)
                                                            <span
                                                                class="inline-flex items-center px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-lg text-[10px] font-black uppercase">{{ $item->od }}
                                                                mm</span>
                                                        @else
                                                            <span class="text-[10px] text-zinc-300 italic font-bold uppercase">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($item->sdr)
                                                            <span
                                                                class="inline-flex items-center px-3 py-1 bg-blue-500/5 text-blue-600 rounded-lg text-[10px] font-black uppercase border border-blue-500/10">SDR
                                                                {{ $item->sdr }}</span>
                                                        @else
                                                            <span class="text-[10px] text-zinc-300 italic font-bold uppercase">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="text-base font-black text-zinc-900 dark:text-white">{{ number_format($item->quantity, 0) }}</span>
                                                        <span
                                                            class="text-[9px] font-black text-zinc-400 uppercase ml-1">{{ $item->unit }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span
                                                            class="text-sm font-bold text-zinc-600 dark:text-zinc-400">{{ number_format($item->unit_price, 2) }}</span>
                                                    </td>
                                                    <td class="pr-6 text-right">
                                                        <span
                                                            class="text-base font-black text-zinc-900 dark:text-white">{{ number_format($item->total_price, 2) }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="text-[10px] uppercase font-black text-zinc-400 tracking-widest">
                                            <tr>
                                                <td colspan="3"
                                                    class="pl-6 py-3 text-zinc-400 uppercase text-[10px] font-black tracking-widest">
                                                    Totals</td>
                                                <td class="text-center">
                                                    <span
                                                        class="font-black text-zinc-900 dark:text-white">{{ number_format($orderItems->sum('quantity'), 0) }}</span>
                                                    <span class="text-[9px] text-zinc-400 ml-0.5">units</span>
                                                </td>
                                                <td></td>
                                                <td class="pr-6 text-right">
                                                    <span
                                                        class="font-black text-zinc-900 dark:text-white">{{ number_format($orderItems->sum('total_price'), 2) }}</span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- ── SECTION 2: Raw Material Plan ── --}}
                        <div class="p-8">
                            <div class="flex justify-between items-center mb-6">
                                <h4
                                    class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-[0.2em] flex items-center gap-3">
                                    <div class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]">
                                    </div>
                                    Raw Material Requirements
                                </h4>
                                @if(!($viewingOrder->plan && $viewingOrder->plan->status === 'approved'))
                                    <button wire:click="openAddMaterialModal"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-zinc-900 dark:bg-white dark:text-white text-black rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-zinc-900/20 hover:scale-105 transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        Add Raw Material
                                    </button>
                                @endif
                            </div>

                            @if(count($aggregatedMaterialSummary) > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    @foreach($aggregatedMaterialSummary as $matId => $summary)
                                        @php
                                            $stockOk = $summary['in_stock'] >= $summary['total_quantity'];
                                            $coverage = $summary['total_quantity'] > 0
                                                ? min(100, ($summary['in_stock'] / $summary['total_quantity']) * 100)
                                                : 0;
                                        @endphp
                                        <div
                                            class="group p-5 bg-zinc-50 dark:bg-zinc-800/30 rounded-3xl border {{ $stockOk ? 'border-emerald-100 dark:border-emerald-900/30' : 'border-red-100 dark:border-red-900/30' }} shadow-sm transition-all hover:shadow-md relative">
                                            <div class="flex justify-between items-start mb-4">
                                                <div>
                                                    <span
                                                        class="block text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Raw
                                                        Material</span>
                                                    <h5
                                                        class="text-sm font-black text-zinc-900 dark:text-zinc-100 uppercase tracking-tight">
                                                        {{ $summary['name'] }}</h5>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    @if(!($viewingOrder->plan && $viewingOrder->plan->status === 'approved'))
                                                        <button wire:click="openEditMaterialModal({{ $summary['id'] }})"
                                                            class="w-7 h-7 rounded-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-400 hover:text-blue-600 hover:border-blue-300 transition-all">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                                viewBox="0 0 20 20" fill="currentColor">
                                                                <path
                                                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                            </svg>
                                                        </button>
                                                        <button wire:click="deletePlanItem({{ $summary['id'] }})"
                                                            wire:confirm="Remove this material from the plan?"
                                                            class="w-7 h-7 rounded-lg bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center text-zinc-400 hover:text-red-600 hover:border-red-300 transition-all">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5"
                                                                viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    @if($stockOk)
                                                        <span
                                                            class="px-2 py-1 bg-emerald-500/10 text-emerald-600 rounded-lg text-[9px] font-black uppercase border border-emerald-500/20">In
                                                            Stock ✓</span>
                                                    @else
                                                        <span
                                                            class="px-2 py-1 bg-red-500/10 text-red-600 rounded-lg text-[9px] font-black uppercase border border-red-500/20">Stock
                                                            Low ⚠</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-3 gap-3 mb-4">
                                                <div
                                                    class="text-center p-2.5 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-100 dark:border-zinc-800">
                                                    <span class="block text-[8px] font-black text-zinc-400 uppercase mb-1">In
                                                        Stock</span>
                                                    <span
                                                        class="text-sm font-black text-zinc-900 dark:text-white tabular-nums">{{ number_format($summary['in_stock'], 1) }}</span>
                                                    <span
                                                        class="block text-[8px] text-zinc-400 uppercase">{{ $summary['unit'] }}</span>
                                                </div>
                                                <div class="text-center p-2.5 bg-blue-500/5 rounded-xl border border-blue-500/10">
                                                    <span
                                                        class="block text-[8px] font-black text-blue-400 uppercase mb-1">Required</span>
                                                    <span
                                                        class="text-sm font-black text-zinc-900 dark:text-white tabular-nums">{{ number_format($summary['total_quantity'], 1) }}</span>
                                                    <span
                                                        class="block text-[8px] text-zinc-400 uppercase">{{ $summary['unit'] }}</span>
                                                </div>
                                                <div
                                                    class="text-center p-2.5 {{ $stockOk ? 'bg-emerald-500/5 border border-emerald-500/10' : 'bg-red-500/5 border border-red-500/10' }} rounded-xl">
                                                    <span
                                                        class="block text-[8px] font-black {{ $stockOk ? 'text-emerald-500' : 'text-red-500' }} uppercase mb-1">Shortage</span>
                                                    <span
                                                        class="text-sm font-black {{ $stockOk ? 'text-emerald-600' : 'text-red-600' }} tabular-nums">
                                                        {{ $stockOk ? '0.0' : number_format($summary['total_quantity'] - $summary['in_stock'], 1) }}
                                                    </span>
                                                    <span
                                                        class="block text-[8px] text-zinc-400 uppercase">{{ $summary['unit'] }}</span>
                                                </div>
                                            </div>

                                            {{-- Coverage progress bar --}}
                                            <div>
                                                <div
                                                    class="flex justify-between text-[9px] font-black text-zinc-400 uppercase mb-1.5">
                                                    <span>Stock Coverage</span>
                                                    <span>{{ round($coverage) }}%</span>
                                                </div>
                                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 overflow-hidden">
                                                    <div class="h-full rounded-full transition-all duration-700 {{ $stockOk ? 'bg-emerald-500' : 'bg-red-500' }}"
                                                        style="width: {{ $coverage }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div
                                    class="mt-6 flex items-center gap-3 bg-zinc-100/80 dark:bg-white/5 p-4 rounded-2xl border border-zinc-200/50 dark:border-white/5">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p
                                        class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 leading-relaxed uppercase tracking-widest">
                                        These quantities are planning projections. Daily floor releases are managed by the Plant
                                        Manager.
                                    </p>
                                </div>
                            @else
                                <div
                                    class="p-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-3xl">
                                    <div
                                        class="w-16 h-16 bg-zinc-100 dark:bg-zinc-800 rounded-2xl flex items-center justify-center text-zinc-300 mx-auto mb-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <h5 class="text-xs font-black text-zinc-400 uppercase tracking-[0.2em]">No Raw Materials
                                        Planned Yet</h5>
                                    <p class="text-[10px] text-zinc-400 mt-2 uppercase tracking-widest">Click "Add Raw Material"
                                        above to plan the materials needed to fulfil this order.</p>
                                </div>
                            @endif
                        </div>

                        {{-- Footer --}}
                        <div class="p-8 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div
                                class="flex flex-col md:flex-row justify-between items-center md:items-end gap-10 opacity-60">
                                <div
                                    class="w-full md:w-60 border-t-2 border-zinc-200 dark:border-zinc-700 pt-3 text-center">
                                    <span class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em]">Authorized
                                        Planner</span>
                                </div>
                                <div
                                    class="hidden md:block text-[9px] font-black text-zinc-300 dark:text-zinc-600 uppercase tracking-[0.5em]">
                                    SHUMBURU • ERP • {{ now()->year }}
                                </div>
                                <div
                                    class="w-full md:w-60 border-t-2 border-zinc-200 dark:border-zinc-700 pt-3 text-center">
                                    <span class="text-[9px] font-black text-zinc-400 uppercase tracking-[0.2em]">Operations
                                        Approval</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                {{-- ══ LIST VIEW ══ --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @forelse ($ordersWithDemands as $order)
                        <div class="group bg-white dark:bg-zinc-900 rounded-3xl border {{ ($order->plan && $order->plan->status === 'approved') ? 'border-emerald-200 dark:border-emerald-900/50' : 'border-zinc-200 dark:border-zinc-800' }} p-6 shadow-sm hover:shadow-2xl hover:shadow-zinc-200/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden relative"
                            wire:click="selectOrder({{ $order->id }})">
                            <div
                                class="absolute -right-8 -top-8 w-24 h-24 {{ ($order->plan && $order->plan->status === 'approved') ? 'bg-emerald-500/5' : 'bg-blue-500/5' }} rounded-full blur-3xl">
                            </div>

                            <div class="flex justify-between items-start mb-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 {{ ($order->plan && $order->plan->status === 'approved') ? 'bg-emerald-500/10 text-emerald-600' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' }} rounded-2xl flex items-center justify-center font-black group-hover:bg-zinc-900 group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-black transition-all">
                                        #
                                    </div>
                                    <div>
                                        <h4 class="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                            Order #{{ $order->order_number }}</h4>
                                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mt-0.5">
                                            {{ $order->customer->name ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1.5">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-amber-500/10 text-amber-600 border border-amber-500/20',
                                            'pending_production' => 'bg-blue-500/10 text-blue-600 border border-blue-500/20',
                                            'approved' => 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20',
                                            'in_production' => 'bg-indigo-500/10 text-indigo-600 border border-indigo-500/20',
                                            'completed' => 'bg-zinc-200 text-zinc-600 border border-zinc-300',
                                        ];
                                        $sc2 = $statusColors[$order->status] ?? 'bg-zinc-100 text-zinc-500';
                                    @endphp
                                    <div
                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $sc2 }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Quick stats --}}
                            <div class="grid grid-cols-3 gap-3 mb-5">
                                <div
                                    class="text-center p-2.5 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-100 dark:border-zinc-800">
                                    <span class="block text-[8px] font-black text-zinc-400 uppercase mb-1">Items</span>
                                    <span
                                        class="text-base font-black text-zinc-900 dark:text-white">{{ $order->items->count() }}</span>
                                </div>
                                <div
                                    class="text-center p-2.5 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-100 dark:border-zinc-800">
                                    <span class="block text-[8px] font-black text-zinc-400 uppercase mb-1">Qty</span>
                                    <span
                                        class="text-base font-black text-zinc-900 dark:text-white">{{ number_format($order->items->sum('quantity'), 0) }}</span>
                                </div>
                                <div
                                    class="text-center p-2.5 {{ $order->plan ? 'bg-blue-500/5 border border-blue-500/10' : 'bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-zinc-800' }} rounded-xl">
                                    <span
                                        class="block text-[8px] font-black {{ $order->plan ? 'text-blue-500' : 'text-zinc-400' }} uppercase mb-1">Materials</span>
                                    <span
                                        class="text-base font-black {{ $order->plan ? 'text-blue-600' : 'text-zinc-400' }}">{{ $order->plan ? $order->plan->items->count() : '—' }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between border-t border-zinc-50 dark:border-zinc-800 pt-4">
                                <div class="flex -space-x-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-zinc-200 dark:bg-zinc-800 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-[7px] font-bold text-zinc-500">
                                        M</div>
                                    <div
                                        class="w-6 h-6 rounded-full bg-blue-500 border-2 border-white dark:border-zinc-900 flex items-center justify-center text-[7px] font-bold text-white">
                                        P</div>
                                </div>
                                @if($order->plan && $order->plan->status === 'approved')
                                    <span
                                        class="text-[10px] font-black uppercase text-emerald-600 tracking-[0.2em] group-hover:translate-x-1 transition-transform">✓
                                        View Approved Plan →</span>
                                @else
                                    <span
                                        class="text-[10px] font-black uppercase text-blue-600 tracking-[0.2em] group-hover:translate-x-1 transition-transform">Plan
                                        This Order →</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div
                            class="md:col-span-2 p-16 text-center bg-zinc-100/50 dark:bg-zinc-800/20 border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[2.5rem]">
                            <div
                                class="w-20 h-20 bg-zinc-100 dark:bg-zinc-800 rounded-3xl flex items-center justify-center text-zinc-300 mx-auto mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <h5 class="text-sm font-black text-zinc-400 uppercase tracking-[0.3em] mb-2">Production Queue Empty
                            </h5>
                            <p class="text-xs text-zinc-400 uppercase tracking-widest">Awaiting new production orders from
                                sales.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Global Material Summary --}}
                <div
                    class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm mt-12">
                    <div
                        class="p-8 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <h3 class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <span class="text-md font-black text-zinc-900 dark:text-white uppercase tracking-[0.2em]">Global
                                Planned Materials</span>
                        </h3>
                        <span
                            class="px-5 py-2 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-[10px] font-black uppercase text-zinc-500 tracking-widest">{{ count($globalMaterialSummary) }}
                            Active Materials</span>
                    </div>

                    <div class="p-8">
                        @if(count($globalMaterialSummary) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($globalMaterialSummary as $summary)
                                    @php $gs = $summary['in_stock'] >= $summary['total_quantity']; @endphp
                                    <div
                                        class="p-6 bg-zinc-50 dark:bg-zinc-800/30 rounded-3xl border {{ $gs ? 'border-zinc-100 dark:border-zinc-800' : 'border-red-100 dark:border-red-900/30' }} shadow-sm flex flex-col justify-between">
                                        <div class="flex justify-between items-start mb-5">
                                            <div>
                                                <span
                                                    class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1 block">Raw
                                                    Material</span>
                                                <h6
                                                    class="text-sm font-black text-zinc-900 dark:text-zinc-200 uppercase tracking-tight">
                                                    {{ $summary['name'] }}</h6>
                                            </div>
                                            <div class="flex items-center gap-1.5">
                                                <div
                                                    class="w-2.5 h-2.5 rounded-full {{ $gs ? 'bg-emerald-500' : 'bg-red-500 animate-pulse' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-end">
                                            <div>
                                                <div class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">In
                                                    Warehouse</div>
                                                <span
                                                    class="text-lg font-black text-zinc-900 dark:text-zinc-300 tabular-nums">{{ number_format($summary['in_stock'], 1) }}
                                                    <span class="text-[10px] text-zinc-400">{{ $summary['unit'] }}</span></span>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-1">
                                                    Total Required</div>
                                                <div
                                                    class="text-2xl font-black text-zinc-900 dark:text-white leading-none tabular-nums">
                                                    {{ number_format($summary['total_quantity'], 1) }}
                                                    <span
                                                        class="text-[10px] font-black text-zinc-400 ml-0.5 uppercase">{{ $summary['unit'] }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="text-center py-10 text-zinc-300 font-black uppercase text-[11px] tracking-[0.4em] opacity-50">
                                No material requirements planned yet.
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- ── Sidebar ──────────────────────────────────────────────────────── --}}
        <div class="lg:col-span-4 space-y-8 print:hidden">
            {{-- Downtime Monitor --}}
            <div
                class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div
                    class="p-6 border-b border-zinc-100 dark:border-zinc-800 bg-red-500/5 flex justify-between items-center">
                    <h3 class="font-black text-xs uppercase tracking-[0.2em] text-red-600 flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-600 animate-ping"></div>
                        Equipment Downtime Log
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($recentDowntime as $record)
                        <div
                            class="p-4 bg-zinc-50 dark:bg-zinc-800 rounded-3xl border border-zinc-100 dark:border-zinc-700">
                            <div class="font-black text-sm text-zinc-900 dark:text-zinc-100 uppercase tracking-tight">
                                {{ $record->reason }}</div>
                        </div>
                    @empty
                        <div class="text-center py-10 flex flex-col items-center">
                            <div
                                class="w-16 h-16 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-500 mb-3 animate-pulse">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h5 class="text-xs font-black text-emerald-600 uppercase tracking-widest">All Systems Nominal
                            </h5>
                            <p class="text-[10px] text-zinc-400 mt-1 uppercase tracking-[0.2em]">No downtime logged ✅</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Planning Readiness --}}
            <div
                class="bg-zinc-900 text-white rounded-[3rem] p-10 shadow-2xl shadow-zinc-900/40 relative overflow-hidden group border border-zinc-800">
                <div
                    class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-600/10 rounded-full blur-[100px] group-hover:bg-blue-600/20 transition-all duration-1000">
                </div>
                <div class="relative z-10">
                    <h4 class="text-zinc-500 text-xs font-black uppercase tracking-[0.4em] mb-10">System Readiness</h4>
                    <div class="space-y-8">
                        <div class="flex justify-between items-end border-b border-zinc-800 pb-3">
                            <span class="text-sm font-bold text-zinc-400 uppercase tracking-widest">Active Queue</span>
                            <span
                                class="text-3xl font-black tabular-nums leading-none">{{ $ordersWithDemands->count() }}</span>
                        </div>
                        <div class="flex justify-between items-end border-b border-zinc-800 pb-3">
                            <span class="text-sm font-bold text-zinc-400 uppercase tracking-widest">Materials
                                Catalogued</span>
                            <span
                                class="text-3xl font-black text-blue-400 tabular-nums leading-none">{{ count($globalMaterialSummary) }}</span>
                        </div>
                        <div class="flex justify-between items-end border-b border-zinc-800 pb-3">
                            <span class="text-sm font-bold text-zinc-400 uppercase tracking-widest">Plans
                                Approved</span>
                            <span class="text-3xl font-black text-emerald-400 tabular-nums leading-none">
                                {{ $ordersWithDemands->where('status', 'approved')->count() }}
                            </span>
                        </div>
                        <p class="text-[10px] font-bold text-zinc-600 leading-relaxed uppercase tracking-widest pt-2">
                            Planning precision directly impacts factory floor throughput and overall material wastage.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Add / Edit Raw Material Modal ── --}}
    @if($showMaterialModal)
        <div class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-[100] p-6 animate-in fade-in duration-300"
            wire:click.self="$set('showMaterialModal', false)">
            <div
                class="bg-white dark:bg-zinc-900 rounded-[3rem] p-10 w-full max-w-lg shadow-2xl scale-100 animate-in zoom-in-95 duration-300 relative overflow-hidden border border-zinc-100 dark:border-zinc-800">
                <div class="absolute -top-32 -left-32 w-64 h-64 bg-blue-500/5 rounded-full blur-[80px]"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-6 mb-10">
                        <div
                            class="w-16 h-16 bg-blue-600 text-white rounded-3xl flex items-center justify-center shadow-xl shadow-blue-600/20">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5 3a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2H5zm0 2h10v7h-2l-1 2H8l-1-2H5V5z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                {{ $editingPlanItemId ? 'Edit Material' : 'Add Raw Material' }}
                            </h3>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1">Plan the quantity
                                required for this order</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-3 ml-1">Raw
                                Material</label>
                            <select wire:model="materialId"
                                class="select select-bordered w-full h-14 bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-2xl font-black text-sm {{ $editingPlanItemId ? 'opacity-60' : '' }}"
                                {{ $editingPlanItemId ? 'disabled' : '' }}>
                                <option value="">Select a raw material...</option>
                                @foreach($rawMaterials as $material)
                                    <option value="{{ $material->id }}">
                                        {{ $material->name }} — {{ $material->unit }} (Stock:
                                        {{ number_format($material->quantity, 0) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('materialId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-[0.2em] mb-3 ml-1">
                                Quantity Required
                            </label>
                            <div class="relative">
                                <input type="number" wire:model="materialQty"
                                    class="input input-bordered w-full h-16 bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 text-3xl font-black rounded-2xl tabular-nums px-6"
                                    step="0.01" min="0.01" placeholder="0.0">
                            </div>
                            @error('materialQty') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                            <p
                                class="text-[9px] font-bold text-zinc-400 mt-3 bg-zinc-50 dark:bg-zinc-800 p-3 rounded-xl uppercase tracking-widest leading-relaxed border border-zinc-200/50">
                                ⚠️ These quantities represent total order requirements. If this material already exists in
                                the plan, quantities will be merged.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-10">
                        <button wire:click="saveMaterialItem"
                            class="btn btn-primary flex-1 h-14 bg-zinc-900 border-0 text-white font-black uppercase text-xs tracking-widest rounded-2xl shadow-xl">
                            {{ $editingPlanItemId ? 'Update Material' : 'Add to Plan' }}
                        </button>
                        <button wire:click="$set('showMaterialModal', false)"
                            class="btn btn-ghost h-14 px-8 font-black uppercase text-xs tracking-widest rounded-2xl">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        ::-webkit-calendar-picker-indicator {
            opacity: 0.4;
            filter: invert(0.5);
            cursor: pointer;
        }

        .dark ::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #planning-report,
            #planning-report * {
                visibility: visible;
            }

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

            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</div>