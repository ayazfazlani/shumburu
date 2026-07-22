<!-- resources/views/livewire/warehouse/finished-goods.blade.php -->
<div class="bx-page bx-page-finished-goods">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Finished Goods Record
            </h1>
            <p class="bx-header-subtitle">Record daily finished goods production</p>
        </div>
        <div class="bx-header-right">
            <a href="{{ route('warehouse.index') }}" class="bx-btn bx-btn-secondary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Warehouse
            </a>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Records</div>
            <div class="bx-stat-value">{{ $finishedGoods->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Today's Production</div>
            <div class="bx-stat-value text-blue">{{ $finishedGoods->where('production_date', today())->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Weight</div>
            <div class="bx-stat-value text-warning">{{ number_format($finishedGoods->sum(function($item) { return ($item->weight_per_meter ?? 0) * ($item->quantity ?? 0); }), 2) }} kg</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Waste</div>
            <div class="bx-stat-value text-danger">{{ number_format($finishedGoods->sum('waste_quantity'), 2) }} kg</div>
        </div>
    </div>

    <!-- ─── ALERTS ─── -->
    @if (session('message'))
        <div class="bx-alert bx-alert-success">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bx-alert bx-alert-danger">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- ─── FORM & INFO PANEL ─── -->
    <div class="bx-grid-2-1">
        <!-- Form -->
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @if($isEditing) Edit Finished Goods Record @else New Finished Goods Record @endif
                </h3>
            </div>
            <div class="bx-card-body">
                <form wire:submit.prevent="save" class="bx-form" id="finishedGoodsForm">
                    <!-- Product -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Product</label>
                        <select wire:model="product_id" class="bx-select @error('product_id') bx-input-error @enderror">
                            <option value="">Select Product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Type & Length -->
                    <div class="bx-form-group">
                        <label class="bx-form-label required">Type</label>
                        <select wire:model="type" class="bx-select @error('type') bx-input-error @enderror">
                            <option value="roll">Roll</option>
                            <option value="cut">Cut</option>
                        </select>
                        @error('type')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bx-form-group">
                        <label class="bx-form-label required">Length (m)</label>
                        <input type="number" wire:model="length_m" min="0.01" step="0.01"
                               class="bx-input @error('length_m') bx-input-error @enderror"
                               placeholder="Length in meters" />
                        @error('length_m')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Surface & Outer Diameter -->
                    <div class="bx-form-group">
                        <label class="bx-form-label">Surface</label>
                        <select wire:model="Surface" class="bx-select @error('Surface') bx-input-error @enderror">
                            <option value="">Select Surface</option>
                            <option value="smooth">Smooth</option>
                            <option value="rough">Rough</option>
                        </select>
                        @error('Surface')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bx-form-group">
                        <label class="bx-form-label">Outer Diameter</label>
                        <input type="number" wire:model="outerDiameter" min="0.01" step="0.01"
                               class="bx-input @error('outerDiameter') bx-input-error @enderror"
                               placeholder="Outer diameter" />
                        @error('outerDiameter')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Quantity & Batch -->
                    <div class="bx-form-group">
                        <label class="bx-form-label required">Quantity</label>
                        <input type="number" wire:model="quantity" min="0.01" step="0.01"
                               class="bx-input @error('quantity') bx-input-error @enderror"
                               placeholder="Quantity" />
                        @error('quantity')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bx-form-group">
                        <label class="bx-form-label required">Batch Number</label>
                        <input type="text" wire:model="batch_number"
                               class="bx-input @error('batch_number') bx-input-error @enderror"
                               placeholder="Batch Number" />
                        @error('batch_number')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Waste & Thickness -->
                    <div class="bx-form-group">
                        <label class="bx-form-label">Waste Quantity (kg)</label>
                        <input type="number" wire:model="waste_quantity" min="0" step="0.01"
                               class="bx-input @error('waste_quantity') bx-input-error @enderror"
                               placeholder="Waste quantity in kg" />
                        @error('waste_quantity')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bx-form-group">
                        <label class="bx-form-label">Thickness</label>
                        <input type="text" wire:model="thickness"
                               class="bx-input @error('thickness') bx-input-error @enderror"
                               placeholder="Thickness" />
                        @error('thickness')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Ovality -->
                    <div class="bx-form-group">
                        <label class="bx-form-label">Start Ovality</label>
                        <input type="text" wire:model="startOvality"
                               class="bx-input @error('startOvality') bx-input-error @enderror"
                               placeholder="Enter start ovality" />
                        @error('startOvality')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bx-form-group">
                        <label class="bx-form-label">End Ovality</label>
                        <input type="text" wire:model="endOvality"
                               class="bx-input @error('endOvality') bx-input-error @enderror"
                               placeholder="Enter end ovality" />
                        @error('endOvality')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Stripe Color & Size -->
                    <div class="bx-form-group">
                        <label class="bx-form-label">Stripe Color</label>
                        <input type="text" wire:model="stripeColor"
                               class="bx-input @error('stripeColor') bx-input-error @enderror"
                               placeholder="Enter stripe color" />
                        @error('stripeColor')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="bx-form-group">
                        <label class="bx-form-label">Size</label>
                        <input type="number" wire:model="size" min="0.01" step="0.01"
                               class="bx-input @error('size') bx-input-error @enderror"
                               placeholder="Size" />
                        @error('size')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Production Date -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Production Date</label>
                        <input type="date" wire:model="production_date"
                               class="bx-input @error('production_date') bx-input-error @enderror" />
                        @error('production_date')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Weight per Meter -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Weight per Meter (kg/m)</label>
                        <input type="number" wire:model="weightPerMeter" min="0.01" step="0.01"
                               class="bx-input @error('weightPerMeter') bx-input-error @enderror"
                               placeholder="Weight per meter in kg/m" />
                        @error('weightPerMeter')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Purpose -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label required">Purpose</label>
                        <select wire:model="purpose" class="bx-select @error('purpose') bx-input-error @enderror">
                            <option value="for_stock">For Stock</option>
                            <option value="for_customer_order">For Customer Order</option>
                        </select>
                        @error('purpose')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Customer (conditional) -->
                    @if ($purpose === 'for_customer_order')
                        <div class="bx-form-group bx-form-full">
                            <label class="bx-form-label required">Customer</label>
                            <select wire:model="customer_id" class="bx-select @error('customer_id') bx-input-error @enderror">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <span class="bx-error">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <!-- Notes -->
                    <div class="bx-form-group bx-form-full">
                        <label class="bx-form-label">Notes</label>
                        <textarea wire:model="notes" rows="3"
                                  class="bx-input @error('notes') bx-input-error @enderror"
                                  placeholder="Additional notes"></textarea>
                        @error('notes')
                            <span class="bx-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="bx-form-group bx-form-full">
                        @if($isEditing)
                            <div class="flex gap-3">
                                <button type="submit" class="bx-btn bx-btn-warning flex-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Update Record
                                </button>
                                <button type="button" wire:click="cancelEdit" class="bx-btn bx-btn-secondary">
                                    Cancel
                                </button>
                            </div>
                        @else
                            <div class="flex gap-3">
                                <button type="submit" class="bx-btn bx-btn-success flex-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Record Finished Goods
                                </button>
                                <button type="reset" wire:click="$refresh" class="bx-btn bx-btn-secondary">
                                    Reset
                                </button>
                            </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Panel -->
        <div class="bx-card">
            <div class="bx-card-header">
                <h3>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Finished Goods Guidelines
                </h3>
            </div>
            <div class="bx-card-body">
                <div class="bx-info-list">
                    <div class="bx-info-item bx-info-accent">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4>Daily Production</h4>
                            <p>Record all finished goods at the end of each shift</p>
                        </div>
                    </div>

                    <div class="bx-info-item bx-info-blue">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4>Traceability</h4>
                            <p>Link each batch to stock out and production line for full traceability</p>
                        </div>
                    </div>

                    <div class="bx-info-item bx-info-green">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4>Quality Control</h4>
                            <p>Ensure all finished goods meet quality standards</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── RECORDS TABLE ─── -->
    <div class="bx-section">
        <div class="bx-section-header">
            <h2>Finished Goods Records</h2>
            <span class="bx-badge bx-badge-secondary">{{ $finishedGoods->total() }} Records</span>
        </div>

        <div class="bx-table-wrap">
            <div class="bx-table-scroll">
                <table class="bx-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="hidden sm:table-cell">Type</th>
                            <th class="hidden md:table-cell">Length</th>
                            <th class="hidden lg:table-cell">Surface</th>
                            <th class="hidden xl:table-cell">Quantity</th>
                            <th class="hidden xl:table-cell">Total Weight</th>
                            <th class="hidden 2xl:table-cell">Waste</th>
                            <th>Batch</th>
                            <th class="hidden lg:table-cell">Purpose</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($finishedGoods as $item)
                            <tr>
                                <td>
                                    <div class="bx-product-cell">
                                        <span class="bx-product-name">{{ $item->product->name ?? 'N/A' }}</span>
                                        <span class="bx-product-code">{{ $item->batch_number ?? '' }}</span>
                                    </div>
                                </td>
                                <td class="hidden sm:table-cell">
                                    <span class="bx-badge {{ $item->type == 'roll' ? 'bx-badge-primary' : 'bx-badge-secondary' }}">
                                        {{ ucfirst($item->type ?? '-') }}
                                    </span>
                                </td>
                                <td class="hidden md:table-cell">{{ number_format($item->length_m ?? 0, 2) }}m</td>
                                <td class="hidden lg:table-cell">
                                    @if($item->surface)
                                        <span class="bx-code">{{ ucfirst($item->surface) }}</span>
                                    @else
                                        <span class="text-gray">—</span>
                                    @endif
                                </td>
                                <td class="hidden xl:table-cell">{{ number_format($item->quantity ?? 0, 2) }}</td>
                                <td class="hidden xl:table-cell">{{ number_format(($item->weight_per_meter ?? 0) * ($item->quantity ?? 0), 2) }} kg</td>
                                <td class="hidden 2xl:table-cell">
                                    <span class="bx-badge {{ ($item->waste_quantity ?? 0) > 0 ? 'bx-badge-danger' : 'bx-badge-success' }}">
                                        {{ number_format($item->waste_quantity ?? 0, 2) }} kg
                                    </span>
                                </td>
                                <td><span class="bx-code">{{ $item->batch_number ?? '-' }}</span></td>
                                <td class="hidden lg:table-cell">
                                    @if($item->purpose)
                                        <span class="bx-badge {{ $item->purpose == 'for_stock' ? 'bx-badge-success' : 'bx-badge-warning' }}">
                                            {{ str_replace('_', ' ', ucfirst($item->purpose)) }}
                                        </span>
                                    @else
                                        <span class="text-gray">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="bx-actions">
                                        <button wire:click="edit({{ $item->id }})"
                                                class="bx-action bx-action-edit"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $item->id }})"
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
                                <td colspan="10" class="bx-empty">
                                    <div class="bx-empty-content">
                                        <div class="bx-empty-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <h3>No finished goods records found</h3>
                                        <p>Start recording your daily production output.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ─── PAGINATION ─── -->
        @if($finishedGoods->hasPages())
            <div class="bx-pagination-wrap">
                <div class="bx-pagination-info">
                    Showing <strong>{{ $finishedGoods->firstItem() ?? 0 }}</strong>
                    to <strong>{{ $finishedGoods->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $finishedGoods->total() }}</strong> entries
                </div>
                <div class="bx-pagination">
                    {{ $finishedGoods->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('scroll-to-form', () => {
            document.getElementById('finishedGoodsForm').scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
