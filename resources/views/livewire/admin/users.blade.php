<!-- resources/views/livewire/admin/users-crud.blade.php -->
<div class="bx-page">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <h1 class="bx-header-title">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            {{ __('users.title') }}
        </h1>
        <p class="bx-header-subtitle">{{ __('users.title_description') }}</p>
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
                       placeholder="{{ __('global.search_here') }}"
                       class="bx-search-input" />
            </div>
            <select wire:model.live="role" class="bx-select">
                <option value="">{{ __('users.all_roles') }}</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <select wire:model.live="perPage" class="bx-select">
                <option value="10">{{ __('global.10_per_page') }}</option>
                <option value="25">{{ __('global.25_per_page') }}</option>
                <option value="50">{{ __('global.50_per_page') }}</option>
                <option value="100">{{ __('global.100_per_page') }}</option>
            </select>
        </div>
        <div class="bx-toolbar-right">
            @can('create users')
                <a href="{{ route('admin.users.create') }}" class="bx-btn bx-btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="hidden sm:inline">{{ __('users.create_user') }}</span>
                    <span class="sm:hidden">{{ __('global.add') }}</span>
                </a>
            @endcan
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Users</div>
            <div class="bx-stat-value">{{ $users->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Active Users</div>
            <div class="bx-stat-value text-green">{{ $users->where('is_active', true)->count() ?? $users->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Roles</div>
            <div class="bx-stat-value text-blue">{{ $roles->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">New This Month</div>
            <div class="bx-stat-value text-blue">{{ $users->where('created_at', '>=', now()->startOfMonth())->count() }}</div>
        </div>
    </div>

    <!-- ─── TABLE ─── -->
    <div class="bx-table-wrap">
        <div class="bx-table-scroll">
            <table class="bx-table">
                <thead>
                    <tr>
                        <th class="w-16">{{ __('global.id') }}</th>
                        <th>{{ __('users.name') }}</th>
                        <th class="hidden sm:table-cell">{{ __('users.email') }}</th>
                        <th>{{ __('users.roles') }}</th>
                        <th class="text-right">{{ __('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td class="text-gray font-mono text-sm">{{ $user->id }}</td>
                            <td class="font-medium">{{ $user->name }}</td>
                            <td class="hidden sm:table-cell">
                                <a href="mailto:{{ $user->email }}" class="text-blue hover:underline">
                                    {{ $user->email }}
                                </a>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($user->roles as $role)
                                        <span class="bx-code">{{ $role->name }}</span>
                                    @endforeach
                                    @if($user->roles->isEmpty())
                                        <span class="text-gray text-sm">—</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="bx-actions">
                                    <!-- View -->
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="bx-action"
                                       title="{{ __('global.view') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <!-- Impersonate -->
                                    @can('impersonate')
                                        @if (auth()->user()->id !== $user->id)
                                            <form action="{{ route('impersonate.store', $user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bx-action" title="{{ __('users.impersonate') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                    <!-- Edit -->
                                    @can('update users')
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="bx-action bx-action-edit"
                                           title="{{ __('global.edit') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    @endcan

                                    <!-- Delete -->
                                    @can('delete users')
                                        <button wire:click="confirmDelete('{{ $user->id }}')"
                                                class="bx-action bx-action-delete"
                                                title="{{ __('global.delete') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="bx-empty">
                                <div class="bx-empty-content">
                                    <div class="bx-empty-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <h3>{{ __('users.no_users_found') ?? 'No users found' }}</h3>
                                    <p>{{ $search ? 'Try adjusting your search terms.' : 'Get started by creating your first user.' }}</p>
                                    @if(!$search)
                                        @can('create users')
                                            <a href="{{ route('admin.users.create') }}" class="bx-btn bx-btn-primary">
                                                {{ __('users.create_user') }}
                                            </a>
                                        @endcan
                                    @else
                                        <button wire:click="$set('search', '')" class="bx-btn bx-btn-secondary">
                                            {{ __('global.clear_search') ?? 'Clear Search' }}
                                        </button>
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
    @if($users->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                <span class="hidden xs:inline">{{ __('global.showing') ?? 'Showing' }} </span>
                <strong>{{ $users->firstItem() ?? 0 }}</strong>
                <span class="hidden xs:inline">{{ __('global.to') ?? 'to' }}</span>
                <strong>{{ $users->lastItem() ?? 0 }}</strong>
                <span class="hidden sm:inline">{{ __('global.of') ?? 'of' }}</span>
                <strong>{{ $users->total() }}</strong>
            </div>
            <div class="bx-pagination">
                {{ $users->links() }}
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
                        {{ __('users.delete_user') }}
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
                    <h4 class="bx-delete-title">{{ __('users.you_are_about_to_delete') }}</h4>
                    <p class="bx-delete-text">{{ __('global.this_action_is_irreversible') }}</p>
                </div>

                <div class="bx-modal-footer justify-center">
                    <button type="button" wire:click="$set('showDeleteModal', false)" class="bx-btn bx-btn-secondary">
                        {{ __('global.cancel') }}
                    </button>
                    <button type="button" wire:click="deleteUser" class="bx-btn bx-btn-danger">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ __('users.delete_user') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
