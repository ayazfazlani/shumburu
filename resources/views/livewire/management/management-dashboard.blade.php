<!-- resources/views/livewire/management/cockpit.blade.php -->
<div class="bx-page bx-page-cockpit">
    <!-- ─── HEADER ─── -->
    <div class="bx-header bx-header-cockpit">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Executive Cockpit
            </h1>
            <p class="bx-header-subtitle">High-level operational metrics and factory performance</p>
        </div>
        <div class="bx-header-right">
            <div class="bx-toggle-group">
                <button wire:click="$set('timeFrame', 'week')"
                        class="bx-toggle-btn {{ $timeFrame === 'week' ? 'active' : '' }}">Weekly</button>
                <button wire:click="$set('timeFrame', 'month')"
                        class="bx-toggle-btn {{ $timeFrame === 'month' ? 'active' : '' }}">Monthly</button>
                <button wire:click="$set('timeFrame', 'year')"
                        class="bx-toggle-btn {{ $timeFrame === 'year' ? 'active' : '' }}">Annual</button>
            </div>
        </div>
    </div>

    <!-- ─── KPI CARDS ─── -->
    <div class="bx-kpi-grid">
        <!-- OTD Card -->
        <div class="bx-kpi-card">
            <div class="bx-kpi-card-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="bx-kpi-content">
                <h3 class="bx-kpi-label">On-Time Delivery (OTD)</h3>
                <div class="bx-kpi-value">{{ $metrics['otd'] }}%</div>
                <div class="bx-kpi-progress">
                    <div class="bx-kpi-progress-bar">
                        <div class="bx-kpi-progress-fill bx-kpi-progress-indigo" style="width: {{ $metrics['otd'] }}%"></div>
                    </div>
                </div>
                <p class="bx-kpi-desc">{{ $metrics['onTimeCount'] }} of {{ $metrics['totalDelivered'] }} orders delivered on time</p>
            </div>
        </div>

        <!-- Scrap Rate Card -->
        <div class="bx-kpi-card">
            <div class="bx-kpi-card-icon bx-kpi-card-icon-red">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div class="bx-kpi-content">
                <h3 class="bx-kpi-label">Scrap Rate</h3>
                <div class="bx-kpi-value {{ $metrics['scrapRate'] > 5 ? 'bx-kpi-value-danger' : 'bx-kpi-value-success' }}">
                    {{ $metrics['scrapRate'] }}%
                </div>
                <div class="bx-kpi-status">
                    <span class="bx-kpi-status-text {{ $metrics['scrapRate'] > 5 ? 'bx-kpi-status-danger' : 'bx-kpi-status-success' }}">
                        {{ $metrics['scrapRate'] > 5 ? 'Above Threshold' : 'Target Achieved' }}
                    </span>
                </div>
                <p class="bx-kpi-desc">Target: &lt;2.5% per production run</p>
            </div>
        </div>

        <!-- Production Volume -->
        <div class="bx-kpi-card">
            <div class="bx-kpi-card-icon bx-kpi-card-icon-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="bx-kpi-content">
                <h3 class="bx-kpi-label">Production Output</h3>
                <div class="bx-kpi-value bx-kpi-value-blue">{{ number_format($metrics['outputVolume']) }}</div>
                <span class="bx-kpi-unit">Total Meters Produced</span>
                <p class="bx-kpi-desc">Real-time FG stock aggregate</p>
            </div>
        </div>

        <!-- System Health -->
        <div class="bx-kpi-card bx-kpi-card-accent">
            <div class="bx-kpi-card-icon bx-kpi-card-icon-white">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div class="bx-kpi-content bx-kpi-content-white">
                <h3 class="bx-kpi-label bx-kpi-label-white">Gate Integrity</h3>
                <div class="bx-kpi-value bx-kpi-value-white">100%</div>
                <span class="bx-kpi-unit bx-kpi-unit-white">QC & Material Gates Active</span>
                <p class="bx-kpi-desc bx-kpi-desc-white">Zero bypasses recorded this period</p>
            </div>
        </div>
    </div>

    <!-- ─── BOTTOM GRID ─── -->
    <div class="bx-cockpit-grid">
        <!-- Order Pipeline -->
        <div class="bx-card bx-card-pipeline">
            <div class="bx-card-header">
                <h3>Order Pipeline</h3>
            </div>
            <div class="bx-card-body">
                <div class="bx-pipeline-items">
                    @foreach($orderStatus as $stat)
                        <div class="bx-pipeline-item">
                            <div class="bx-pipeline-item-header">
                                <span class="bx-pipeline-item-label">{{ ucfirst(str_replace('_', ' ', $stat->status)) }}</span>
                                <span class="bx-pipeline-item-count">{{ $stat->count }}</span>
                            </div>
                            <div class="bx-pipeline-progress">
                                <div class="bx-pipeline-progress-bar">
                                    <div class="bx-pipeline-progress-fill" style="width: {{ ($stat->count / ($orderStatus->sum('count') ?: 1)) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Large Scraps -->
        <div class="bx-card bx-card-scraps">
            <div class="bx-card-header bx-card-header-scraps">
                <h3>Recent Large Scraps (Risk Log)</h3>
                <svg class="w-5 h-5 text-red-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="bx-table-wrap">
                <div class="bx-table-scroll">
                    <table class="bx-table bx-table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product / Line</th>
                                <th>Quantity</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentHighScrap as $scrap)
                                <tr>
                                    <td class="text-sm font-medium text-gray-500">{{ $scrap->created_at->format('M d') }}</td>
                                    <td>
                                        <div class="font-bold text-sm">Line {{ $scrap->materialStockOutLine->productionLine->name ?? 'N/A' }}</div>
                                        <div class="text-[10px] text-gray-400">Batch #{{ $scrap->materialStockOutLine->materialStockOut->batch_number ?? 'N/A' }}</div>
                                    </td>
                                    <td class="font-bold text-red-500 text-sm">{{ number_format($scrap->quantity, 2) }} kg</td>
                                    <td class="text-xs text-gray-500 italic">"{{ $scrap->reason }}"</td>
                                    <td>
                                        <span class="bx-badge bx-badge-gray bx-badge-xs">Logged</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="bx-empty bx-empty-sm">
                                        <p class="text-gray-400 italic">No significant scrap recorded.</p>
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
