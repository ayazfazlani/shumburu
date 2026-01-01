<div class="space-y-6 p-6">
    <!-- Stats Stripe -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalRevenue, 2) }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-600 dark:text-gray-400">Monthly Revenue</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($monthlyRevenue, 2) }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-600 dark:text-gray-400">Total Orders</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalOrders) }}</p>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-600 dark:text-gray-400">Customers</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalCustomers) }}</p>
        </div>
    </div>

    <!-- Two Graphs -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Revenue Trend -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Revenue Trend (30 Days)</h2>
            <div class="flex h-48 items-end justify-between space-x-1">
                @if($revenueTrend->count() > 0)
                    @php
                        $maxRevenue = max($revenueTrend->max('total'), 1);
                        $days = $revenueTrend->take(30);
                    @endphp
                    @foreach($days as $day)
                        <div class="group relative flex flex-1 flex-col items-center">
                            <div 
                                class="w-full rounded-t bg-gray-300 transition-colors hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500"
                                style="height: {{ max(5, ($day->total / $maxRevenue) * 100) }}%"
                                title="{{ number_format($day->total, 2) }} on {{ \Carbon\Carbon::parse($day->date)->format('M d') }}"
                            ></div>
                            @if($loop->iteration % 5 == 0 || $loop->last)
                                <span class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($day->date)->format('M d') }}</span>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="flex h-full w-full items-center justify-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Orders Status -->
        <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-sm font-semibold text-gray-900 dark:text-white">Orders Status</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Pending</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $pendingOrders }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700 dark:text-gray-300">In Production</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $inProductionOrders }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Completed</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $completedOrders }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Delivered</span>
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $deliveredOrders }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
