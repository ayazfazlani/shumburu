<div class="min-h-screen bg-base-200">
    <!-- Header -->
    <div class="bg-base-100 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-primary">📦 Inventory Dashboard</h1>
                    <p class="text-base-content/70 mt-1">Real-time inventory visibility for all teams</p>
                </div>
                <div class="flex gap-2">
                    <button class="btn btn-outline btn-primary" onclick="window.print()">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Report
                    </button>
                    <a href="{{ route('warehouse.index') }}" class="btn btn-outline btn-warning">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Warehouse
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6">
        <!-- Search and Filters -->
        <div class="card bg-base-100 shadow-lg mb-6">
            <div class="card-body">
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-64">
                        <label class="label">Search Products</label>
                        <input type="text" wire:model.live="search" placeholder="Search by product name..." 
                               class="input input-bordered w-full">
                    </div>
                    <div>
                        <label class="label">Type</label>
                        <select wire:model.live="filter_type" class="select select-bordered">
                            <option value="all">All Items</option>
                            <option value="raw_materials">Raw Materials</option>
                            <option value="finished_goods">Finished Goods</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Status</label>
                        <select wire:model.live="filter_status" class="select select-bordered">
                            <option value="all">All Status</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Summary -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="stat-title">Raw Materials</div>
                <div class="stat-value text-primary">{{ $inventorySummary['total_raw_materials'] }}</div>
                <div class="stat-desc">{{ $inventorySummary['low_stock_raw_materials'] }} low stock</div>
            </div>

            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-secondary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="stat-title">Finished Goods</div>
                <div class="stat-value text-secondary">{{ $inventorySummary['total_finished_goods'] }}</div>
                <div class="stat-desc">{{ $inventorySummary['low_stock_finished_goods'] }} low stock</div>
            </div>

            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-accent">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="stat-title">Total Value</div>
                <div class="stat-value text-accent">${{ number_format($inventorySummary['total_value'], 2) }}</div>
                <div class="stat-desc">Inventory value</div>
            </div>

            <div class="stat bg-base-100 shadow-lg rounded-lg">
                <div class="stat-figure text-warning">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="stat-title">Low Stock Items</div>
                <div class="stat-value text-warning">{{ $inventorySummary['low_stock_raw_materials'] + $inventorySummary['low_stock_finished_goods'] }}</div>
                <div class="stat-desc">Need attention</div>
            </div>
        </div>

        <!-- Raw Materials Inventory -->
        @if($filter_type === 'all' || $filter_type === 'raw_materials')
            <div class="card bg-base-100 shadow-lg mb-6">
                <div class="card-body">
                    <h2 class="card-title text-primary mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Raw Materials Inventory
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Material Name</th>
                                    <th>Current Stock (kg)</th>
                                    <th>Unit Price</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rawMaterials as $material)
                                    <tr>
                                        <td class="font-semibold">{{ $material->name }}</td>
                                        <td>{{ number_format($material->quantity, 2) }}</td>
                                        <td>${{ number_format($material->unit_price, 2) }}</td>
                                        <td>${{ number_format($material->quantity * $material->unit_price, 2) }}</td>
                                        <td>
                                            @if($material->quantity < 100)
                                                <div class="badge badge-warning">Low Stock</div>
                                            @elseif($material->quantity == 0)
                                                <div class="badge badge-error">Out of Stock</div>
                                            @else
                                                <div class="badge badge-success">In Stock</div>
                                            @endif
                                        </td>
                                        <td>{{ $material->updated_at->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-gray-500 py-8">No raw materials found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Finished Goods Inventory -->
        @if($filter_type === 'all' || $filter_type === 'finished_goods')
            <div class="card bg-base-100 shadow-lg">
                <div class="card-body">
                    <h2 class="card-title text-secondary mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Finished Goods Inventory
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Size</th>
                                    <th>Quantity</th>
                                    <th>Length (m)</th>
                                    <th>Type</th>
                                    <th>Batch #</th>
                                    <th>Production Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($finishedGoods as $good)
                                    <tr>
                                        <td class="font-semibold">{{ $good->product->name ?? 'Unknown' }}</td>
                                        <td>{{ $good->size ?? '-' }}</td>
                                        <td>{{ $good->quantity }}</td>
                                        <td>{{ $good->length_m ?? '-' }}</td>
                                        <td>
                                            <div class="badge badge-outline">{{ ucfirst($good->type) }}</div>
                                        </td>
                                        <td>{{ $good->batch_number ?? '-' }}</td>
                                        <td>{{ $good->production_date ? $good->production_date->format('Y-m-d') : '-' }}</td>
                                        <td>
                                            @if($good->quantity < 10)
                                                <div class="badge badge-warning">Low Stock</div>
                                            @elseif($good->quantity == 0)
                                                <div class="badge badge-error">Out of Stock</div>
                                            @else
                                                <div class="badge badge-success">In Stock</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-gray-500 py-8">No finished goods found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
    }
}
</style>
