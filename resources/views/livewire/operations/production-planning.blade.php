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

    <div class="grid grid-cols-1 gap-6">
        @if($viewingOrder)
            <!-- Detail View for Selected Order -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50 flex justify-between items-center">
                    <div>
                        <button wire:click="backToList" class="btn btn-ghost btn-sm gap-2 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                            Back to Orders
                        </button>
                        <h3 class="font-bold text-lg">Demands for Order: #{{ $viewingOrder->order_number }}</h3>
                        <p class="text-sm opacity-70">Customer: {{ $viewingOrder->customer->name ?? 'N/A' }}</p>
                    </div>
                    <div class="badge badge-primary">{{ count($selectedOrderDemands) }} Demands</div>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Requested By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($selectedOrderDemands as $request)
                                <tr>
                                    <td>
                                        <div class="font-medium">{{ $request->product->name }}</div>
                                        <div class="text-xs opacity-50">{{ $request->product->code }}</div>
                                    </td>
                                    <td>
                                        <span class="font-bold text-lg">{{ number_format($request->quantity, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($request->priority === 'high')
                                            <span class="badge badge-error text-white">High</span>
                                        @elseif($request->priority === 'medium')
                                            <span class="badge badge-warning">Medium</span>
                                        @else
                                            <span class="badge badge-ghost">Low</span>
                                        @endif
                                    </td>
                                    <td>
                                        <select wire:change="updateStatus({{ $request->id }}, $event.target.value)" class="select select-bordered select-xs">
                                            <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $request->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="scheduled" {{ $request->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="completed" {{ $request->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </td>
                                    <td>{{ $request->requestedBy->name ?? 'System' }}</td>
                                    <td>
                                        <button wire:click="openPurchaseModal({{ $request->id }})" class="btn btn-outline btn-xs gap-1">
                                            Request Material
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Order Summary List -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                    <h3 class="font-bold">Production Orders with Demands</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Customer</th>
                                <th>Requested Date</th>
                                <th>Pending Demands</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ordersWithDemands as $order)
                                <tr>
                                    <td class="font-bold">#{{ $order->order_number }}</td>
                                    <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $order->requested_date?->format('M d, Y') ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $order->pending_requests_count }} Items</span>
                                    </td>
                                    <td>
                                        <button wire:click="selectOrder({{ $order->id }})" class="btn btn-primary btn-xs">
                                            View Demands
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-zinc-500 italic">
                                        No orders have pending demands.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stock Replenishment Demands (No Order) -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm mt-6">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                    <h3 class="font-bold">Stock Replenishment Requests (Direct)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Requested By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($replenishmentRequests as $request)
                                <tr>
                                    <td>
                                        <div class="font-medium">{{ $request->product->name }}</div>
                                        <div class="text-xs opacity-50">{{ $request->product->code }}</div>
                                    </td>
                                    <td>
                                        <span class="font-bold">{{ number_format($request->quantity, 2) }}</span>
                                    </td>
                                    <td>
                                        <select wire:change="updateStatus({{ $request->id }}, $event.target.value)" class="select select-bordered select-xs">
                                            <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="approved" {{ $request->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                            <option value="scheduled" {{ $request->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="completed" {{ $request->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                    </td>
                                    <td>{{ $request->requestedBy->name ?? 'System' }}</td>
                                    <td>
                                        <button wire:click="openPurchaseModal({{ $request->id }})" class="btn btn-outline btn-xs gap-1">
                                            Request Material
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-zinc-500 italic">
                                        No direct replenishment demands.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Purchase Request Modal -->
    <dialog class="modal @if($showPurchaseModal) modal-open @endif">
        <div class="modal-box w-full max-w-lg">
            <h3 class="font-bold text-lg mb-4 text-secondary">New Purchase Requisition</h3>
            <p class="text-xs text-zinc-500 mb-6">Send a request to Finance/Procurement to buy more raw materials.</p>

            <div class="space-y-4">
                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Select Raw Material</span>
                    </label>
                    <select wire:model="raw_material_id" class="select select-bordered w-full">
                        <option value="">Choose Material...</option>
                        @foreach($rawMaterials as $mat)
                            <option value="{{ $mat->id }}">{{ $mat->name }} (Balance: {{ $mat->quantity }} {{ $mat->unit }})</option>
                        @endforeach
                    </select>
                    @error('raw_material_id') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Quantity Required</span>
                    </label>
                    <input wire:model="purchase_quantity" type="number" step="0.01" class="input input-bordered w-full" placeholder="e.g. 500" />
                    @error('purchase_quantity') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Notes / Priority</span>
                    </label>
                    <textarea wire:model="purchase_notes" class="textarea textarea-bordered h-24" placeholder="Why is this needed? High priority for Order #123..."></textarea>
                    @error('purchase_notes') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="modal-action">
                <button wire:click="$set('showPurchaseModal', false)" class="btn btn-ghost">Cancel</button>
                <button wire:click="raisePurchaseRequest" class="btn btn-secondary">Submit Requisition</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button wire:click="$set('showPurchaseModal', false)">close</button>
        </form>
    </dialog>
</div>
