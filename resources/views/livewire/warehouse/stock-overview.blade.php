<div>
<!-- resources/views/livewire/warehouse/fg-stock.blade.php -->
<div class="bx-page bx-page-fg-stock">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Finished Goods Stock
            </h1>
            <p class="bx-header-subtitle">Real-time inventory of produced goods available for dispatch</p>
        </div>
        <div class="bx-header-right">
            <div class="bx-search">
                <svg class="bx-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by product or batch..." class="bx-search-input" />
            </div>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Products</div>
            <div class="bx-stat-value">{{ $stocks->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Quantity</div>
            <div class="bx-stat-value text-blue">{{ number_format($stocks->sum('quantity'), 2) }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Reserved</div>
            <div class="bx-stat-value text-warning">{{ number_format($stocks->sum('reserved_quantity'), 2) }}</div>
        </div>
        <class="bx-stat">
            <div class="bx-stat-label">Available</div>
            <div class="bx-stat-value text-success">{{ number_format($stocks->sum('available_quantity'), 2) }}</div>
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Batch Number</th>
                        <th>Total Physical</th>
                        <th>Reserved</th>
                        <th>Available</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr>
                            <td>
                                <div class="bx-product-cell">
                                    <span class="bx-product-name">{{ $stock->product->name }}</span>
                                    <span class="bx-product-code">{{ $stock->product->code }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="bx-code">{{ $stock->batch_number ?? 'N/A' }}</span>
                            </td>
                            <td class="font-semibold">{{ number_format($stock->quantity, 2) }}</td>
                            <td>
                                <span class="bx-stock-reserved">{{ number_format($stock->reserved_quantity, 2) }}</span>
                            </td>
                            <td>
                                <span class="bx-stock-available">{{ number_format($stock->available_quantity, 2) }}</span>
                            </td>
                            <td>
                                @php
                                    $statusConfig = [
                                        'available' => ['class' => 'bx-badge-success', 'label' => 'Available'],
                                        'reserved' => ['class' => 'bx-badge-warning', 'label' => 'Reserved'],
                                        'dispatched' => ['class' => 'bx-badge-gray', 'label' => 'Dispatched'],
                                    ];
                                    $config = $statusConfig[$stock->status] ?? ['class' => 'bx-badge-info', 'label' => ucfirst($stock->status)];
                                @endphp
                                <span class="bx-badge {{ $config['class'] }}">{{ $config['label'] }}</span>
                            </td>
                            <td>{{ $stock->location ?? 'Main Warehouse' }}</td>
                            <td class="text-xs text-gray-400">{{ $stock->updated_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="bx-empty">
                                <div class="bx-empty-content">
                                    <div class="bx-empty-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <h3>No stock found in inventory</h3>
                                    <p>Finished goods will appear here once produced.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── PAGINATION ─── -->
    @if($stocks->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                Showing <strong>{{ $stocks->firstItem() ?? 0 }}</strong>
                to <strong>{{ $stocks->lastItem() ?? 0 }}</strong>
                of <strong>{{ $stocks->total() }}</strong> items
            </div>
            <div class="bx-pagination">
                {{ $stocks->links() }}
            </div>
        </div>
    @endif
</div>

</div>
