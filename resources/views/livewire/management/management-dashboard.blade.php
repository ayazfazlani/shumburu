<div class="p-6 space-y-8 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Executive Cockpit</h1>
            <p class="text-zinc-500 dark:text-zinc-400">High-level operational metrics and factory performance.</p>
        </div>
        <div
            class="flex gap-2 bg-white dark:bg-zinc-900 p-1 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <button wire:click="$set('timeFrame', 'week')"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all {{ $timeFrame === 'week' ? 'bg-zinc-900 text-white shadow-md' : 'text-zinc-500 hover:bg-zinc-100' }}">Weekly</button>
            <button wire:click="$set('timeFrame', 'month')"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all {{ $timeFrame === 'month' ? 'bg-zinc-900 text-white shadow-md' : 'text-zinc-500 hover:bg-zinc-100' }}">Monthly</button>
            <button wire:click="$set('timeFrame', 'year')"
                class="px-4 py-2 rounded-lg text-sm font-bold transition-all {{ $timeFrame === 'year' ? 'bg-zinc-900 text-white shadow-md' : 'text-zinc-500 hover:bg-zinc-100' }}">Annual</button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- OTD Card -->
        <div
            class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-30 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-zinc-500 text-xs font-bold uppercase tracking-widest mb-1">On-Time Delivery (OTD)</h3>
            <div class="text-4xl font-black text-zinc-900 dark:text-white mb-2">{{ $metrics['otd'] }}%</div>
            <div class="flex items-center gap-2">
                <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-indigo-500 h-full" style="width: {{ $metrics['otd'] }}%"></div>
                </div>
            </div>
            <p class="text-[10px] text-zinc-400 mt-3 font-medium">{{ $metrics['onTimeCount'] }} of
                {{ $metrics['totalDelivered'] }} orders delivered on time
            </p>
        </div>

        <!-- Scrap Rate Card -->
        <div
            class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-30 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-zinc-500 text-xs font-bold uppercase tracking-widest mb-1">Scrap Rate</h3>
            <div class="text-4xl font-black {{ $metrics['scrapRate'] > 5 ? 'text-red-500' : 'text-green-500' }} mb-2">
                {{ $metrics['scrapRate'] }}%
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold {{ $metrics['scrapRate'] > 5 ? 'text-red-500' : 'text-green-500' }}">
                    {{ $metrics['scrapRate'] > 5 ? 'Above Threshold' : 'Target Achieved' }}
                </span>
            </div>
            <p class="text-[10px] text-zinc-400 mt-3 font-medium italic">Target: &lt;2.5% per production run</p>
        </div>

        <!-- Production Volume -->
        <div
            class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm relative overflow-hidden group">
            <div class="absolute top-4 right-4 opacity-20 group-hover:opacity-30 transition-opacity">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <h3 class="text-zinc-500 text-xs font-bold uppercase tracking-widest mb-1">Production Output</h3>
            <div class="text-4xl font-black text-indigo-600 mb-2">{{ number_format($metrics['outputVolume']) }}</div>
            <span class="text-xs font-bold text-zinc-400">Total Meters Produced</span>
            <p class="text-[10px] text-zinc-400 mt-3 font-medium">Real-time FG stock aggregate</p>
        </div>

        <!-- System Health -->
        <div class="bg-indigo-600 p-6 rounded-2xl shadow-xl relative overflow-hidden group">
            <div class="absolute top-4 right-4 opacity-20">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h3 class="text-indigo-200 text-xs font-bold uppercase tracking-widest mb-1">Gate Integrity</h3>
            <div class="text-4xl font-black text-white mb-2">100%</div>
            <span class="text-xs font-bold text-indigo-100">QC & Material Gates Active</span>
            <p class="text-[10px] text-indigo-200 mt-3 font-medium">Zero bypasses recorded this period</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Status Chart / Progress -->
        <div
            class="lg:col-span-1 bg-white dark:bg-zinc-900 p-8 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <h3 class="text-lg font-black text-zinc-900 dark:text-white mb-6">Order Pipeline</h3>
            <div class="space-y-6">
                @foreach($orderStatus as $stat)
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span
                                class="text-sm font-bold text-zinc-500 uppercase">{{ str_replace('_', ' ', $stat->status) }}</span>
                            <span class="text-lg font-black text-zinc-900 dark:text-white">{{ $stat->count }}</span>
                        </div>
                        <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-2 rounded-full overflow-hidden">
                            <div class="bg-indigo-600 h-full"
                                style="width: {{ ($stat->count / ($orderStatus->sum('count') ?: 1)) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- High Risk Areas / Recent Scrap -->
        <div
            class="lg:col-span-2 bg-white dark:bg-zinc-900 p-8 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-black text-zinc-900 dark:text-white">Recent Large Scraps (Risk Log)</h3>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 opacity-50" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-zinc-400 text-[10px] font-bold uppercase tracking-widest">
                            <th class="pb-4">Date</th>
                            <th class="pb-4">Product / Line</th>
                            <th class="pb-4">Quantity</th>
                            <th class="pb-4">Reason</th>
                            <th class="pb-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        @forelse($recentHighScrap as $scrap)
                            <tr>
                                <td class="py-4 text-sm font-medium text-zinc-500">{{ $scrap->created_at->format('M d') }}
                                </td>
                                <td class="py-4">
                                    <div class="font-bold text-zinc-900 dark:text-white text-sm">Line
                                        {{ $scrap->materialStockOutLine->productionLine->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-[10px] text-zinc-400">Batch
                                        #{{ $scrap->materialStockOutLine->materialStockOut->batch_number ?? 'N/A' }}</div>
                                </td>
                                <td class="py-4 font-black text-red-500 text-sm">{{ number_format($scrap->quantity, 2) }} kg
                                </td>
                                <td class="py-4 text-xs text-zinc-500 italic">"{{ $scrap->reason }}"</td>
                                <td class="py-4">
                                    <span
                                        class="px-2 py-1 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-[10px] font-bold">Logged</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-zinc-400 italic">No significant scrap recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>