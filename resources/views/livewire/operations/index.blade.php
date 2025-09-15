<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-primary">⚙️ Operations Dashboard</h1>
                    <p class="text-base-content/70 mt-1">Factory Operations & Production Monitoring</p>
                </div>
                <div class="flex gap-2">
                    <div class="badge badge-primary badge-lg">Operations</div>
                    <div class="badge badge-outline">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="container mx-auto px-4 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-title">Material Usage (In Process)</div>
                <div class="stat-value text-primary">{{ $materialUsage->total() }}</div>
                <div class="stat-desc">Active material usage records</div>
            </div>
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-title">Waste Records</div>
                <div class="stat-value text-error">{{ $wasteRecords->total() }}</div>
                <div class="stat-desc">Total waste records</div>
            </div>
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-title">Downtime Records</div>
                <div class="stat-value text-warning">0</div>
                <div class="stat-desc">(Feature coming soon)</div>
            </div>
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-title">Finished Goods</div>
                <div class="stat-value text-success">{{ $finishedGoods->total() }}</div>
                <div class="stat-desc">Total finished goods</div>
            </div>
        </div>

        <!-- Tabs for Sections -->
        <div class="tabs tabs-boxed bg-base-100 shadow-lg mb-6">
            <a class="tab tab-active" onclick="showOpTab('material-usage')">Material Usage</a>
            <a class="tab" onclick="showOpTab('waste-records')">Waste Records</a>
            <a class="tab" onclick="showOpTab('downtime-records')">Downtime</a>
            <a class="tab" onclick="showOpTab('finished-goods')">Finished Goods</a>
        </div>

        <!-- Material Usage Table -->
        <div id="material-usage" class="tab-content">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-primary">Material Usage (In Process)</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Material</th>
                                    <th>Issued By</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($materialUsage as $index => $item)
                                    <tr>
                                        <td>{{ $materialUsage->firstItem() + $index }}</td>
                                        <td>{{ $item->rawMaterial->name ?? '-' }}</td>
                                        <td>{{ $item->issuedBy->name ?? '-' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td><span class="badge badge-info">{{ ucfirst($item->status) }}</span></td>
                                        <td class="text-right flex gap-2 justify-end">
                                            <button class="btn btn-xs btn-outline" wire:click="editMaterialUsage({{ $item->id }})">Edit</button>
                                            <button class="btn btn-xs btn-error" wire:click="deleteMaterialUsage({{ $item->id }})">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $materialUsage->links() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Waste Records Table -->
        <div id="waste-records" class="tab-content hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-error">Waste Records</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Material</th>
                                    <th>Recorded By</th>
                                    <th>Quantity</th>
                                    <th>Notes</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wasteRecords as $index => $item)
                                    <tr>
                                        <td>{{ $wasteRecords->firstItem() + $index }}</td>
                                        {{-- <td>{{ $item->rawMaterial->name ?? '-' }}</td> --}}
                                        <td>{{ $item->recordedBy->name ?? '-' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->notes }}</td>
                                        <td class="text-right flex gap-2 justify-end">
                                            <button class="btn btn-xs btn-outline" wire:click="editWasteRecord({{ $item->id }})">Edit</button>
                                            <button class="btn btn-xs btn-error" wire:click="deleteWasteRecord({{ $item->id }})">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $wasteRecords->links() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Downtime Records Table -->
        <div id="downtime-records" class="tab-content hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-warning">Downtime Records</h2>
                    <p>No records to show yet.</p>
                </div>
            </div>
        </div>

        <!-- Finished Goods Table -->
        <div id="finished-goods" class="tab-content hidden">
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-success">Finished Goods</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Customer</th>
                                    <th>Quantity</th>
                                    <th>Produced By</th>
                                    <th class="text-right">Actions</th>
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
                                        <td class="text-right flex gap-2 justify-end">
                                            <button class="btn btn-xs btn-outline" wire:click="editFinishedGood({{ $item->id }})">Edit</button>
                                            <button class="btn btn-xs btn-error" wire:click="deleteFinishedGood({{ $item->id }})">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">{{ $finishedGoods->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showOpTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.querySelectorAll('.tab').forEach(tab => {
            tab.classList.remove('tab-active');
        });
        document.getElementById(tabName).classList.remove('hidden');
        event.target.classList.add('tab-active');
    }
</script>
