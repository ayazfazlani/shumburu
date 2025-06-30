<div class="p-4 space-y-8">

    {{-- Material Usage --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Material Usage (In Process)</h2>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Material</th>
                            <th>Issued By</th>
                            <th>Quantity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materialUsage as $index => $item)
                            <tr>
                                <td>{{ $materialUsage->firstItem() + $index }}</td>
                                <td>{{ $item->rawMaterial->name ?? '-' }}</td>
                                <td>{{ $item->issuedBy->name ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ ucfirst($item->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $materialUsage->links() }}
            </div>
        </div>
    </div>

    {{-- Waste Records --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Waste Records</h2>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Material</th>
                            <th>Recorded By</th>
                            <th>Quantity</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($wasteRecords as $index => $item)
                            <tr>
                                <td>{{ $wasteRecords->firstItem() + $index }}</td>
                                <td>{{ $item->rawMaterial->name ?? '-' }}</td>
                                <td>{{ $item->recordedBy->name ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->notes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $wasteRecords->links() }}
            </div>
        </div>
    </div>

    {{-- Downtime Records (Static/Empty for Now) --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Downtime Records</h2>
            <p>No records to show yet.</p>
        </div>
    </div>

    {{-- Finished Goods --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title text-lg">Finished Goods</h2>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Customer</th>
                            <th>Quantity</th>
                            <th>Produced By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($finishedGoods as $index => $item)
                            <tr>
                                <td>{{ $finishedGoods->firstItem() + $index }}</td>
                                <td>{{ $item->product->name ?? '-' }}</td>
                                <td>{{ $item->customer->name ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->producedBy->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $finishedGoods->links() }}
            </div>
        </div>
    </div>

</div>
