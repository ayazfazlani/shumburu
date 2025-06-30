<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-primary">📦 Sales Dashboard</h1>
                    <p class="text-base-content/70 mt-1">SPF-HDPE Pipe Factory - Sales & Customer Management</p>
                </div>
                <div class="flex gap-2">
                    <div class="badge badge-primary badge-lg">Sales</div>
                    <div class="badge badge-outline">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Production Orders Stats -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <div class="stat-title">Production Orders</div>
                <div class="stat-value text-primary">{{ $productionOrders->count() }}</div>
                <div class="stat-desc">Total orders</div>
            </div>

            <!-- Pending Orders Stats -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-warning">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="stat-title">Pending Orders</div>
                <div class="stat-value text-warning">{{ $productionOrders->where('status', 'pending')->count() }}</div>
                <div class="stat-desc">Awaiting approval</div>
            </div>

            <!-- Deliveries Stats -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-success">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="stat-title">Today's Deliveries</div>
                <div class="stat-value text-success">{{ $deliveries->where('delivery_date', today())->count() }}</div>
                <div class="stat-desc">Delivered today</div>
            </div>

            <!-- Payments Stats -->
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-accent">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
                <div class="stat-title">Today's Payments</div>
                <div class="stat-value text-accent">{{ $payments->where('payment_date', today())->count() }}</div>
                <div class="stat-desc">Received today</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <a href="{{ route('sales.create-order') }}" class="btn btn-primary btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Order
            </a>
            <a href="{{ route('sales.deliveries') }}" class="btn btn-success btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                Record Delivery
            </a>
            <a href="{{ route('sales.payments') }}" class="btn btn-accent btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                    </path>
                </svg>
                Record Payment
            </a>
            <a href="{{ route('sales.reports') }}" class="btn btn-info btn-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                Sales Reports
            </a>
        </div>

        <!-- Sales Activity Tabs -->
        <div class="tabs tabs-boxed bg-base-100 shadow-lg mb-6">
            <a class="tab tab-active" onclick="showTab('production-orders')">Production Orders</a>
            <a class="tab" onclick="showTab('deliveries')">Deliveries</a>
            <a class="tab" onclick="showTab('payments')">Payments</a>
        </div>

        <!-- Production Orders Table -->
        <div id="production-orders" class="tab-content">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-primary">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        Production Orders
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Order Number</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Requested Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($productionOrders->take(5) as $order)
                                    <tr>
                                        <td>
                                            <span class="font-mono font-bold">{{ $order->order_number }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-outline">{{ $order->customer->display_name }}</span>
                                        </td>
                                        <td>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium">{{ $order->product->name }}</span>
                                                <span class="badge badge-sm">{{ $order->product->code }}</span>
                                            </div>
                                        </td>
                                        <td class="font-mono">{{ number_format($order->quantity) }}</td>
                                        <td>
                                            @if ($order->status === 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($order->status === 'approved')
                                                <span class="badge badge-info">Approved</span>
                                            @elseif($order->status === 'in_production')
                                                <span class="badge badge-primary">In Production</span>
                                            @elseif($order->status === 'completed')
                                                <span class="badge badge-success">Completed</span>
                                            @else
                                                <span class="badge badge-accent">Delivered</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->requested_date->format('M d, Y') }}</td>
                                        <td>
                                            <div class="flex space-x-2">
                                                <button class="btn btn-xs btn-outline">View</button>
                                                @if ($order->status === 'pending')
                                                    <button class="btn btn-xs btn-primary">Edit</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                                </path>
                                            </svg>
                                            No production orders found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deliveries Table -->
        <div id="deliveries" class="tab-content hidden">
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
                                    <th>Total Amount</th>
                                    <th>Delivery Date</th>
                                    <th>Delivered By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deliveries->take(5) as $delivery)
                                    <tr>
                                        <td>
                                            <span
                                                class="font-mono font-bold">{{ $delivery->productionOrder->order_number }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-outline">{{ $delivery->customer->display_name }}</span>
                                        </td>
                                        <td>
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium">{{ $delivery->product->name }}</span>
                                                <span class="badge badge-sm">{{ $delivery->product->code }}</span>
                                            </div>
                                        </td>
                                        <td class="font-mono">{{ number_format($delivery->quantity) }}</td>
                                        <td class="font-mono font-bold text-success">
                                            ${{ number_format($delivery->total_amount, 2) }}</td>
                                        <td>{{ $delivery->delivery_date->format('M d, Y') }}</td>
                                        <td>{{ $delivery->deliveredBy->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                </path>
                                            </svg>
                                            No deliveries found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Table -->
        <div id="payments" class="tab-content hidden">
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
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Bank Slip Ref</th>
                                    <th>Proforma Invoice</th>
                                    <th>Payment Date</th>
                                    <th>Recorded By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments->take(5) as $payment)
                                    <tr>
                                        <td>
                                            <span
                                                class="badge badge-outline">{{ $payment->customer->display_name }}</span>
                                        </td>
                                        <td class="font-mono font-bold text-accent">
                                            ${{ number_format($payment->amount, 2) }}</td>
                                        <td>
                                            <span
                                                class="badge badge-info">{{ $payment->payment_method ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if ($payment->bank_slip_reference)
                                                <span
                                                    class="badge badge-success">{{ $payment->bank_slip_reference }}</span>
                                            @else
                                                <span class="text-base-content/50">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($payment->proforma_invoice_number)
                                                <span
                                                    class="badge badge-warning">{{ $payment->proforma_invoice_number }}</span>
                                            @else
                                                <span class="text-base-content/50">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                        <td>{{ $payment->recordedBy->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-8 text-base-content/50">
                                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                </path>
                                            </svg>
                                            No payments found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
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
