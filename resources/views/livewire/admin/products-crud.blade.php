<!-- resources/views/livewire/admin/products-crud.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Products Management
        </h1>
        <p class="bx-header-subtitle">Create, edit, and manage finished products</p>
    </div>

    <!-- ─── TOOLBAR ─── -->
    <div class="bx-toolbar">
        <div class="bx-toolbar-left">
            <div class="bx-search">
                <svg class="bx-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search products..."
                       class="bx-search-input" />
            </div>
            <select wire:model.live="perPage" class="bx-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>
        <div class="bx-toolbar-right">
            <button wire:click="openCreateModal" class="bx-btn bx-btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">New Product</span>
                <span class="sm:hidden">Add</span>
            </button>
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

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Products</div>
            <div class="bx-stat-value">{{ $products->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Active</div>
            <div class="bx-stat-value text-green">{{ $products->where('is_active', true)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Inactive</div>
            <div class="bx-stat-value text-gray">{{ $products->where('is_active', false)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">New This Month</div>
            <div class="bx-stat-value text-blue">{{ $products->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th class="w-16">ID</th>
                        <th>Name</th>
                        <th class="hidden sm:table-cell">Code</th>
                        <th class="hidden md:table-cell">Size</th>
                        <th class="hidden lg:table-cell">PN</th>
                        <th class="hidden xl:table-cell">Weight/M</th>
                        <th class="hidden xl:table-cell">Length</th>
                        <th class="hidden 2xl:table-cell">Description</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr wire:key="product-{{ $product->id }}">
                            <td class="text-gray font-mono text-sm">{{ $product->id }}</td>
                            <td class="font-medium">{{ $product->name }}</td>
                            <td class="hidden sm:table-cell">
                                <span class="bx-code">{{ $product->code }}</span>
                            </td>
                            <td class="hidden md:table-cell">{{ $product->size ?? '—' }}</td>
                            <td class="hidden lg:table-cell">
                                <span class="bx-code">{{ $product->pn ?? '—' }}</span>
                            </td>
                            <td class="hidden xl:table-cell">{{ $product->weight_per_meter ?? '—' }}</td>
                            <td class="hidden xl:table-cell">{{ $product->meter_length ?? '—' }}</td>
                            <td class="hidden 2xl:table-cell">{{ Str::limit($product->description ?? '—', 30) }}</td>
                            <td>
                                @if ($product->is_active)
                                    <span class="bx-badge bx-badge-success">Active</span>
                                @else
                                    <span class="bx-badge bx-badge-gray">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="bx-actions">
                                    <button wire:click="openEditModal({{ $product->id }})"
                                            class="bx-action bx-action-edit"
                                            title="Edit product">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $product->id }})"
                                            class="bx-action bx-action-delete"
                                            title="Delete product">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                    <h3>No products found</h3>
                                    <p>{{ $search ? 'Try adjusting your search terms.' : 'Get started by creating your first product.' }}</p>
                                    @if(!$search)
                                        <button wire:click="openCreateModal" class="bx-btn bx-btn-primary">Create Product</button>
                                    @else
                                        <button wire:click="$set('search', '')" class="bx-btn bx-btn-secondary">Clear Search</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── PAGINATION ─── -->
    @if($products->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                <span class="hidden xs:inline">Showing </span>
                <strong>{{ $products->firstItem() ?? 0 }}</strong>
                <span class="hidden xs:inline">to</span>
                <strong>{{ $products->lastItem() ?? 0 }}</strong>
                <span class="hidden sm:inline">of</span>
                <strong>{{ $products->total() }}</strong>
            </div>
            <div class="bx-pagination">
                {{ $products->links() }}
            </div>
        </div>
    @endif

    <!-- ─── CREATE/EDIT MODAL ─── -->
    @if($showModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showModal', false)">
            <div class="bx-modal">
                <form wire:submit.prevent="saveProduct">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEdit ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' }}" />
                            </svg>
                            {{ $isEdit ? 'Edit Product' : 'Create Product' }}
                        </h3>
                        <button type="button" wire:click="$set('showModal', false)" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form">
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Name</label>
                                <input type="text" wire:model.defer="name" class="bx-input" placeholder="Product name" />
                                @error('name')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label required">Code</label>
                                <input type="text" wire:model.defer="code" class="bx-input" placeholder="PRD-001" />
                                @error('code')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label">Size</label>
                                <input type="text" wire:model.defer="size" class="bx-input" placeholder="e.g. 20mm, 32mm" />
                                @error('size')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label">PN</label>
                                <input type="text" wire:model.defer="pn" class="bx-input" placeholder="e.g. PN6, PN10" />
                                @error('pn')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label">Weight Per Meter</label>
                                <input type="text" wire:model.defer="weight_per_meter" class="bx-input" placeholder="e.g. 1.5 kg" />
                                @error('weight_per_meter')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label">Meter Length</label>
                                <input type="number" step="0.01" wire:model.defer="meter_length" class="bx-input" placeholder="6.00" />
                                @error('meter_length')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label">Description</label>
                                    <textarea wire:model.defer="description" class="bx-input" rows="2" placeholder="Product description"></textarea>
                                    @error('description')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bx-form-full">
                                <label class="bx-checkbox">
                                    <input type="checkbox" wire:model.defer="is_active" />
                                    <span>Active Product</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="$set('showModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEdit ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' }}" />
                            </svg>
                            {{ $isEdit ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ─── DELETE MODAL ─── -->
    @if($showDeleteModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showDeleteModal', false)">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3 class="text-red">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Delete Product
                    </h3>
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="bx-modal-close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="bx-modal-body text-center">
                    <div class="bx-delete-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h4 class="bx-delete-title">Are you sure?</h4>
                    <p class="bx-delete-text">This action cannot be undone. This will permanently delete the product and all associated data.</p>
                </div>

                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button type="button" wire:click="deleteProduct" class="bx-btn bx-btn-danger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
