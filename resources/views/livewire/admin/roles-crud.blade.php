<!-- resources/views/livewire/admin/roles-crud.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Roles Management
            </h1>
            <p class="bx-header-subtitle">Create, edit, assign permissions, and delete roles</p>
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
                       placeholder="Search roles..."
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
                <span class="hidden sm:inline">New Role</span>
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
            <div class="bx-stat-label">Total Roles</div>
            <div class="bx-stat-value">{{ $roles->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Total Permissions</div>
            <div class="bx-stat-value text-blue">{{ $permissions->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Modules</div>
            <div class="bx-stat-value text-green">
                @php
                    $modules = $permissions->map(function($p) {
                        return str_contains($p->name, '.') ? explode('.', $p->name)[0] : 'General';
                    })->unique()->count();
                @endphp
                {{ $modules }}
            </div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Roles with Permissions</div>
            <div class="bx-stat-value text-purple-600">{{ $roles->where('permissions', '!=', null)->count() }}</div>
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th class="w-16">ID</th>
                        <th>Role Name</th>
                        <th>Permissions</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr wire:key="role-{{ $role->id }}">
                            <td class="text-gray font-mono text-sm">{{ $role->id }}</td>
                            <td class="font-medium">
                                <span class="bx-code">{{ $role->name }}</span>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1 max-h-16 overflow-y-auto py-1">
                                    @foreach ($role->permissions as $permission)
                                        <span class="bx-code" title="{{ $permission->name }}">
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                    @if($role->permissions->isEmpty())
                                        <span class="text-gray text-sm">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="bx-actions">
                                    <button wire:click="openEditModal({{ $role->id }})"
                                            class="bx-action bx-action-edit"
                                            title="Edit role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $role->id }})"
                                            class="bx-action bx-action-delete"
                                            title="Delete role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="bx-empty">
                                <div class="bx-empty-content">
                                    <div class="bx-empty-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                    <h3>No roles found</h3>
                                    <p>{{ $search ? 'Try adjusting your search terms.' : 'Get started by creating your first role.' }}</p>
                                    @if(!$search)
                                        <button wire:click="openCreateModal" class="bx-btn bx-btn-primary">Create Role</button>
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
    @if($roles->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                <span class="hidden xs:inline">Showing </span>
                <strong>{{ $roles->firstItem() ?? 0 }}</strong>
                <span class="hidden xs:inline">to</span>
                <strong>{{ $roles->lastItem() ?? 0 }}</strong>
                <span class="hidden sm:inline">of</span>
                <strong>{{ $roles->total() }}</strong>
            </div>
            <div class="bx-pagination">
                {{ $roles->links() }}
            </div>
        </div>
    @endif

    <!-- ─── CREATE/EDIT MODAL ─── -->
    @if($showModal)
        <div class="bx-modal-overlay open">
            <div class="bx-modal bx-modal-lg">
                <form wire:submit.prevent="saveRole">
                    <div class="bx-modal-header">
                        <h3>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEdit ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' }}" />
                            </svg>
                            {{ $isEdit ? 'Edit Role' : 'Create Role' }}
                        </h3>
                        <button type="button" wire:click="closeModal" class="bx-modal-close">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="bx-modal-body">
                        <div class="bx-form">
                            <!-- Role Name -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label required">Role Name</label>
                                <input type="text" wire:model.defer="name" class="bx-input @error('name') bx-input-error @enderror"
                                       placeholder="e.g. admin, manager, user" />
                                @error('name')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Permissions -->
                            <div class="bx-form-group bx-form-full">
                                <label class="bx-form-label">Permissions <span class="text-gray text-xs font-normal">(Grouped by Module)</span></label>

                                @php
                                    $groupedPermissions = $permissions->groupBy(function($permission) {
                                        if (str_contains($permission->name, '.')) {
                                            return explode('.', $permission->name)[0];
                                        }
                                        if (str_contains($permission->name, 'customer')) return 'admin';
                                        if (str_contains($permission->name, 'user')) return 'admin';
                                        if (str_contains($permission->name, 'role')) return 'admin';
                                        if (str_contains($permission->name, 'permission')) return 'admin';
                                        if (str_contains($permission->name, 'sales') || str_contains($permission->name, 'order')) return 'sales';
                                        if (str_contains($permission->name, 'production') || str_contains($permission->name, 'operation')) return 'operations';
                                        if (str_contains($permission->name, 'stock') || str_contains($permission->name, 'warehouse')) return 'warehouse';
                                        if (str_contains($permission->name, 'finance') || str_contains($permission->name, 'payment')) return 'finance';
                                        return 'General';
                                    });

                                    function formatPermissionName($name) {
                                        if (str_contains($name, '.')) {
                                            $parts = explode('.', $name);
                                            $label = end($parts);

                                            $mappings = [
                                                'view' => 'View Dashboard',
                                                'stock-overview' => 'View Finished Goods Stock',
                                                'pending-receipts' => 'Approve Goods Receipts (GRN)',
                                                'material-issue-requests' => 'Manage Material Issues (SIV)',
                                                'demand-aggregation' => 'Consolidate PR Demands',
                                                'demand-control' => 'Authorize Stock Requests',
                                                'production-machine' => 'Configure Production Lines',
                                                'manager' => 'Manage Production Queue',
                                                'procurement' => 'Procurement Lifecycle (RFQ/PO)',
                                                'purchase-payments' => 'Suppliers Payments (AP)',
                                                'revenue-report' => 'Financial Revenue Analysis',
                                                'inventory-report' => 'Stock Valuation Report',
                                                'production-report' => 'Daily Production Log',
                                                'weekly-production-report' => 'Weekly Performance Report',
                                                'monthly-production-report' => 'Monthly Analytical Report',
                                                'raw-material-stock-balance-report' => 'Material Balance Sheet',
                                                'quality-report-manager' => 'Quality Standards Manager',
                                                'material-stock-out-line-crud' => 'Monitor Material Consumption Logs',
                                                'finished-goods' => 'Record Produced Finished Goods',
                                                'finished-good-material-stock-out-line-crud' => 'Link Finished Goods to Raw Materials',
                                                'scrap-waste-crud' => 'Manage Scrap and Waste Records',
                                                'management-dashboard' => 'Executive Performance Cockpit',
                                                'customers-crud' => 'Manage Customer Directory',
                                                'suppliers-crud' => 'Manage Supplier Directory',
                                                'users-crud' => 'Manage System Users',
                                                'roles-crud' => 'Manage Access Roles',
                                                'raw-materials-crud' => 'Manage Raw Material Catalog',
                                                'products-crud' => 'Manage Finished Products Catalog',
                                            ];

                                            if (isset($mappings[$label])) {
                                                return $mappings[$label];
                                            }
                                            return ucwords(str_replace('-', ' ', $label));
                                        }
                                        return ucwords(str_replace(['-', '.', '_'], ' ', $name));
                                    }
                                @endphp

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2 max-h-[50vh] overflow-y-auto pr-2">
                                    @foreach ($groupedPermissions as $module => $modulePermissions)
                                        <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-100 dark:border-gray-700/50">
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">{{ ucfirst($module) }}</h4>
                                            <div class="space-y-2">
                                                @foreach ($modulePermissions as $permission)
                                                    <label class="bx-checkbox-wrapper">
                                                        <input type="checkbox"
                                                               wire:model.defer="selectedPermissions"
                                                               value="{{ $permission->name }}" />
                                                        <div>
                                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ formatPermissionName($permission->name) }}</span>
                                                            <span class="block text-[10px] text-gray-400 dark:text-gray-500 font-mono leading-none mt-0.5">{{ $permission->name }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selectedPermissions')
                                    <span class="bx-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bx-modal-footer">
                        <button type="button" wire:click="closeModal" class="bx-btn bx-btn-secondary">Cancel</button>
                        <button type="submit" class="bx-btn bx-btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isEdit ? 'M5 13l4 4L19 7' : 'M12 4v16m8-8H4' }}" />
                            </svg>
                            {{ $isEdit ? 'Update Role' : 'Create Role' }}
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
                        Delete Role
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
                    <p class="bx-delete-text">This action cannot be undone. This will permanently delete the role and all associated permissions.</p>
                </div>

                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="closeDeleteModal" class="bx-btn bx-btn-secondary">Cancel</button>
                    <button type="button" wire:click="deleteRole" class="bx-btn bx-btn-danger">
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
