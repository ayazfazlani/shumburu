<!-- resources/views/livewire/components/notification-center.blade.php -->
<div class="bx-notification-wrapper" x-data="{ open: false }">
    <!-- ─── NOTIFICATION BELL ─── -->
    <button
        @click="open = !open; $wire.toggleNotificationCenter()"
        class="bx-notification-bell {{ $unreadCount > 0 ? 'bx-notification-bell-unread' : '' }}"
        title="Notifications"
    >
        <div class="bx-notification-bell-content">
            <div class="bx-notification-bell-icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z"/>
                </svg>

                @if($unreadCount > 0)
                    <span class="bx-notification-badge">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </div>
            <span class="bx-notification-label">Notifications</span>
        </div>
    </button>

    <!-- ─── NOTIFICATION DROPDOWN ─── -->
    <div
        class="bx-notification-dropdown"
        x-show="open"
        @click.away="open = false; $wire.toggleNotificationCenter()"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
    >
        <!-- Header -->
        <div class="bx-notification-header">
            <div class="bx-notification-header-left">
                <div class="bx-notification-header-dot"></div>
                <h3>Notifications</h3>
                @if($unreadCount > 0)
                    <span class="bx-notification-count">{{ $unreadCount }} new</span>
                @endif
            </div>
            <div class="bx-notification-header-right">
                @if($unreadCount > 0)
                    <button wire:click="markAllAsRead" class="bx-notification-mark-read">Mark all read</button>
                @endif
                <button @click="open = false; $wire.toggleNotificationCenter()" class="bx-notification-close" title="Close">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="bx-notification-list">
            @forelse($notifications as $notification)
                <div
                    wire:click="viewNotification('{{ $notification->id }}')"
                    class="bx-notification-item {{ is_null($notification->read_at) ? 'bx-notification-unread' : '' }}"
                >
                    <div class="bx-notification-item-content">
                        <div class="bx-notification-item-icon">
                            @if(isset($notification->data['icon']))
                                <i class="{{ $notification->data['icon'] }}"></i>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z"/>
                                </svg>
                            @endif
                        </div>
                        <div class="bx-notification-item-body">
                            <div class="bx-notification-item-header">
                                <p class="bx-notification-item-title">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </p>
                                @if(is_null($notification->read_at))
                                    <div class="bx-notification-unread-dot"></div>
                                @endif
                            </div>
                            <p class="bx-notification-item-message">
                                {{ Str::limit($notification->data['message'] ?? $notification->data['title'], 70) }}
                            </p>
                            <p class="bx-notification-item-time">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bx-notification-empty">
                    <div class="bx-notification-empty-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z"></path>
                        </svg>
                    </div>
                    <h3>All caught up!</h3>
                    <p>No new notifications</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if(count($notifications) > 0)
            <div class="bx-notification-footer">
                <a href="{{ route('notifications.index') }}" class="bx-notification-view-all">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    View all notifications
                </a>
            </div>
        @endif
    </div>

    <!-- ─── NOTIFICATION DETAIL MODAL ─── -->
    @if($selectedNotification)
        <div class="bx-modal-overlay open" wire:click.self="closeModal">
            <div class="bx-modal bx-modal-sm">
                <div class="bx-modal-header">
                    <h3>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z"/>
                        </svg>
                        {{ $selectedNotification->data['title'] ?? 'Notification' }}
                    </h3>
                    <button type="button" wire:click="closeModal" class="bx-modal-close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="bx-modal-body">
                    <p class="bx-notification-detail-message">
                        {{ $selectedNotification->data['message'] ?? $selectedNotification->data['title'] }}
                    </p>
                    @if(isset($selectedNotification->data['action_url']))
                        <a href="{{ $selectedNotification->data['action_url'] }}" class="bx-btn bx-btn-primary bx-btn-sm mt-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            View Details
                        </a>
                    @endif
                </div>
                <div class="bx-modal-footer bx-modal-footer-notification">
                    <p class="bx-notification-detail-time">
                        {{ $selectedNotification->created_at->format('M d, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
