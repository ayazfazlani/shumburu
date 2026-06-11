<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">Production Planning & Demand</h2>
            <p class="text-zinc-500 dark:text-zinc-400">Incoming production requests from Sales and material shortage management.</p>
        </div>
        
        <button wire:click="openPurchaseModal" class="btn btn-secondary">
            Manual Purchase Request
        </button>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Planning Area -->
        <div class="lg:col-span-2 space-y-8">
            @if($viewingOrder)
                <!-- Detail View: Specific Order Planning (Unified Table Theme) -->
                <div class="animate-in fade-in slide-in-from-left-4 duration-500">
                    <div class="mb-4 flex items-center justify-between px-2">
                        <div>
                            <button wire:click="backToList" class="btn btn-ghost btn-sm gap-2 text-zinc-500 hover:text-zinc-900 font-black">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                                Back to Active Orders
                            </button>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Order Progress</span>
                            <span class="badge badge-primary h-7 px-4 font-black uppercase text-[10px]">{{ count($selectedOrderDemands) }} Profiles</span>
                        </div>
                    </div>

                    <!-- Professional Planning & Material Report (High Density) -->
                    <div id="planning-report" class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden mb-8 print:border-0 print:shadow-none print:rounded-none">
                        <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 flex justify-between items-center print:bg-white">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-zinc-900 dark:bg-zinc-800 rounded-lg flex items-center justify-center text-white print:hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white uppercase">Planning Report: #{{ $viewingOrder->order_number }}</h3>
                                    <p class="text-xs font-medium text-zinc-500 uppercase tracking-wider">{{ $viewingOrder->customer->name ?? 'N/A' }} • {{ now()->format('d M, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 print:hidden">
                                <button onclick="window.print()" class="btn btn-sm btn-ghost gap-2 font-bold uppercase text-[10px] border border-zinc-200 dark:border-zinc-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2-2v4h10z" /></svg>
                                    Print Report
                                </button>
                                <div class="px-3 py-1.5 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 flex flex-col items-center">
                                    <span class="text-[8px] font-bold text-zinc-400 uppercase leading-none">Profiles</span>
                                    <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ count($selectedOrderDemands) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- 1. Production Profile Table -->
                        <div class="p-4">
                            <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                                I. Production Sequencing & Specs
                            </h4>
                            <div class="overflow-x-auto rounded-lg border border-zinc-100 dark:border-zinc-800">
                                <table class="table table-sm w-full">
                                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-100 dark:border-zinc-800 text-[10px] uppercase text-zinc-500 font-bold">
                                        <tr>
                                            <th class="py-3 pl-6">Profile Item</th>
                                            <th>Specs (OD)</th>
                                            <th class="text-center">Target Qty</th>
                                            <th>Status</th>
                                            <th class="pr-6 text-right print:hidden">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-xs">
                                        @foreach ($selectedOrderDemands as $request)
                                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 border-b border-zinc-50 dark:border-zinc-800 last:border-0">
                                                <td class="pl-6 py-3 font-bold text-zinc-900 dark:text-white uppercase">{{ $request->product->name }}</td>
                                                <td>
                                                    @if($request->orderItem && $request->orderItem->od)
                                                        <span class="badge badge-outline badge-sm font-bold text-[9px]">{{ $request->orderItem->od }} MM</span>
                                                    @else
                                                        <span class="text-[10px] text-zinc-400 italic font-bold">Standard</span>
                                                    @endif
                                                </td>
                                                <td class="text-center font-bold">{{ number_format($request->quantity, 2) }}</td>
                                                <td>
                                                    <div class="flex items-center gap-2">
                                                        <select wire:change="updateStatus({{ $request->id }}, $event.target.value)" class="select select-bordered select-xs font-bold uppercase text-[9px] bg-white dark:bg-zinc-900 print:hidden h-6 min-h-0">
                                                            <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                            <option value="approved" {{ $request->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                            <option value="scheduled" {{ $request->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                            <option value="completed" {{ $request->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                        </select>
                                                        <span class="hidden print:block font-bold uppercase text-[10px]">{{ $request->status }}</span>
                                                    </div>
                                                </td>
                                                <td class="pr-6 text-right print:hidden">
                                                    <button wire:click="requestMaterials({{ $request->id }})" class="btn btn-ghost btn-xs text-blue-600 font-bold uppercase text-[8px] h-6 min-h-0">Add Demand</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 2. Aggregated Material Requirements -->
                        <div class="p-4 bg-zinc-50/30 dark:bg-zinc-800/10 border-t border-zinc-100 dark:border-zinc-800">
                            <h4 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-zinc-300"></span>
                                II. Raw Material Summary
                            </h4>
                            
                            @if(count($aggregatedMaterialSummary) > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($aggregatedMaterialSummary as $matId => $summary)
                                        <div class="p-3 bg-white dark:bg-zinc-900 rounded-lg border border-zinc-100 dark:border-zinc-800 flex justify-between items-center shadow-sm">
                                            <div>
                                                <span class="text-[8px] font-bold text-zinc-400 uppercase block leading-none mb-1">Material</span>
                                                <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 uppercase">{{ $summary['name'] }}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-[8px] font-bold text-blue-500 uppercase block mb-0.5">Req. Qty</span>
                                                <span class="text-lg font-bold text-zinc-900 dark:text-white leading-none tracking-tight">{{ number_format($summary['total_quantity'], 1) }}</span>
                                                <span class="text-[9px] font-bold text-zinc-400 uppercase ml-0.5">{{ $summary['unit'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">
                                    <span class="text-[10px] font-bold text-zinc-300 uppercase tracking-widest italic">No material requirements recorded.</span>
                                </div>
                            @endif
                        </div>

                        <!-- 3. Report Footer -->
                        <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 font-bold bg-zinc-50/50 dark:bg-zinc-800/30">
                            <div class="flex justify-between items-end mb-6">
                                <div class="w-48 border-t border-zinc-300 dark:border-zinc-700 pt-1">
                                    <p class="text-[8px] text-zinc-400 uppercase text-center">Production Manager</p>
                                </div>
                                <div class="w-48 border-t border-zinc-300 dark:border-zinc-700 pt-1">
                                    <p class="text-[8px] text-zinc-400 uppercase text-center">Warehouse Verification</p>
                                </div>
                            </div>
                            <p class="text-[8px] text-zinc-400 uppercase text-center tracking-widest">
                                Report Generated: {{ now()->format('d M Y - H:i') }} • Shumburu Factory ERP Systems
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Order Summary List: Initial View (High Density / Standard ERP) -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                    <div class="p-3.5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50 flex justify-between items-center">
                        <h3 class="font-bold text-[10px] uppercase tracking-widest text-zinc-500 italic">Unprocessed Production Orders</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-sm w-full">
                            <thead class="bg-zinc-50/20 dark:bg-zinc-900/40 border-b border-zinc-100 dark:border-zinc-800 text-[9px] uppercase tracking-widest text-zinc-400 font-bold">
                                <tr>
                                    <th class="bg-transparent pl-6">Order Reference</th>
                                    <th class="bg-transparent">Customer</th>
                                    <th class="bg-transparent text-center">Items Waiting</th>
                                    <th class="bg-transparent text-right pr-6">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800 text-xs">
                                @forelse ($ordersWithDemands as $order)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                        <td class="pl-6 font-bold text-zinc-800 dark:text-white uppercase tracking-tight py-2.5 text-base">#{{ $order->order_number }}</td>
                                        <td class="font-medium text-zinc-500 uppercase text-[10px]">{{ $order->customer->name ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-outline badge-sm h-5 px-3 font-bold uppercase text-[9px] border-zinc-200 dark:border-zinc-700">{{ $order->pending_requests_count ?? 0 }} Demands</span>
                                        </td>
                                        <td class="text-right pr-6">
                                            <button wire:click="selectOrder({{ $order->id }})" class="btn btn-primary btn-xs px-4 font-bold uppercase text-[9px] h-7 min-h-0">Open Planner</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-10 text-zinc-300 font-bold uppercase tracking-widest text-xs opacity-50">No orders awaiting material planning.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Consolidated Material Demands (Global Summary - Compact) -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm mt-6">
                    <div class="p-3.5 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-[10px] uppercase tracking-widest text-zinc-500 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                Raw Material Consolidation
                            </h3>
                        </div>
                        <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-tighter">{{ count($globalMaterialSummary) }} Materials Active</span>
                    </div>
                    <div class="p-4">
                        @if(count($globalMaterialSummary) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($globalMaterialSummary as $summary)
                                    <div class="p-3.5 bg-zinc-50/50 dark:bg-zinc-800/30 rounded-lg border border-zinc-100 dark:border-zinc-800 flex justify-between items-center group">
                                        <div>
                                            <span class="text-[8px] font-bold text-zinc-400 uppercase block mb-0.5">Material Description</span>
                                            <span class="text-xs font-bold text-zinc-800 dark:text-zinc-200 uppercase leading-tight">{{ $summary['name'] }}</span>
                                            <div class="mt-1.5 flex items-center gap-1.5">
                                                <div class="w-1.5 h-1.5 rounded-full {{ $summary['in_stock'] < $summary['total_quantity'] ? 'bg-red-500 animate-pulse' : 'bg-green-500' }}"></div>
                                                <span class="text-[9px] font-medium text-zinc-500">Stock: {{ number_format($summary['in_stock'], 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] font-bold text-blue-600 block leading-none mb-0.5 tracking-tighter">{{ number_format($summary['total_quantity'], 1) }}</span>
                                            <span class="text-[8px] font-bold text-zinc-400 uppercase leading-none">{{ $summary['unit'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-zinc-300 font-bold uppercase tracking-widest text-[10px] border border-dashed border-zinc-100 dark:border-zinc-800 rounded-lg">
                                No active material demands found.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Stock Replenishment Demands -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm mt-6">
                    <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                        <h3 class="font-bold text-[10px] uppercase tracking-widest text-zinc-500">Stock Replenishment</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($replenishmentRequests as $request)
                                    <tr>
                                        <td>{{ $request->product->name }}</td>
                                        <td>{{ number_format($request->quantity, 1) }}</td>
                                        <td>{{ $request->status }}</td>
                                        <td><button wire:click="openPurchaseModal({{ $request->id }})" class="btn btn-outline btn-xs">Request Material</button></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4 text-zinc-500 italic">No direct requests.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar: Downtime / Maintenance Monitor -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 flex justify-between items-center">
                    <h3 class="font-bold flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                        Maintenance Monitor
                    </h3>
                </div>
                <div class="p-4 space-y-4">
                    @forelse($recentDowntime as $record)
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-100 dark:border-zinc-700">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-xs font-bold text-zinc-500 uppercase">{{ \Carbon\Carbon::parse($record->downtime_date)->format('M d, Y') }}</span>
                                <span class="badge badge-ghost badge-xs">{{ $record->duration_minutes }} min</span>
                            </div>
                            <div class="font-bold text-sm text-zinc-800 dark:text-zinc-200">{{ $record->reason }}</div>
                            @if($record->notes)
                                <div class="text-[10px] text-zinc-500 mt-1 italic line-clamp-2">"{{ $record->notes }}"</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-zinc-500 text-sm">
                            No recent downtime recorded.<br>
                            <span class="text-green-500 font-bold">All systems nominal ✅</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Material Summary Widget -->
            <div class="bg-zinc-900 text-white rounded-xl p-6 shadow-xl border border-zinc-800 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
                <h4 class="text-zinc-400 text-xs font-bold uppercase tracking-widest mb-4">Planning Readiness</h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span>Active Orders</span>
                        <span class="font-bold">{{ $ordersWithDemands->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span>Material Safety</span>
                        <span class="text-green-400 font-bold">High</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Material Request Modal (Warehouse) -->
    <dialog class="modal @if($showMaterialRequestModal) modal-open @endif">
        <div class="modal-box w-full max-w-lg">
            <h3 class="font-bold text-lg mb-4 text-blue-600">Request Materials from Warehouse</h3>
            <div class="space-y-4">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">Select Raw Material</span></label>
                    <select wire:model="req_material_id" class="select select-bordered w-full">
                        <option value="">Choose Material...</option>
                        @foreach($rawMaterials as $mat)
                            <option value="{{ $mat->id }}">{{ $mat->name }} (In Stock: {{ $mat->quantity }} {{ $mat->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control w-full">
                    <label class="label"><span class="label-text">Quantity to Demand (kg)</span></label>
                    <input wire:model="req_quantity" type="number" step="0.01" class="input input-bordered w-full" />
                    <p class="text-[10px] opacity-50 mt-1 italic font-bold">Recommended based on request qty and weight per meter.</p>
                </div>
            </div>
            <div class="modal-action">
                <button wire:click="$set('showMaterialRequestModal', false)" class="btn btn-ghost">Cancel</button>
                <button wire:click="submitMaterialRequest" class="btn btn-primary">Send to Warehouse</button>
            </div>
        </div>
    </dialog>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #planning-report, #planning-report * {
                visibility: visible;
            }
            #planning-report {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none !important;
            }
            .print\:hidden {
                display: none !important;
            }
            .print\:block {
                display: block !important;
            }
            .print\:bg-white {
                background-color: white !important;
            }
            .print\:text-zinc-900 {
                color: #18181b !important;
            }
            .print\:border-t-2 {
                border-top-width: 2px !important;
            }
            .print\:border-zinc-900 {
                border-color: #18181b !important;
            }
            .print\:border-0 {
                border-width: 0 !important;
            }
            .print\:shadow-none {
                box-shadow: none !important;
            }
            .print\:rounded-none {
                border-radius: 0 !important;
            }
        }
    </style>
</div>
