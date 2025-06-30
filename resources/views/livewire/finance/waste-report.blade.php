<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Finance Waste Report</h1>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Material</th>
                    <th>Quantity</th>
                    <th>Reason</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($scrapWaste as $item)
                    <tr>
                        <td>{{ $item->date ?? ($item->waste_date ?? '-') }}</td>
                        <td>{{ $item->rawMaterial->name ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->reason }}</td>
                        <td>{{ $item->recordedBy->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $scrapWaste->links() }}</div>
</div>
