<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-primary">📊 Sales Reports</h1>
                    <p class="text-base-content/70 mt-1">SPF-HDPE Pipe Factory - Sales Analytics & Reports</p>
                </div>
                <div class="flex gap-2">
                    <div class="badge badge-primary badge-lg">Reports</div>
                    <div class="badge badge-outline">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <!-- Report Controls -->
        <div class="card bg-base-100 shadow-lg mb-6">
            <div class="card-body">
                <h2 class="card-title text-primary">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    Report Settings
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Report Type -->
                    <div>
                        <label class="label">
                            <span class="label-text">Report Type</span>
                        </label>
                        <select wire:model.live="reportType" class="select select-bordered w-full">
                            <option value="daily">Daily Report</option>
                            <option value="weekly">Weekly Report</option>
                            <option value="monthly">Monthly Report</option>
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="label">
                            <span class="label-text">Start Date</span>
                        </label>
                        <input wire:model.live="startDate" type="date" class="input input-bordered w-full" />
                    </div>

                    <!-- End Date (for weekly reports) -->
                    <div>
                        <label class="label">
                            <span class="label-text">End Date</span>
                        </label>
                        <input wire:model.live="endDate" type="date" class="input input-bordered w-full" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Orders -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <div class="stat-title">Delivered Orders</div>
                <div class="stat-value text-primary">{{ $summary['total_orders'] }}</div>
                <div class="stat-desc">{{ ucfirst($reportType) }} period</div>
            </div>

            <!-- Total Deliveries -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-success">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="stat-title">Deliveries</div>
                <div class="stat-value text-success">{{ $summary['total_deliveries'] }}</div>
                <div class="stat-desc">{{ ucfirst($reportType) }} period</div>
            </div>

            <!-- Total Payments -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-accent">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
                <div class="stat-title">Payments</div>
                <div class="stat-value text-accent">{{ $summary['total_payments'] }}</div>
                <div class="stat-desc">{{ ucfirst($reportType) }} period</div>
            </div>

            <!-- Total Revenue -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-info">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
                <div class="stat-title">Total Revenue</div>
                <div class="stat-value text-info">${{ number_format($summary['total_payment_value'], 2) }}</div>
                <div class="stat-desc">{{ ucfirst($reportType) }} period</div>
            </div>
        </div>

        <!-- Report Tabs -->
        <div class="tabs tabs-boxed bg-base-100 shadow-lg mb-6">
            <a class="tab tab-active" onclick="showReportTab('delivered-orders')">Delivered Orders</a>
            <a class="tab" onclick="showReportTab('deliveries')">Deliveries</a>
            <a class="tab" onclick="showReportTab('payments')">Payments</a>
        </div>

        <!-- Delivered Orders Table -->
        <div id="delivered-orders" class="report-tab">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-primary">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        Delivered Orders
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Products</th>
                                    <th>Total Value</th>
                                    <th>Payment Progress</th>
                                    <th>Delivery Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deliveredOrders as $order)
                                    <tr>
                                        <td>
                                            <span class="font-mono font-bold">{{ $order->order_number }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-outline">{{ $order->customer->display_name }}</span>
                                        </td>
                                        <td>
                                            <div class="flex flex-col space-y-1">
                                                @foreach($order->items->take(3) as $item)
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-sm">{{ $item->product->name }}</span>
                                                        <span class="badge badge-sm">{{ $item->formatted_quantity }}</span>
                                                    </div>
                                                @endforeach
                                                @if($order->items->count() > 3)
                                                    <span class="text-xs text-base-content/50">+{{ $order->items->count() - 3 }} more</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="font-mono font-bold text-success">
                                            ${{ number_format($order->items->sum('total_price'), 2) }}
                                        </td>
                                        <td>
                                            @php
                                                $totalPaid = $order->payments->sum('amount');
                                                $totalValue = $order->items->sum('total_price');
                                                $progress = $totalValue > 0 ? ($totalPaid / $totalValue) * 100 : 0;
                                            @endphp
                                            <div class="flex items-center space-x-2">
                                                <progress class="progress progress-success w-20" value="{{ $progress }}" max="100"></progress>
                                                <span class="text-xs">{{ number_format($progress, 1) }}%</span>
                                            </div>
                                            <div class="text-xs text-base-content/50">
                                                ${{ number_format($totalPaid, 2) }} / ${{ number_format($totalValue, 2) }}
                                            </div>
                                        </td>
                                        <td>{{ $order->delivery_date ? $order->delivery_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-success">Delivered</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                </path>
                                            </svg>
                                            No delivered orders found for this period
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $deliveredOrders->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Deliveries Table -->
        <div id="deliveries" class="report-tab hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-success">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Deliveries
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Amount</th>
                                    <th>Delivery Date</th>
                                    <th>Delivered By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deliveries as $delivery)
                                    <tr>
                                        <td>
                                            <span class="font-mono font-bold">{{ $delivery->productionOrder->order_number ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-outline">{{ $delivery->customer->display_name }}</span>
                                        </td>
                                        <td>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium">{{ $delivery->product->name ?? 'N/A' }}</span>
                                                <span class="badge badge-sm">{{ $delivery->product->code ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="font-mono">{{ number_format($delivery->quantity, 2) }}</td>
                                        <td class="font-mono">${{ number_format($delivery->unit_price, 2) }}</td>
                                        <td class="font-mono font-bold text-success">
                                            ${{ number_format($delivery->total_amount, 2) }}
                                        </td>
                                        <td>{{ $delivery->delivery_date->format('M d, Y') }}</td>
                                        <td>{{ $delivery->deliveredBy->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            No deliveries found for this period
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $deliveries->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div id="payments" class="report-tab hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-accent">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                        Payments
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Order Number</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Bank Slip Ref</th>
                                    <th>Proforma Invoice</th>
                                    <th>Payment Date</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                    <tr>
                                        <td>
                                            <span class="badge badge-outline">{{ $payment->customer->display_name }}</span>
                                        </td>
                                        <td>
                                            <span class="font-mono font-bold">{{ $payment->productionOrder->order_number ?? 'N/A' }}</span>
                                        </td>
                                        <td class="font-mono font-bold text-accent">
                                            ${{ number_format($payment->amount, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $payment->payment_method ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if ($payment->bank_slip_reference)
                                                <span class="badge badge-success">{{ $payment->bank_slip_reference }}</span>
                                            @else
                                                <span class="text-base-content/50">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($payment->proforma_invoice_number)
                                                <span class="badge badge-warning">{{ $payment->proforma_invoice_number }}</span>
                                            @else
                                                <span class="text-base-content/50">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>{{ $payment->recordedBy->name ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                </path>
                                            </svg>
                                            No payments found for this period
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showReportTab(tabName) {
        // Hide all report tab contents
        document.querySelectorAll('.report-tab').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('tab-active');
        });

        // Show selected tab content
        document.getElementById(tabName).classList.remove('hidden');

        // Add active class to clicked tab
        event.target.classList.add('tab-active');
    }
</script>