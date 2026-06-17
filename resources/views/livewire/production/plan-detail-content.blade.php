<div>
    <div class="flex items-center justify-between mb-8">
        <h4 class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-[0.3em] flex items-center gap-3">
            <div class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.6)]"></div>
            Material Release Matrix
        </h4>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @php
            $planMaterialSummary = collect();
            if ($activePlanRequest->plan) {
                $planMaterialSummary = $activePlanRequest->plan->items
                    ->groupBy('raw_material_id')
                    ->map(function ($group) use ($activePlanRequest) {
                        $alreadySent = \App\Models\MaterialRequest::where('production_plan_id', $activePlanRequest->plan->id)
                            ->where('raw_material_id', $group->first()->raw_material_id)
                            ->sum('quantity');
                        
                        return [
                            'material_id' => $group->first()->raw_material_id,
                            'material_name' => $group->first()->rawMaterial->name,
                            'planned_qty' => $group->sum('planned_quantity'),
                            'already_sent' => $alreadySent,
                            'remaining' => $group->sum('planned_quantity') - $alreadySent,
                            'in_stock' => $group->first()->rawMaterial->quantity,
                        ];
                    });
            }
        @endphp

        @forelse($planMaterialSummary as $material)
            <div class="p-6 bg-zinc-50 dark:bg-zinc-800/50 rounded-[2rem] border border-zinc-100 dark:border-zinc-800 hover:shadow-xl transition-all duration-300">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest mb-1">Input Catalog</span>
                        <h5 class="text-base font-black text-zinc-900 dark:text-zinc-100 uppercase leading-none tracking-tight">{{ $material['material_name'] }}</h5>
                    </div>
                    @if($material['remaining'] <= 0)
                        <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-3 mb-8">
                    <div class="text-center p-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800">
                        <span class="block text-[8px] font-black text-zinc-400 uppercase leading-none mb-1.5">Stock</span>
                        <span class="text-xs font-black text-zinc-900 dark:text-white tabular-nums tracking-tighter">{{ number_format($material['in_stock'], 1) }}</span>
                    </div>
                    <div class="text-center p-2 rounded-xl bg-blue-500/5 border border-blue-500/10">
                        <span class="block text-[8px] font-black text-blue-400 uppercase leading-none mb-1.5">Plan</span>
                        <span class="text-xs font-black text-zinc-900 dark:text-white tabular-nums tracking-tighter">{{ number_format($material['planned_qty'], 1) }}</span>
                    </div>
                    <div class="text-center p-2 rounded-xl bg-indigo-500/5 border border-indigo-500/10">
                        <span class="block text-[8px] font-black text-indigo-400 uppercase leading-none mb-1.5">Sent</span>
                        <span class="text-xs font-black text-indigo-600 tabular-nums tracking-tighter">{{ number_format($material['already_sent'], 1) }}</span>
                    </div>
                </div>

                <div class="mb-8">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-[9px] font-black text-zinc-400 uppercase tracking-widest">Released Percentage</span>
                        <span class="text-[10px] font-black text-zinc-900 dark:text-zinc-200 uppercase tabular-nums">
                            {{ $material['planned_qty'] > 0 ? round(($material['already_sent'] / $material['planned_qty']) * 100) : 0 }}%
                        </span>
                    </div>
                    <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-500 h-full rounded-full transition-all duration-1000"
                             style="width: {{ $material['planned_qty'] > 0 ? min(100, ($material['already_sent'] / $material['planned_qty']) * 100) : 0 }}%">
                        </div>
                    </div>
                </div>

                @if($activePlanRequest->status === 'approved' || $activePlanRequest->status === 'in_production')
                    <button wire:click="openWarehouseRequestForm({{ $activePlanRequest->id }}, {{ $material['material_id'] }}, {{ $material['remaining'] }})"
                            class="btn btn-primary w-full h-11 bg-zinc-900 dark:bg-white border-0 text-white dark:text-black font-black uppercase text-[10px] tracking-widest rounded-2xl shadow-xl shadow-zinc-900/10 dark:shadow-none hover:scale-[1.02] active:scale-95 transition-all outline-none focus:outline-none">
                        Request Release Batch
                    </button>
                @endif
            </div>
        @empty
            <div class="md:col-span-2 text-center p-12 bg-white dark:bg-zinc-900 rounded-[2rem] border border-dashed border-zinc-200 dark:border-zinc-800">
                <p class="font-black uppercase text-xs text-zinc-300 tracking-[0.2em]">Material analysis was not attached to this plan</p>
            </div>
        @endforelse
    </div>

    {{-- Flow Gate: Start Production / Completion Status --}}
    <div class="mt-12 bg-zinc-50 dark:bg-zinc-900/50 p-8 rounded-[2.5rem] border border-zinc-100 dark:border-zinc-800 flex flex-col items-center text-center">
        @if($activePlanRequest->status === 'approved')
            {{-- ... Start production logic ... --}}
            @php
                $hasIssuedMaterials = $activePlanRequest->plan ? \App\Models\MaterialRequest::where('production_plan_id', $activePlanRequest->plan->id)->where('status', 'issued')->exists() : false;
            @endphp
            
            @if($hasIssuedMaterials)
                <div class="flex flex-col items-center gap-6 print:hidden">
                    <div class="w-16 h-16 bg-emerald-500 rounded-[2rem] flex items-center justify-center text-white shadow-2xl shadow-emerald-500/30 animate-bounce">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h5 class="text-xl font-black text-zinc-900 dark:text-white uppercase tracking-tight">Factory Floor Ready</h5>
                        <p class="text-xs font-bold text-zinc-500 mt-1 uppercase tracking-widest">Released materials arrived. Start live monitoring?</p>
                    </div>
                    <button wire:click="startProduction({{ $activePlanRequest->id }})" 
                            class="btn btn-primary h-14 px-16 bg-zinc-900 dark:bg-white border-0 text-white dark:text-black font-black uppercase text-xs tracking-[0.3em] rounded-3xl shadow-2xl shadow-zinc-900/40 dark:shadow-none hover:-translate-y-1 transition-all">
                        🏭 Initialize Factory Run
                    </button>
                </div>
            @else
                <div class="flex flex-col items-center gap-6 opacity-60">
                    <div class="w-16 h-16 bg-zinc-200 dark:bg-zinc-800 rounded-[2rem] flex items-center justify-center text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="max-w-xs">
                        <h5 class="text-xs font-black text-zinc-900 dark:text-white uppercase tracking-[0.2em]">Gate Lock: Supply Chain</h5>
                        <p class="text-[10px] font-bold text-zinc-500 mt-2 uppercase tracking-widest leading-relaxed px-4">Production cannot begin until at least one material batch is picked and issued by the warehouse terminal.</p>
                    </div>
                </div>
            @endif
        @elseif($activePlanRequest->status === 'in_production')
            <div class="w-full p-6 bg-blue-500/10 border border-blue-500/20 rounded-3xl text-blue-600 flex flex-col items-center gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-red-600 animate-ping"></div>
                    <span class="text-xs font-black uppercase tracking-[0.3em]">Live Runtime Active</span>
                </div>
            </div>
        @elseif($activePlanRequest->status === 'completed')
            <div class="w-full p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-3xl text-emerald-600 flex flex-col items-center gap-2">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-xs font-black uppercase tracking-[0.3em]">Production Completed & Verified</span>
                </div>
                <p class="text-[10px] font-bold uppercase mt-2">Log Reference: {{ $activePlanRequest->updated_at->format('d M Y H:i') }}</p>
            </div>
        @endif
    </div>
</div>
