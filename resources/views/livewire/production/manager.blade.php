<div class="p-6 md:p-8 bg-zinc-50/50 dark:bg-zinc-950 min-h-screen">
    {{-- Top Navigation & Stats Bar --}}
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-10 print:hidden">
        <div>
            <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight uppercase">Plant Manager
                Dashboard</h2>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.4em] mt-1.5 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></span>
                Operations Status: Operational Phase {{ now()->format('H:i') }}
            </p>
        </div>

        <div
            class="flex flex-wrap items-center gap-3 bg-white dark:bg-zinc-900 p-2 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-xl shadow-zinc-200/20 dark:shadow-none">
            <button wire:click="$set('activeTab', 'plans')"
                class="flex items-center gap-2.5 px-6 py-3 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest {{ $activeTab === 'plans' ? 'bg-zinc-900 text-black shadow-lg' : 'text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                📊 Batch Queue ({{ $plannedProductionRequests->count() }})
            </button>
            <button wire:click="$set('activeTab', 'warehouse')"
                class="flex items-center gap-2.5 px-6 py-3 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest {{ $activeTab === 'warehouse' ? 'bg-zinc-900 text-black shadow-lg' : 'text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                📦 Supply ({{ $pendingWarehouseRequests->count() }})
            </button>
            <button wire:click="$set('activeTab', 'production')"
                class="flex items-center gap-2.5 px-6 py-3 rounded-xl transition-all font-black text-[10px] uppercase tracking-widest {{ $activeTab === 'production' ? 'bg-zinc-900 text-black shadow-lg' : 'text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                🏭 Running ({{ $inProgressRequests->count() }})
            </button>
            <div class="w-px h-6 bg-zinc-100 dark:bg-zinc-800 mx-1"></div>
            <button wire:click="$set('activeTab', 'completed')"
                class="p-3 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors {{ $activeTab === 'completed' ? 'text-zinc-900 dark:text-white' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
        </div>
    </div>

    @if (session()->has('success'))
        <div
            class="alert alert-success bg-zinc-900 text-white border-0 shadow-2xl mb-10 animate-in slide-in-from-top-4 duration-300 rounded-[2rem] print:hidden">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-black uppercase text-[10px] tracking-[0.2em]">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Main Content Area -->
        <div class="lg:col-span-8 space-y-8">

            {{-- ══════════════════════════════════════════════════════════
            PLAN DETAIL: Release & History Management
            ══════════════════════════════════════════════════════════ --}}
            @if($activePlanRequestId && $activePlanRequest)
                <div class="animate-in fade-in slide-in-from-right-8 duration-500">
                    <div class="mb-4 flex items-center justify-between print:hidden">
                        <button wire:click="backToPlans"
                            class="group flex items-center gap-2 text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">
                            <div
                                class="w-8 h-8 rounded-full bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-center group-hover:border-zinc-300 dark:group-hover:border-zinc-700 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </div>
                            <span class="text-xs font-black uppercase tracking-widest">Back to Operations Dashboard</span>
                        </button>
                    </div>

                    <div id="production-report"
                        class="bg-white dark:bg-zinc-900 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-2xl shadow-zinc-200/50 dark:shadow-none overflow-hidden pb-10">
                        <div class="p-8 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
                                <div class="flex items-center gap-5">
                                    <div
                                        class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-indigo-600/30">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3
                                            class="text-2xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                            BATCH #{{ $activePlanRequest->order_number }}</h3>
                                        <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1">
                                            Operational Specification & Log Profile</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 w-full md:w-auto">
                                    <button onclick="window.print()"
                                        class="btn btn-ghost bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 flex-1 md:flex-none h-11 px-6 rounded-xl font-bold uppercase text-[10px] tracking-widest transition-all print:hidden">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download PDF
                                    </button>
                                    <div
                                        class="px-6 py-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl text-center flex-1 md:flex-none">
                                        <span
                                            class="block text-[8px] font-black text-zinc-400 uppercase tracking-widest leading-none mb-1">Order
                                            Volume</span>
                                        <span
                                            class="text-lg font-black text-zinc-900 dark:text-white leading-none tabular-nums">{{ number_format($activePlanRequest->total_quantity, 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-8">
                            @include('livewire.production.plan-detail-content', ['activePlanRequest' => $activePlanRequest])
                        </div>
                    </div>
                </div>
            @else
                {{-- ══════════════════════════════════════════════════════════
                PLANS TAB: Batch Selection (Only if not viewing detail)
                ══════════════════════════════════════════════════════════ --}}
                @if($activeTab === 'plans')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-in fade-in slide-in-from-bottom-4 duration-500">
                        @forelse($plannedProductionRequests as $order)
                            <div class="group bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 dark:border-zinc-800 p-8 shadow-sm hover:shadow-2xl hover:shadow-zinc-200/50 hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden relative"
                                wire:click="selectPlan({{ $order->id }})">
                                <div
                                    class="absolute -right-8 -top-8 w-32 h-32 bg-indigo-500/5 rounded-full blur-[80px] group-hover:bg-indigo-500/10 transition-all duration-700">
                                </div>

                                <div class="flex justify-between items-start mb-8 relative z-10">
                                    <div>
                                        <h4 class="text-2xl font-black text-zinc-900 dark:text-white leading-none tracking-tight">
                                            BATCH #{{ $order->order_number }}</h4>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span
                                                class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">{{ $order->customer->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <span
                                        class="px-4 py-1.5 bg-indigo-500/10 text-indigo-600 rounded-full text-[9px] font-black uppercase tracking-[0.2em] border border-indigo-500/20">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-4 mb-8">
                                    <div
                                        class="bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                                        <span
                                            class="block text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Target
                                            Output</span>
                                        <span
                                            class="text-lg font-black text-zinc-900 dark:text-white tabular-nums">{{ $order->total_quantity }}
                                            <span class="text-[10px] font-bold opacity-40 ml-0.5">UNITS</span></span>
                                    </div>
                                    <div class="bg-indigo-500/5 p-4 rounded-2xl border border-indigo-500/10">
                                        <span
                                            class="block text-[9px] font-black text-indigo-600/60 uppercase tracking-widest mb-1">Batch
                                            Load</span>
                                        <span class="text-sm font-black text-indigo-600 uppercase tracking-widest">
                                            📋 {{ $order->plan?->items?->count() ?? 0 }} Designs
                                        </span>
                                    </div>
                                </div>

                                @php
                                    $pendingCount = $order->plan ? \App\Models\MaterialRequest::where('production_plan_id', $order->plan->id)->where('status', 'pending')->count() : 0;
                                @endphp
                                @if($pendingCount > 0)
                                    <div
                                        class="mb-6 px-4 py-2 bg-amber-500/10 text-amber-600 border border-amber-500/20 rounded-xl text-[9px] font-black uppercase tracking-widest text-center">
                                        ⚠️ {{ $pendingCount }} Unprocessed Supply Requests
                                    </div>
                                @endif

                                <div class="flex items-center justify-between border-t border-zinc-50 dark:border-zinc-800 pt-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-xl bg-zinc-900 dark:bg-zinc-100 flex items-center justify-center text-white dark:text-black">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        @if($order->plan && $order->plan->productionLine)
                                            <span
                                                class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">{{ $order->plan->productionLine->name }}</span>
                                        @endif
                                    </div>
                                    <div
                                        class="text-[9px] font-black uppercase text-zinc-400 tracking-widest group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">
                                        Review Details →</div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="md:col-span-2 p-16 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[3rem] bg-zinc-50/50 dark:bg-zinc-800/10">
                                <div
                                    class="w-16 h-16 bg-white dark:bg-zinc-900 rounded-3xl flex items-center justify-center text-zinc-300 mx-auto mb-6 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                                <h5 class="text-xs font-black text-zinc-400 uppercase tracking-[0.4em]">Queue is Static</h5>
                                <p class="text-[10px] text-zinc-400 mt-2 uppercase tracking-widest">No approved plans awaiting
                                    operational review.</p>
                            </div>
                        @endforelse
                    </div>
                @endif
            @endif

            {{-- ══════════════════════════════════════════════════════════
            SUPPLY TAB: Today's Material Flow
            ══════════════════════════════════════════════════════════ --}}
            @if($activeTab === 'warehouse' && !$activePlanRequestId)
                <div class="space-y-8 animate-in fade-in duration-500 overflow-hidden">
                    {{-- Pending Releases --}}
                    <div
                        class="bg-white dark:bg-zinc-900 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                        <div class="p-8 border-b bg-amber-500/5 flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">Active
                                    Releases</h3>
                                <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mt-1">Batches sent
                                    to warehouse queue today</p>
                            </div>
                            <span
                                class="px-4 py-1.5 bg-amber-500 text-white rounded-full text-[9px] font-black uppercase tracking-widest">{{ $pendingWarehouseRequests->count() }}
                                PENDING</span>
                        </div>
                        <div class="divide-y divide-zinc-50 dark:divide-zinc-800">
                            @forelse($pendingWarehouseRequests as $request)
                                <div class="p-8 flex justify-between items-center hover:bg-zinc-50/50 transition-all group">
                                    <div class="flex items-center gap-6">
                                        <div
                                            class="w-12 h-12 bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-800 rounded-2xl flex items-center justify-center text-xl shadow-sm group-hover:scale-110 transition-transform">
                                            📦</div>
                                        <div>
                                            <div
                                                class="text-lg font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                                {{ $request->rawMaterial->name }}</div>
                                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mt-1">
                                                Target: Batch
                                                #{{ $request->productionPlan->productionOrder->order_number ?? '---' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right flex flex-col items-end gap-2">
                                        <div
                                            class="text-2xl font-black text-zinc-900 dark:text-white tabular-nums leading-none">
                                            {{ number_format($request->quantity, 1) }}
                                            <span
                                                class="text-xs font-bold text-zinc-300 ml-0.5 uppercase">{{ $request->rawMaterial->unit }}</span>
                                        </div>
                                        <span
                                            class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded text-[8px] font-black text-zinc-400 uppercase tracking-widest">Awaiting
                                            Load</span>
                                    </div>
                                </div>
                            @empty
                                <div class="p-16 text-center border-b border-zinc-50 dark:border-zinc-800">
                                    <p class="text-[10px] font-black text-zinc-300 uppercase tracking-[0.4em]">Supply Queue
                                        Clear</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Confirmed Deliveries --}}
                    <div
                        class="bg-white dark:bg-zinc-900 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                        <div class="p-8 border-b bg-emerald-500/5 flex justify-between items-center">
                            <div>
                                <h3 class="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                    Factory Floor Stocked</h3>
                                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mt-1">Confirmed
                                    issuance from terminal today</p>
                            </div>
                        </div>
                        <div class="divide-y divide-zinc-50 dark:divide-zinc-800 p-2">
                            @forelse($issuedMaterialsToday as $material)
                                <div
                                    class="p-6 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-800/30 rounded-3xl mb-1 last:mb-0 border border-zinc-50 dark:border-transparent">
                                    <div class="flex items-center gap-5">
                                        <div
                                            class="w-10 h-10 bg-white dark:bg-zinc-900 rounded-xl flex items-center justify-center text-emerald-500 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div
                                                class="text-md font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                                {{ $material->rawMaterial->name }}</div>
                                            <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mt-0.5">
                                                Verified Load @ {{ $material->updated_at->format('H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xl font-black text-emerald-600 tabular-nums leading-none">
                                            {{ number_format($material->quantity, 1) }}
                                            <span
                                                class="text-[9px] font-bold text-zinc-300 ml-0.5 uppercase">{{ $material->rawMaterial->unit }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-10 text-center">
                                    <p class="text-[10px] font-black text-zinc-200 uppercase tracking-[0.4em]">No inbound stock
                                        confirmed</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif

            {{-- ══════════════════════════════════════════════════════════
            RUNNING TAB: Live Factory Floor
            ══════════════════════════════════════════════════════════ --}}
            @if($activeTab === 'production' && !$activePlanRequestId)
                <div class="space-y-6 animate-in fade-in duration-500">
                    @forelse($inProgressRequests as $order)
                        <div class="bg-white dark:bg-zinc-900 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 p-10 shadow-2xl shadow-zinc-200/50 dark:shadow-none overflow-hidden relative"
                            wire:click="selectPlan({{ $order->id }})">
                            <div
                                class="absolute -right-20 -bottom-20 w-80 h-80 bg-blue-500/5 rounded-full blur-[100px] animate-pulse">
                            </div>

                            <div
                                class="flex flex-col md:flex-row justify-between items-start md:items-center gap-10 mb-10 relative z-10">
                                <div class="flex items-center gap-6">
                                    <div
                                        class="w-16 h-16 bg-zinc-900 dark:bg-white rounded-[2rem] flex items-center justify-center text-white dark:text-black shadow-2xl shadow-zinc-900/20 dark:shadow-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 animate-spin-slow" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-3xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                            BATCH #{{ $order->order_number }}</h4>
                                        <p
                                            class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em] mt-1.5 flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-ping"></span> Live
                                            Execution Phase
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-2">
                                    <span
                                        class="px-5 py-2 bg-zinc-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl">
                                        {{ $order->plan?->productionLine->name ?? 'Line Primary' }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10 relative z-10">
                                @php
                                    if ($order->plan) {
                                        $issued = \App\Models\MaterialRequest::where('production_plan_id', $order->plan->id)->where('status', 'issued')->sum('quantity');
                                        $consumed = \App\Models\MaterialRequest::where('production_plan_id', $order->plan->id)->where('status', 'consumed')->sum('quantity');
                                        $matProgress = $issued > 0 ? min(100, ($consumed / $issued) * 100) : 0;
                                    } else {
                                        $issued = 0;
                                        $consumed = 0;
                                        $matProgress = 0;
                                    }
                                @endphp

                                <div
                                    class="p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800">
                                    <div class="flex justify-between items-end mb-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1.5">Floor
                                                Supply</span>
                                            <span
                                                class="text-xs font-black text-zinc-900 dark:text-zinc-200 uppercase tabular-nums">{{ number_format($issued, 1) }}
                                                kg received</span>
                                        </div>
                                        <span
                                            class="text-[10px] font-black text-blue-500 tabular-nums">{{ round($matProgress) }}%</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-blue-500 h-full rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(59,130,246,0.6)]"
                                            style="width: {{ $matProgress }}%"></div>
                                    </div>
                                </div>

                                <div
                                    class="p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800">
                                    <div class="flex justify-between items-end mb-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1.5">Runtime
                                                Clock</span>
                                            <span
                                                class="text-xs font-black text-zinc-900 dark:text-zinc-200 uppercase tracking-widest leading-none">{{ $order->plan?->start_date?->diffForHumans() ?? 'Pending Initial' }}</span>
                                        </div>
                                        <span
                                            class="text-[10px] font-black text-emerald-500 animate-pulse uppercase">Active</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-full rounded-full animate-progress-indefinite"></div>
                                    </div>
                                </div>
                            </div>

                            <button wire:click.stop="openCompleteForm({{ $order->id }})"
                                class="btn btn-primary w-full h-16 bg-emerald-600 border-0 text-white font-black uppercase text-xs tracking-[0.4em] rounded-[2rem] shadow-2xl shadow-emerald-500/20 hover:scale-[1.01] transition-all relative z-10">
                                ✓ Signal Completion & Log Result
                            </button>
                        </div>
                    @empty
                        <div
                            class="p-20 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-800 rounded-[3rem] bg-zinc-50/50 dark:bg-zinc-800/10 scale-95 opacity-50">
                            <div class="text-8xl mb-6 grayscale pointer-events-none">🏭</div>
                            <h5 class="text-xs font-black text-zinc-400 uppercase tracking-[0.5em]">Factory Floor Secondary
                                Phase</h5>
                            <p class="text-[10px] text-zinc-300 mt-4 uppercase tracking-[0.2em]">No live runs currently
                                initialized on the line controllers.</p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- ══════════════════════════════════════════════════════════
            COMPLETED TAB: Archives
            ══════════════════════════════════════════════════════════ --}}
            @if($activeTab === 'completed' && !$activePlanRequestId)
                <div
                    class="bg-white dark:bg-zinc-900 rounded-[3rem] border border-zinc-200 dark:border-zinc-800 shadow-xl overflow-hidden animate-in fade-in duration-700">
                    <div class="p-8 border-b bg-zinc-50 dark:bg-zinc-800/50 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">
                                Post-Operational Log</h3>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mt-1">Archive of
                                verified completions (Shift Historical)</p>
                        </div>
                    </div>
                    <div class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($completedRequests as $order)
                            <div class="p-8 flex justify-between items-center hover:bg-zinc-50/30 transition-all group cursor-pointer"
                                wire:click="selectPlan({{ $order->id }})">
                                <div class="flex items-center gap-6">
                                    <div
                                        class="w-12 h-12 bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-800 rounded-2xl flex items-center justify-center text-zinc-900 dark:text-white font-black shadow-sm group-hover:bg-emerald-500 group-hover:text-white transition-all">
                                        ✓</div>
                                    <div>
                                        <div
                                            class="text-lg font-black text-zinc-900 dark:text-white uppercase tracking-tight group-hover:text-emerald-600 transition-colors">
                                            Batch #{{ $order->order_number }}</div>
                                        <p class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mt-1">Verified
                                            Resolution: {{ $order->updated_at->format('d M • H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right flex items-center gap-6">
                                    <div class="text-2xl font-black text-zinc-900 dark:text-zinc-200 tabular-nums leading-none">
                                        {{ number_format($order->total_quantity, 0) }}
                                        <span class="text-[9px] font-black text-zinc-300 ml-1 uppercase">Units</span>
                                    </div>
                                    <div
                                        class="text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-white transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                            fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-20 text-center opacity-30">
                                <p class="text-[10px] font-black text-zinc-300 uppercase tracking-[0.5em]">Archival Ledger Empty
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Area (Hidden in print) -->
        <div class="lg:col-span-4 space-y-8 print:hidden">
            {{-- Metrics Console --}}
            <div
                class="bg-white dark:bg-zinc-900 rounded-[2.5rem] border border-zinc-200 shadow-sm overflow-hidden p-8">
                <h3
                    class="font-black uppercase text-[10px] tracking-[0.4em] text-zinc-400 mb-8 pb-4 border-b border-zinc-50 dark:border-zinc-800">
                    Console Metrics</h3>

                <div class="space-y-6">
                    <div class="flex justify-between items-center group">
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Inbound
                            Queue</span>
                        <span
                            class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center font-black text-xs">{{ $pendingWarehouseRequests->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center group">
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Floor
                            Activity</span>
                        <span
                            class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-black text-xs">{{ $inProgressRequests->count() }}</span>
                    </div>

                    <div class="pt-8 border-t border-zinc-50 dark:border-zinc-800 flex justify-between items-end">
                        <div>
                            <span
                                class="block text-[8px] font-black text-zinc-400 uppercase tracking-[0.3em] mb-2">Stock
                                Issued (24H)</span>
                            <span
                                class="text-3xl font-black text-zinc-900 dark:text-white leading-none tabular-nums tracking-tighter">{{ number_format($issuedMaterialsToday->sum('quantity'), 0) }}<span
                                    class="text-sm font-bold opacity-30 ml-2 uppercase">kg</span></span>
                        </div>
                        <div
                            class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Checklist / Awareness --}}
            <div
                class="bg-zinc-900 dark:bg-black rounded-[3rem] p-10 text-white shadow-2xl shadow-zinc-900/60 relative overflow-hidden group border border-zinc-800">
                <div
                    class="absolute -right-20 -bottom-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-[120px] group-hover:bg-emerald-500/20 transition-all duration-1000">
                </div>

                <h4 class="font-black uppercase text-[10px] tracking-[0.4em] text-zinc-600 mb-10">Shift Security</h4>
                <div class="space-y-6">
                    <div class="flex items-center gap-4 group/item">
                        <div
                            class="w-2 h-2 rounded-full {{ $plannedProductionRequests->count() > 0 ? 'bg-emerald-500 shadow-[0_0_12px_rgba(16,185,129,0.8)]' : 'bg-zinc-800' }} transition-all duration-500">
                        </div>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest {{ $plannedProductionRequests->count() > 0 ? 'text-zinc-200' : 'text-zinc-700' }} transition-colors">Approved
                            Plans: {{ $plannedProductionRequests->count() }}</span>
                    </div>
                    <div class="flex items-center gap-4 group/item">
                        <div
                            class="w-2 h-2 rounded-full {{ $inProgressRequests->count() > 0 ? 'bg-blue-500 shadow-[0_0_12px_rgba(59,130,246,0.8)]' : 'bg-zinc-800' }} transition-all duration-500">
                        </div>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest {{ $inProgressRequests->count() > 0 ? 'text-zinc-200' : 'text-zinc-700' }} transition-colors">Floor
                            Pulse: {{ $inProgressRequests->count() }} Active</span>
                    </div>

                    <div
                        class="mt-12 p-5 bg-zinc-800/40 rounded-3xl border border-zinc-800 text-[9px] font-bold text-zinc-500 uppercase tracking-widest leading-relaxed">
                        "Precision is not just a metric, it's our core operational principle."
                        <br><br>
                        — Operational Directive
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Warehouse Request Modal --}}
    @if($showWarehouseRequestForm)
        <div class="fixed inset-0 bg-black/90 backdrop-blur-xl flex items-center justify-center z-[100] p-6 animate-in fade-in duration-500 print:hidden"
            wire:click.self="cancelWarehouseRequest">
            <div
                class="bg-white dark:bg-zinc-900 rounded-[3rem] p-12 w-full max-w-xl shadow-2xl border border-zinc-100 dark:border-zinc-800 scale-100 animate-in zoom-in-95 duration-300 relative overflow-hidden">
                <div class="absolute -top-32 -left-32 w-80 h-80 bg-indigo-500/5 rounded-full blur-[100px]"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-6 mb-10">
                        <div
                            class="w-16 h-16 bg-indigo-600 text-white rounded-[2rem] flex items-center justify-center shadow-2xl shadow-indigo-600/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h3
                                class="text-3xl font-black text-zinc-900 dark:text-white uppercase tracking-tight leading-none mb-1.5">
                                Shift Release</h3>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em]">Factory Floor Daily
                                Batch Allocation</p>
                        </div>
                    </div>

                    @php $selectedMaterial = \App\Models\RawMaterial::find($warehouseRequestMaterialId); @endphp

                    <div class="space-y-10">
                        <div
                            class="p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-3xl border border-zinc-100 dark:border-zinc-800">
                            <span
                                class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Input
                                Catalog Entry</span>
                            <div
                                class="font-black text-2xl text-zinc-900 dark:text-white uppercase tracking-tight leading-none">
                                {{ $selectedMaterial?->name ?? 'Unidentified Material' }}</div>
                            <div class="flex items-center gap-2 mt-4">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]">
                                </div>
                                <span
                                    class="text-[11px] font-black text-zinc-600 dark:text-emerald-500 uppercase tracking-widest tabular-nums">Warehouse
                                    Depth: {{ number_format($selectedMaterial?->quantity ?? 0, 1) }}
                                    {{ $selectedMaterial?->unit ?? 'kg' }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-end mb-4 px-1">
                                <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Quantum for
                                    Shift</label>
                                <span
                                    class="text-[11px] font-black text-indigo-600 uppercase">{{ $selectedMaterial?->unit ?? 'kg' }}
                                    Units</span>
                            </div>
                            <div class="relative">
                                <input type="number" wire:model="warehouseRequestQty"
                                    class="input input-bordered w-full text-5xl font-black h-24 bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 focus:ring-8 focus:ring-indigo-600/10 focus:border-indigo-600 transition-all rounded-[2rem] tabular-nums px-8"
                                    step="0.1" min="0.01">
                            </div>
                            <div
                                class="flex items-start gap-4 mt-8 bg-zinc-50 dark:bg-zinc-800/30 p-5 rounded-3xl border border-zinc-100 dark:border-zinc-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-500 shrink-0" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p
                                    class="text-[10px] font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest leading-relaxed">
                                    This allocation is subtracted from the planned totals. Avoid over-requesting to prevent
                                    floor congestion.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-12">
                        <button wire:click="sendWarehouseRequest"
                            class="btn btn-primary flex-1 h-16 bg-zinc-900 dark:bg-white border-0 text-white dark:text-black font-black uppercase text-xs tracking-[0.3em] rounded-[2rem] shadow-2xl shadow-zinc-900/20 dark:shadow-none hover:-translate-y-1 transition-all">
                            Release Batch to Supply →
                        </button>
                        <button wire:click="cancelWarehouseRequest"
                            class="btn btn-ghost h-16 px-8 font-black uppercase text-xs tracking-widest rounded-[2rem]">Abort</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Completion Modal --}}
    @if($showProductionForm)
        <div class="fixed inset-0 bg-black/90 backdrop-blur-xl flex items-center justify-center z-[100] p-6 animate-in fade-in duration-500 print:hidden"
            wire:click.self="cancelProduction">
            <div
                class="bg-white dark:bg-zinc-900 rounded-[3rem] p-12 w-full max-w-xl shadow-2xl border border-zinc-100 dark:border-zinc-800 scale-100 animate-in zoom-in-95 duration-300 relative overflow-hidden">
                <div class="absolute -top-32 -left-32 w-80 h-80 bg-emerald-500/5 rounded-full blur-[100px]"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-6 mb-12">
                        <div
                            class="w-16 h-16 bg-emerald-600 text-white rounded-[2rem] flex items-center justify-center shadow-2xl shadow-emerald-600/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3
                                class="text-3xl font-black text-zinc-900 dark:text-white uppercase tracking-tight leading-none mb-1.5">
                                Record Exit</h3>
                            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-[0.3em]">Closing Factory Run &
                                Logging Output</p>
                        </div>
                    </div>

                    <div class="space-y-10">
                        <div>
                            <div class="flex justify-between items-end mb-4 px-1">
                                <label class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Verified Net
                                    Output</label>
                                <span class="text-[11px] font-black text-emerald-600 uppercase">UNITS DISPATCH READY</span>
                            </div>
                            <div class="relative">
                                <input type="number" wire:model="actualProduced"
                                    class="input input-bordered w-full text-5xl font-black h-24 bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 focus:ring-8 focus:ring-emerald-600/10 focus:border-emerald-600 transition-all rounded-[2rem] tabular-nums px-8"
                                    step="0.1" min="0">
                            </div>
                            @if($activeRequestId)
                                @php $activeOrderObj = \App\Models\ProductionOrder::find($activeRequestId); @endphp
                                <div
                                    class="mt-6 flex justify-between items-center bg-zinc-900 dark:bg-white p-5 rounded-[1.5rem] border-0 text-white dark:text-black">
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em]">Specified Lab Target</span>
                                    <span
                                        class="text-lg font-black tabular-nums">{{ number_format($activeOrderObj->total_quantity ?? 0, 0) }}</span>
                                </div>
                            @endif
                        </div>

                        <div>
                            <label
                                class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-4 block ml-1">Post-Operational
                                Review / QC Remarks</label>
                            <textarea wire:model="productionNotes"
                                class="textarea textarea-bordered w-full font-bold text-sm bg-zinc-50 dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 rounded-3xl p-6 h-32 focus:ring-8 focus:ring-zinc-600/5 transition-all outline-none"
                                placeholder="Annotate shift performance, anomalies or quality pass details..."></textarea>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-12">
                        <button wire:click="completeProduction"
                            class="btn btn-primary flex-1 h-16 bg-emerald-600 border-0 text-white font-black uppercase text-xs tracking-[0.3em] rounded-[2rem] shadow-2xl shadow-emerald-500/20 hover:-translate-y-1 transition-all">
                            ✓ Verify & Finalize Archive
                        </button>
                        <button wire:click="cancelProduction"
                            class="btn btn-ghost h-16 px-8 font-black uppercase text-xs tracking-widest rounded-[2rem]">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes progress-indefinite {
            0% {
                transform: translateX(-100%);
                width: 0%;
            }

            50% {
                transform: translateX(0%);
                width: 100%;
            }

            100% {
                transform: translateX(100%);
                width: 0%;
            }
        }

        .animate-progress-indefinite {
            animation: progress-indefinite 2s infinite ease-in-out;
        }

        .animate-spin-slow {
            animation: spin 8s linear infinite;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #production-report,
            #production-report * {
                visibility: visible;
            }

            #production-report {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none !important;
                border-radius: 0;
            }

            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</div>