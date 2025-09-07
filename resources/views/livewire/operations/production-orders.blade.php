<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Production Orders Management</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Manage production order status and track progress</p>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-wrap gap-4">
        <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Orders</label>
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Search by order number or customer..."
                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
        </div>

        <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Status</label>
            <select wire:model.live="statusFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="in_production">In Production</option>
                <option value="completed">Completed</option>
                <option value="delivered">Delivered</option>
            </select>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-700">
                    <tr>
                        <th wire:click="sortBy('order_number')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600">
                            Order Number
                            @if($sortField === 'order_number')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('customer_id')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600">
                            Customer
                            @if($sortField === 'customer_id')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600">
                            Status
                            @if($sortField === 'status')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                        <th wire:click="sortBy('requested_date')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600">
                            Requested Date
                            @if($sortField === 'requested_date')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Production Dates
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    #{{ $order->order_number }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $order->customer->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $order->customer->code ?? '' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getStatusColor($order->status) }}">
                                    <i class="{{ $this->getStatusIcon($order->status) }} mr-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $order->requested_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <div>
                                    @if($order->production_start_date)
                                        <div class="text-green-600 dark:text-green-400">
                                            <i class="fas fa-play mr-1"></i>
                                            Started: {{ $order->production_start_date->format('M d, Y') }}
                                        </div>
                                    @endif
                                    @if($order->production_end_date)
                                        <div class="text-blue-600 dark:text-blue-400">
                                            <i class="fas fa-stop mr-1"></i>
                                            Completed: {{ $order->production_end_date->format('M d, Y') }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if($order->status === 'approved')
                                        <button 
                                            wire:click="markAsInProduction({{ $order->id }})"
                                            wire:confirm="Mark this order as in production?"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                        >
                                            <i class="fas fa-industry mr-1"></i>
                                            Start Production
                                        </button>
                                    @endif
                                    
                                    @if($order->status === 'in_production')
                                        <button 
                                            wire:click="markAsCompleted({{ $order->id }})"
                                            wire:confirm="Mark this order as completed? This will notify the sales team."
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                        >
                                            <i class="fas fa-check-double mr-1"></i>
                                            Mark Completed
                                        </button>
                                    @endif
                                    
                                    @if($order->status === 'completed')
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-800 bg-green-100 rounded-md">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Ready for Delivery
                                        </span>
                                    @endif
                                    
                                    @if($order->status === 'delivered')
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-800 bg-blue-100 rounded-md">
                                            <i class="fas fa-truck mr-1"></i>
                                            Delivered
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No production orders found</p>
                                    <p>Try adjusting your search or filter criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 z-50">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('message') }}
                </div>
            </div>
        </div>
    @endif
</div>
