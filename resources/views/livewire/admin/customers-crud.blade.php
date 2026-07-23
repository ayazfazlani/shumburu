<!-- resources/views/livewire/admin/customers-crud.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Customers
            </h1>
            <p class="bx-header-subtitle">Manage your customer database</p>
        </div>
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
                       placeholder="Search..."
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
                <span class="hidden sm:inline">New Customer</span>
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
            <div class="bx-stat-label">Total</div>
            <div class="bx-stat-value">{{ $customers->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Active</div>
            <div class="bx-stat-value text-green">{{ $customers->where('is_active', true)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Inactive</div>
            <div class="bx-stat-value text-gray">{{ $customers->where('is_active', false)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">New</div>
            <div class="bx-stat-value text-blue">{{ $customers->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        @can('can see customer name')
                            <th>Name</th>
                        @endcan
                        <th class="hidden sm:table-cell">Contact</th>
                        <th class="hidden md:table-cell">Phone</th>
                        <th class="hidden lg:table-cell">Email</th>
                        <th class="hidden xl:table-cell">Address</th>
                        <th>Status</th>
                        <th class="hidden md:table-cell">Created</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td class="text-gray font-mono text-sm">{{ $customer->id }}</td>
                            <td><span class="bx-code">{{ $customer->code }}</span></td>
                            @can('can see customer name')
                                <td class="font-medium">{{ $customer->name }}</td>
                            @endcan
                            <td class="hidden sm:table-cell">{{ $customer->contact_person ?? '—' }}</td>
                            <td class="hidden md:table-cell">
                                @if($customer->phone)
                                    <a href="tel:{{ $customer->phone }}" class="text-blue hover:underline">{{ $customer->phone }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="hidden lg:table-cell">
                                @if($customer->email)
                                    <a href="mailto:{{ $customer->email }}" class="text-blue hover:underline">{{ Str::limit($customer->email, 20) }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="hidden xl:table-cell">{{ Str::limit($customer->address ?? '—', 20) }}</td>
                            <td>
                                @if ($customer->is_active)
                                    <span class="bx-badge bx-badge-success">Active</span>
                                @else
                                    <span class="bx-badge bx-badge-gray">Inactive</span>
                                @endif
                            </td>
                            <td class="hidden md:table-cell text-gray text-sm">{{ $customer->created_at ? $customer->created_at->format('M d, Y') : '—' }}</td>
                            <td>
                                <div class="bx-actions">
                                    <button wire:click="openEditModal({{ $customer->id }})"
                                            class="bx-action bx-action-edit"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $customer->id }})"
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
                            <td colspan="{{ Auth::user()->can('can see customer name') ? 11 : 10 }}" class="bx-empty">
                                <div class="bx-empty-content">
                                    <div class="bx-empty-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                                        </svg>
                                    </div>
                                    <h3>No customers found</h3>
                                    <p>{{ $search ? 'Try adjusting your search terms.' : 'Get started by creating your first customer.' }}</p>
                                    @if(!$search)
                                        <button wire:click="openCreateModal" class="bx-btn bx-btn-primary">Create Customer</button>
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
    @if($customers->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                <span class="hidden xs:inline">Showing </span>
                <strong>{{ $customers->firstItem() ?? 0 }}</strong>
                <span class="hidden xs:inline">to</span>
                <strong>{{ $customers->lastItem() ?? 0 }}</strong>
                <span class="hidden sm:inline">of</span>
                <strong>{{ $customers->total() }}</strong>
            </div>
            <div class="bx-pagination">
                {{ $customers->links() }}
            </div>
        </div>
    @endif

    <!-- ─── CREATE/EDIT MODAL ─── -->
    @if($showModal)
        <div class="bx-modal-overlay open">
            <div class="bx-modal">
                <form wire:submit.prevent="saveCustomer">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEdit ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' }}" />
                            </svg>
                            {{ $isEdit ? 'Edit Customer' : 'Create Customer' }}
                        </h3>
                        <button type="button" wire:click="closeModal" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form">
                            <!-- Code -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Code</label>
                                <input type="text" wire:model.defer="code" class="bx-input @error('code') bx-input-error @enderror"
                                       placeholder="CUST-001" />
                                @error('code')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Name -->
                            <div class="bx-form-group">
                                <label class="bx-form-label required">Name</label>
                                <input type="text" wire:model.defer="name" class="bx-input @error('name') bx-input-error @enderror"
                                       placeholder="John Doe" />
                                @error('name')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Contact Person -->
                            <div class="bx-form-group">
                                <label class="bx-form-label">Contact Person</label>
                                <input type="text" wire:model.defer="contact_person" class="bx-input @error('contact_person') bx-input-error @enderror"
                                       placeholder="Jane Smith" />
                                @error('contact_person')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="bx-form-group">
                                <label class="bx-form-label">Phone</label>
                                <input type="text" wire:model.defer="phone" class="bx-input @error('phone') bx-input-error @enderror"
                                       placeholder="+1 (555) 000-0000" />
                                @error('phone')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Email</label>
                                <input type="email" wire:model.defer="email" class="bx-input @error('email') bx-input-error @enderror"
                                       placeholder="john@example.com" />
                                @error('email')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Address</label>
                                <input type="text" wire:model.defer="address" class="bx-input @error('address') bx-input-error @enderror"
                                       placeholder="123 Main St, City" />
                                @error('address')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Active Checkbox -->
                            <div class="bx-form-group bx-form-full">
                                <div class="bx-checkbox-wrapper">
                                    <input type="checkbox" wire:model.defer="is_active" id="is_active" />
                                    <label for="is_active">Active Customer</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="closeModal" class="bx-btn bx-btn-secondary">Cancel</button>
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
        <div class="bx-modal-overlay open">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3 class="text-red">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Delete Customer
                    </h3>
                    <button type="button" wire:click="closeDeleteModal" class="bx-modal-close">
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
                    <p class="bx-delete-text">This action cannot be undone. This will permanently delete the customer and all associated data.</p>
                </div>

                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="closeDeleteModal" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button type="button" wire:click="deleteCustomer" class="bx-btn bx-btn-danger">
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
