<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Revenue Report</h1>
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-2">Payments</h2>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Delivery</th>
                        <th>Amount</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment->customer->name ?? '-' }}</td>
                            <td>{{ $payment->delivery->id ?? '-' }}</td>
                            <td>{{ $payment->amount }}</td>
                            <td>{{ $payment->payment_date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">{{ $payments->links() }}</div>
    </div>
    <div>
        <h2 class="text-xl font-semibold mb-2">Deliveries</h2>
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Batch</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->productionOrder->order_number ?? '-' }}</td>
                            <td>{{ $delivery->customer->name ?? '-' }}</td>
                            <td>{{ $delivery->product->name ?? '-' }}</td>
                            <td>{{ $delivery->quantity }}</td>
                            <td>{{ $delivery->batch_number }}</td>
                            <td>{{ $delivery->delivery_date }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">{{ $deliveries->links() }}</div>
    </div>
</div>
