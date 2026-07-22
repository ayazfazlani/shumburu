<!-- resources/views/livewire/admin/suppliers-crud.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Suppliers
        </h1>
        <p class="bx-header-subtitle">Manage raw material suppliers and vendor details</p>
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
                       placeholder="Search suppliers..."
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
                <span class="hidden sm:inline">New Supplier</span>
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
            <div class="bx-stat-label">Total Suppliers</div>
            <div class="bx-stat-value">{{ $suppliers->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Active</div>
            <div class="bx-stat-value text-green">{{ $suppliers->where('is_active', true)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Inactive</div>
            <div class="bx-stat-value text-gray">{{ $suppliers->where('is_active', false)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">New This Month</div>
            <div class="bx-stat-value text-blue">{{ $suppliers->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th class="hidden sm:table-cell">Contact</th>
                        <th class="hidden md:table-cell">Phone</th>
                        <th class="hidden lg:table-cell">Email</th>
                        <th class="hidden xl:table-cell">Payment Terms</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td><span class="bx-code">{{ $supplier->code }}</span></td>
                            <td class="font-medium">{{ $supplier->name }}</td>
                            <td class="hidden sm:table-cell">{{ $supplier->contact_person ?? '—' }}</td>
                            <td class="hidden md:table-cell">
                                @if($supplier->phone)
                                    <a href="tel:{{ $supplier->phone }}" class="text-blue hover:underline">{{ $supplier->phone }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="hidden lg:table-cell">
                                @if($supplier->email)
                                    <a href="mailto:{{ $supplier->email }}" class="text-blue hover:underline">{{ Str::limit($supplier->email, 20) }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="hidden xl:table-cell">
                                @if($supplier->payment_terms)
                                    <span class="bx-code">{{ $supplier->payment_terms }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($supplier->is_active)
                                    <span class="bx-badge bx-badge-success">Active</span>
                                @else
                                    <span class="bx-badge bx-badge-gray">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="bx-actions">
                                    <button wire:click="openEditModal({{ $supplier->id }})"
                                            class="bx-action bx-action-edit"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $supplier->id }})"
                                            class="bx-action bx-action-delete"
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
                            <td colspan="8" class="bx-empty">
                                <div class="bx-empty-content">
                                    <div class="bx-empty-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <h3>No suppliers found</h3>
                                    <p>{{ $search ? 'Try adjusting your search terms.' : 'Get started by creating your first supplier.' }}</p>
                                    @if(!$search)
                                        <button wire:click="openCreateModal" class="bx-btn bx-btn-primary">Create Supplier</button>
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
    @if($suppliers->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                <span class="hidden xs:inline">Showing </span>
                <strong>{{ $suppliers->firstItem() ?? 0 }}</strong>
                <span class="hidden xs:inline">to</span>
                <strong>{{ $suppliers->lastItem() ?? 0 }}</strong>
                <span class="hidden sm:inline">of</span>
                <strong>{{ $suppliers->total() }}</strong>
            </div>
            <div class="bx-pagination">
                {{ $suppliers->links() }}
            </div>
        </div>
    @endif

    <!-- ─── CREATE/EDIT MODAL ─── -->
    @if($showModal)
        <div class="bx-modal-overlay" wire:click.self="$set('showModal', false)">
            <div class="bx-modal">
                <form wire:submit.prevent="saveSupplier">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEdit ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' }}" />
                            </svg>
                            {{ $isEdit ? 'Edit Supplier' : 'Create Supplier' }}
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
                                <label class="bx-form-label required">Code</label>
                                <input type="text" wire:model.defer="code" class="bx-input" placeholder="SUP-001" />
                                @error('code')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label required">Supplier Name</label>
                                <input type="text" wire:model.defer="name" class="bx-input" placeholder="Company name" />
                                @error('name')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label">Contact Person</label>
                                <input type="text" wire:model.defer="contact_person" class="bx-input" placeholder="Contact name" />
                                @error('contact_person')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label">Phone</label>
                                <input type="text" wire:model.defer="phone" class="bx-input" placeholder="+92 300 1234567" />
                                @error('phone')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label">Email</label>
                                <input type="email" wire:model.defer="email" class="bx-input" placeholder="supplier@company.com" />
                                @error('email')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-group">
                                <label class="bx-form-label">Payment Terms</label>
                                <input type="text" wire:model.defer="payment_terms" class="bx-input" placeholder="Net 30, Cash on Delivery" />
                                @error('payment_terms')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="bx-form-full">
                                <div class="bx-form-group">
                                    <label class="bx-form-label">Address</label>
                                    <input type="text" wire:model.defer="address" class="bx-input" placeholder="Supplier address" />
                                    @error('address')
                                        <span class="bx-error">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="bx-form-full">
                                <label class="bx-checkbox">
                                    <input type="checkbox" wire:model.defer="is_active" />
                                    <span>Active Supplier</span>
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
                        Delete Supplier
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
                    <p class="bx-delete-text">This action cannot be undone. This will permanently delete the supplier and all associated data.</p>
                </div>

                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button type="button" wire:click="deleteSupplier" class="bx-btn bx-btn-danger">
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
