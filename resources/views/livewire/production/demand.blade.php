<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-zinc-800 dark:text-white">Productions Authorization Center</h2>
        <p class="text-zinc-500 dark:text-zinc-400">Control point for all production authorizations.</p>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Finished Goods Demands (From Sales) -->
    @if($viewingOrder)
        <!-- Detail View for Selected Order -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center bg-zinc-50 dark:bg-zinc-800/50">
                <div>
                    <button wire:click="backToList" class="btn btn-ghost btn-sm gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Orders
                    </button>
                    <h3 class="font-bold text-lg">Authorize Items for Order: #{{ $viewingOrder->order_number }}</h3>
                    <p class="text-xs">Customer: {{ $viewingOrder->customer->name ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity Needed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($selectedOrderFgDemands as $demand)
                            <tr>
                                <td>
                                    <div class="font-medium text-lg">{{ $demand->product->name }}</div>
                                    <div class="text-xs opacity-50">{{ $demand->product->code }}</div>
                                </td>
                                <td>
                                    <span class="font-bold text-xl text-blue-600">{{ number_format($demand->quantity, 2) }}</span>
                                </td>
                                <td>
                                    <button wire:click="authorizeProduction({{ $demand->id }})" class="btn btn-primary btn-sm">
                                        Authorize Production
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-zinc-500">
                                    No pending demands for this order.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Order List for Authorization -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <h3 class="font-bold">Production Orders Requiring Authorization</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Customer</th>
                            <th>Products to Authorize</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ordersWithStockDemands as $order)
                            <tr>
                                <td class="font-bold text-lg">#{{ $order->order_number }}</td>
                                <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $order->orderItems()->whereHas('stockDemands', function($q) {
                                            $q->where('status', 'pending');
                                        })->count() }} items
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="selectOrder({{ $order->id }})" class="btn btn-primary btn-sm">
                                        View & Authorize
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-zinc-500">
                                    No pending orders require authorization.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Individual/Manual Demands -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm mt-6">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <h3 class="font-bold">Manual/Individual Demands</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Requested By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($individualFgDemands as $demand)
                            <tr>
                                <td>{{ $demand->product->name }}</td>
                                <td class="font-bold">{{ number_format($demand->quantity, 2) }}</td>
                                <td>{{ $demand->requestedBy->name ?? 'System' }}</td>
                                <td>
                                    <button wire:click="authorizeProduction({{ $demand->id }})" class="btn btn-primary btn-sm">
                                        Authorize
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-zinc-500">
                                    No manual demands.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>