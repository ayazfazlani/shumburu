<!-- resources/views/livewire/warehouse/demand-aggregation.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            Inventory Demand Aggregator
        </h1>
        <p class="bx-header-subtitle">Combine shortages from multiple production orders into bulk procurement requests</p>
    </div>

    <!-- ─── ALERTS ─── -->
    @if (session()->has('success'))
        <div class="bx-alert bx-alert-success">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bx-alert bx-alert-danger">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Materials</div>
            <div class="bx-stat-value">{{ $aggregatedDemands->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Shortage</div>
            <div class="bx-stat-value text-red-500">
                {{ number_format($aggregatedDemands->sum('shortage'), 2) }}
            </div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Orders Waiting</div>
            <div class="bx-stat-value text-warning">{{ $aggregatedDemands->sum('order_count') }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Bulk PRs Created</div>
            <div class="bx-stat-value text-blue">{{ $bulkPRsCreated ?? 0 }}</div>
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th>Raw Material</th>
                        <th class="text-center">In Stock</th>
                        <th class="text-center">Total Demand</th>
                        <th class="text-center">Orders Waiting</th>
                        <th class="text-center">Shortage</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($aggregatedDemands as $item)
                        <tr wire:key="demand-{{ $item['material_id'] }}" class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors group">
                            <td>
                                <div class="flex flex-col">
                                    <span class="text-base font-bold text-gray-900 dark:text-white">{{ $item['name'] }}</span>
                                    <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Base Unit: {{ $item['unit'] }}</span>
                                </div>
                            </td>
                            <td class="text-center font-bold text-gray-500">{{ number_format($item['in_stock'], 2) }}</td>
                            <td class="text-center font-bold text-gray-500">{{ number_format($item['total_required'], 2) }}</td>
                            <td class="text-center">
                                <span class="bx-code">
                                    {{ $item['order_count'] }} Orders
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="text-xl font-bold text-red-500">-{{ number_format($item['shortage'], 2) }}</span>
                            </td>
                            <td class="text-right">
                                <button wire:click="openPrModal({{ $item['material_id'] }}, {{ $item['shortage'] }})"
                                        class="bx-btn bx-btn-primary bx-btn-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Create Bulk PR
                                </button>
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
                                    <h3>All Inventory Demands Fulfilled</h3>
                                    <p>No shortages detected. All production orders are fully stocked.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── PR MODAL ─── -->
    @if($showPrModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showPrModal', false)">
            <div class="bx-modal">
                <form wire:submit.prevent="raiseBulkPR">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            Issue Bulk Requisition
                        </h3>
                        <button type="button" wire:click="$set('showPrModal', false)" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Combining multiple orders into one bulk procurement request for Finance.
                        </p>

                        <div class="bx-form">
                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label required">Quantity to Purchase</label>
                                    <div class="relative">
                                        <input type="number"
                                               step="0.01"
                                               wire:model="bulkQuantity"
                                               class="bx-input bx-input-lg"
                                               placeholder="Enter quantity" />
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-400 uppercase">
                                            {{ $selectedUnit ?? 'UNIT' }}
                                        </span>
                                    </div>
                                    @error('bulkQuantity')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label">Procurement Notes</label>
                                    <textarea wire:model="bulkNotes"
                                              class="bx-input"
                                              rows="4"
                                              placeholder="E.g. urgent requirement for export orders..."></textarea>
                                    @error('bulkNotes')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="$set('showPrModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Send to Finance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
