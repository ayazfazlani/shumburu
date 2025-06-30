<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Waste Report</h1>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Batch</th>
                    <th>Line</th>
                    <th>Quantity</th>
                    <th>Reason</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($wasteRecords as $item)
                    <tr>
                        <td>{{ $item->waste_date ?? '-' }}</td>
                        <td>{{ $item->materialStockOutLine->materialStockOut->batch_number ?? '-' }}</td>
                        <td>{{ $item->materialStockOutLine->productionLine->name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->reason }}</td>
                        <td>{{ $item->recordedBy->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $wasteRecords->links() }}</div>
</div>
