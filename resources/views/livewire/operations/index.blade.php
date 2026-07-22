<!-- resources/views/livewire/operations/dashboard.blade.php -->
<div class="bx-page bx-page-operations">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Operations Dashboard
            </h1>
            <p class="bx-header-subtitle">Factory Operations & Production Monitoring</p>
        </div>
        <div class="bx-header-right">
            <span class="bx-badge bx-badge-primary">Operations</span>
            <span class="bx-badge bx-badge-secondary">{{ auth()->user()->name }}</span>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats-grid">
        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Material Usage</div>
            <div class="bx-stat-card-value text-primary">{{ $materialUsage->total() }}</div>
            <div class="bx-stat-card-desc">Active material usage records</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-danger">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Waste Records</div>
            <div class="bx-stat-card-value text-danger">{{ $wasteRecords->total() }}</div>
            <div class="bx-stat-card-desc">Total waste records</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-warning">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Downtime</div>
            <div class="bx-stat-card-value text-warning">0</div>
            <div class="bx-stat-card-desc">(Feature coming soon)</div>
        </div>

        <div class="bx-stat-card">
            <div class="bx-stat-card-icon text-success">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="bx-stat-card-label">Finished Goods</div>
            <div class="bx-stat-card-value text-success">{{ $finishedGoods->total() }}</div>
            <div class="bx-stat-card-desc">Total finished goods</div>
        </div>
    </div>

    <!-- ─── TABS ─── -->
    <div class="bx-tabs-container">
        <div class="bx-tabs">
            <button class="bx-tab active" data-tab="material-usage" onclick="switchOpTab('material-usage')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Material Usage
            </button>
            <button class="bx-tab" data-tab="waste-records" onclick="switchOpTab('waste-records')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Waste Records
            </button>
            <button class="bx-tab" data-tab="downtime-records" onclick="switchOpTab('downtime-records')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Downtime
            </button>
            <button class="bx-tab" data-tab="finished-goods" onclick="switchOpTab('finished-goods')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Finished Goods
            </button>
        </div>
    </div>

    <!-- ─── TAB CONTENT: Material Usage ─── -->
    <div id="tab-material-usage" class="bx-tab-content active">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Material Usage (In Process)
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
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
                            @forelse ($materialUsage as $index => $item)
                                <tr>
                                    <td>{{ $materialUsage->firstItem() + $index }}</td>
                                    <td>{{ $item->rawMaterial->name ?? '-' }}</td>
                                    <td>{{ $item->issuedBy->name ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        <span class="bx-badge bx-badge-info">{{ ucfirst($item->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="bx-actions">
                                            <button wire:click="editMaterialUsage({{ $item->id }})"
                                                    class="bx-action bx-action-edit"
                                                    title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="deleteMaterialUsage({{ $item->id }})"
                                                    class="bx-action bx-action-delete"
                                                    onclick="return confirm('Are you sure?')"
                                                    title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                                </svg>
                                            </div>
                                            <h3>No material usage records</h3>
                                            <p>Material usage records will appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($materialUsage->hasPages())
                <div class="bx-pagination-wrap">
                    {{ $materialUsage->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ─── TAB CONTENT: Waste Records ─── -->
    <div id="tab-waste-records" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Waste Records
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
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
                            @forelse ($wasteRecords as $index => $item)
                                <tr>
                                    <td>{{ $wasteRecords->firstItem() + $index }}</td>
                                    <td>{{ $item->rawMaterial->name ?? '-' }}</td>
                                    <td>{{ $item->recordedBy->name ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ Str::limit($item->notes ?? '-', 30) }}</td>
                                    <td>
                                        <div class="bx-actions">
                                            <button wire:click="editWasteRecord({{ $item->id }})"
                                                    class="bx-action bx-action-edit"
                                                    title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="deleteWasteRecord({{ $item->id }})"
                                                    class="bx-action bx-action-delete"
                                                    onclick="return confirm('Are you sure?')"
                                                    title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </div>
                                            <h3>No waste records found</h3>
                                            <p>Waste records will appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($wasteRecords->hasPages())
                <div class="bx-pagination-wrap">
                    {{ $wasteRecords->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- ─── TAB CONTENT: Downtime Records ─── -->
    <div id="tab-downtime-records" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Downtime Records
                </h3>
            </div>
            <div class="bx-card-body">
                <div class="bx-empty-state bx-empty-state-sm">
                    <div class="bx-empty-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3>No records to show yet</h3>
                    <p>Downtime records will appear here.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── TAB CONTENT: Finished Goods ─── -->
    <div id="tab-finished-goods" class="bx-tab-content">
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Finished Goods
                </h3>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table">
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
                            @forelse ($finishedGoods as $index => $item)
                                <tr>
                                    <td>{{ $finishedGoods->firstItem() + $index }}</td>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td>{{ $item->customer->name ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->producedBy->name ?? '-' }}</td>
                                    <td>
                                        <div class="bx-actions">
                                            <button wire:click="editFinishedGood({{ $item->id }})"
                                                    class="bx-action bx-action-edit"
                                                    title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button wire:click="deleteFinishedGood({{ $item->id }})"
                                                    class="bx-action bx-action-delete"
                                                    onclick="return confirm('Are you sure?')"
                                                    title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="bx-empty">
                                        <div class="bx-empty-content">
                                            <div class="bx-empty-icon">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <h3>No finished goods found</h3>
                                            <p>Finished goods records will appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($finishedGoods->hasPages())
                <div class="bx-pagination-wrap">
                    {{ $finishedGoods->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function switchOpTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.bx-tab-content').forEach(content => {
            content.classList.remove('active');
        });

        // Remove active class from all tabs
        document.querySelectorAll('.bx-tab').forEach(tab => {
            tab.classList.remove('active');
        });

        // Show selected tab content
        const targetContent = document.getElementById('tab-' + tabName);
        if (targetContent) {
            targetContent.classList.add('active');
        }

        // Add active class to clicked tab
        const targetTab = document.querySelector(`.bx-tab[data-tab="${tabName}"]`);
        if (targetTab) {
            targetTab.classList.add('active');
        }
    }
</script>
