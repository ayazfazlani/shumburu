<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">Warehouse Authorization Center</h2>
        <p class="text-zinc-500 dark:text-zinc-400">Control point for all raw material authorizations.</p>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Raw Material Requests FROM PLANNING (Grouped by Product) -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm mb-6">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center bg-blue-50/50 dark:bg-blue-900/10">
            <h3 class="font-bold flex items-center gap-2 text-blue-800 dark:text-blue-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-1 1v9a1 1 0 001 1h10a1 1 0 001-1V8a1 1 0 00-1-1h-1V6a4 4 0 00-4-4zm3 5V6a3 3 0 10-6 0v1h6z" clip-rule="evenodd" />
                </svg>
                Production Material Demands (Issue Stock)
            </h3>
            <span class="badge badge-primary">{{ $rmRequests->count() }} Total Items</span>
        </div>
        <div class="p-4 space-y-8">
            @php
                $groupedRequests = $rmRequests->groupBy('plan_reference_id');
            @endphp

            @forelse ($groupedRequests as $planRefId => $requests)
                @php 
                    $firstReq = $requests->first();
                    $orderNumber = $firstReq->order_number;
                @endphp
                <div class="border border-zinc-100 dark:border-zinc-800 rounded-lg overflow-hidden shadow-sm">
                    <div class="bg-zinc-50 dark:bg-zinc-800/80 p-3 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                        <div>
                            <span class="text-[10px] font-black uppercase text-zinc-400">Order #{{ $orderNumber }} / Product:</span>
                            <h4 class="font-bold text-zinc-700 dark:text-zinc-200">{{ $firstReq->product_name }}</h4>
                        </div>
                        <span class="badge badge-outline text-[10px]">Planning Ref #{{ $planRefId }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="table table-compact w-full text-sm">
                            <thead class="bg-zinc-50/50 dark:bg-zinc-900/50">
                                <tr>
                                    <th>Material</th>
                                    <th>Current Stock</th>
                                    <th>Requested Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $req)
                                    @php $stockOk = $req->rawMaterial->quantity >= $req->quantity; @endphp
                                    <tr>
                                        <td class="font-medium">{{ $req->rawMaterial->name }}</td>
                                        <td>
                                            <span class="text-xs {{ $stockOk ? 'text-green-600' : 'text-red-600' }} font-bold">
                                                {{ number_format($req->rawMaterial->quantity, 2) }} {{ $req->rawMaterial->unit }}
                                            </span>
                                        </td>
                                        <td class="font-black text-blue-600">
                                            {{ number_format($req->quantity, 2) }}
                                        </td>
                                        <td>
                                            @if($req->status === 'pending')
                                                @if($stockOk)
                                                    <button wire:click="stockOutMaterial({{ $req->id }})" class="btn btn-success btn-xs text-white">
                                                        Stock Out
                                                    </button>
                                                @else
                                                    <button wire:click="forwardToProcurement({{ $req->id }})" class="btn btn-warning btn-xs">
                                                        Shortage: Forward to PR
                                                    </button>
                                                @endif
                                            @else
                                                <span class="badge badge-ghost badge-xs uppercase">{{ $req->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-zinc-400 italic">
                    No material issue requests found.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Raw Material Requisitions (From Plant) -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
            <h3 class="font-bold">Purchase Requests Awaiting Authorization</h3>
            <span class="badge badge-secondary">{{ $rmDemands->count() }} items</span>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>Requested Date</th>
                        <th>Raw Material</th>
                        <th>Current Stock</th>
                        <th>Requested Qty</th>
                        <th>Requested By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rmDemands as $req)
                        <tr>
                            <td>{{ $req->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="font-medium">{{ $req->rawMaterial->name }}</div>
                                <div class="text-xs opacity-50">{{ $req->rawMaterial->code }}</div>
                            </td>
                            <td>
                                <span class="badge badge-ghost">{{ number_format($req->rawMaterial->quantity, 2) }} {{ $req->rawMaterial->unit }}</span>
                            </td>
                            <td>
                                <span class="font-bold text-lg text-secondary">{{ number_format($req->quantity, 2) }}</span>
                            </td>
                            <td>{{ $req->requestedBy->name ?? 'Plant' }}</td>
                            <td>
                                <button wire:click="authorizePurchase({{ $req->id }})" class="btn btn-secondary btn-sm">
                                    Approve For Finance
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-zinc-500">
                                No pending Raw Material requisitions from the Plant.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>