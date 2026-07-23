<!-- resources/views/livewire/notifications/index.blade.php -->
<div class="bx-page bx-page-notifications">
    <!-- ─── HEADER ─── -->
    <div class="bx-header">
        <div class="bx-header-left">
            <h1 class="bx-header-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z"/>
                </svg>
                Notifications
            </h1>
            <p class="bx-header-subtitle">Stay updated with production order activities</p>
        </div>
        <div class="bx-header-right">
            @if($notifications->where('read_at', null)->count() > 0)
                <button wire:click="markAllAsRead" class="bx-btn bx-btn-secondary bx-btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Mark All as Read</span>
                    <span class="sm:hidden">Mark Read</span>
                </button>
            @endif
        </div>
    </div>

    <!-- ─── TOOLBAR ─── -->
    <div class="bx-toolbar">
        <div class="bx-toolbar-left">
            <select wire:model.live="filter" class="bx-select">
                <option value="all">All Notifications</option>
                <option value="unread">Unread Only</option>
                <option value="read">Read Only</option>
            </select>
            <select wire:model.live="type" class="bx-select">
                <option value="all">All Types</option>
                <option value="production_order_created">Order Created</option>
                <option value="production_order_status_changed">Status Changed</option>
                <option value="order_ready">🚨 Order Ready (URGENT)</option>
                <option value="order_delivered">Order Delivered</option>
                <option value="urgent">🚨 Urgent Only</option>
            </select>
        </div>
        <div class="bx-toolbar-right">
            <span class="bx-badge bx-badge-secondary">{{ $notifications->total() }} Total</span>
        </div>
    </div>

    <!-- ─── STATS ─── -->
    <div class="bx-stats">
        <div class="bx-stat">
            <div class="bx-stat-label">Total Notifications</div>
            <div class="bx-stat-value">{{ $notifications->total() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Unread</div>
            <div class="bx-stat-value text-blue">{{ $notifications->where('read_at', null)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Read</div>
            <div class="bx-stat-value text-gray">{{ $notifications->where('read_at', '!=', null)->count() }}</div>
        </div>
        <div class="bx-stat">
            <div class="bx-stat-label">Urgent</div>
            <div class="bx-stat-value text-danger">{{ $notifications->filter(function($n) { return isset($n->data['priority']) && $n->data['priority'] === 'urgent'; })->count() }}</div>
        </div>
    </div>

    <!-- ─── NOTIFICATIONS LIST ─── -->
    <div class="bx-notifications-list">
        @forelse($notifications as $notification)
            <div class="bx-notification-card {{ is_null($notification->read_at) ? 'bx-notification-card-unread' : '' }}">
                <div class="bx-notification-card-content">
                    <!-- Icon -->
                    <div class="bx-notification-card-icon">
                        @if(isset($notification->data['icon']))
                            <i class="{{ $notification->data['icon'] }}"></i>
                        @else
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z"/>
                            </svg>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="bx-notification-card-body">
                        <div class="bx-notification-card-header">
                            <h3 class="bx-notification-card-title">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h3>
                            <div class="bx-notification-card-badges">
                                @if(is_null($notification->read_at))
                                    <span class="bx-badge bx-badge-info bx-badge-xs">New</span>
                                @endif
                                @if(isset($notification->data['priority']) && $notification->data['priority'] === 'urgent')
                                    <span class="bx-badge bx-badge-danger bx-badge-xs bx-badge-pulse">🚨 URGENT</span>
                                @endif
                                @if(isset($notification->data['urgency']) && $notification->data['urgency'] === 'high')
                                    <span class="bx-badge bx-badge-warning bx-badge-xs">⚠️ HIGH</span>
                                @endif
                            </div>
                        </div>

                        <p class="bx-notification-card-message">
                            {{ $notification->data['message'] ?? $notification->data['title'] }}
                        </p>

                        <!-- Order Details -->
                        @if(isset($notification->data['order_number']))
                            <div class="bx-notification-order-details">
                                <h4>Order Details</h4>
                                <div class="bx-notification-order-grid">
                                    <div>
                                        <span class="bx-notification-order-label">Order #:</span>
                                        <span class="bx-notification-order-value">#{{ $notification->data['order_number'] }}</span>
                                    </div>
                                    @if(isset($notification->data['customer_name']))
                                        <div>
                                            <span class="bx-notification-order-label">Customer:</span>
                                            <span class="bx-notification-order-value">{{ $notification->data['customer_name'] }}</span>
                                        </div>
                                    @endif
                                    @if(isset($notification->data['status']))
                                        <div>
                                            <span class="bx-notification-order-label">Status:</span>
                                            <span class="bx-notification-order-value">{{ ucfirst($notification->data['status']) }}</span>
                                        </div>
                                    @endif
                                    @if(isset($notification->data['total_price']))
                                        <div>
                                            <span class="bx-notification-order-label">Total:</span>
                                            <span class="bx-notification-order-value bx-notification-order-total">${{ number_format((float)($notification->data['total_price'] ?? 0), 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="bx-notification-card-footer">
                            <div class="bx-notification-card-time">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $notification->created_at->format('M d, Y \a\t g:i A') }}
                                <span class="bx-notification-time-separator">•</span>
                                {{ $notification->created_at->diffForHumans() }}
                            </div>

                            <div class="bx-notification-card-actions">
                                @if(isset($notification->data['action_url']))
                                    <a href="{{ $notification->data['action_url'] }}" class="bx-btn bx-btn-secondary bx-btn-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        View Details
                                    </a>
                                @endif

                                @if(is_null($notification->read_at))
                                    <button wire:click="markAsRead('{{ $notification->id }}')" class="bx-action bx-action-edit" title="Mark as Read">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                @endif

                                <button wire:click="deleteNotification('{{ $notification->id }}')"
                                        wire:confirm="Are you sure you want to delete this notification?"
                                        class="bx-action bx-action-delete" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bx-empty-state bx-empty-state-large">
                <div class="bx-empty-icon">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z"/>
                    </svg>
                </div>
                <h3>No notifications found</h3>
                <p>
                    @if($filter === 'unread')
                        You have no unread notifications.
                    @elseif($filter === 'read')
                        You have no read notifications.
                    @else
                        You don't have any notifications yet.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <!-- ─── PAGINATION ─── -->
    @if($notifications->hasPages())
        <div class="bx-pagination-wrap">
            <div class="bx-pagination-info">
                Showing <strong>{{ $notifications->firstItem() ?? 0 }}</strong>
                to <strong>{{ $notifications->lastItem() ?? 0 }}</strong>
                of <strong>{{ $notifications->total() }}</strong> notifications
            </div>
            <div class="bx-pagination">
                {{ $notifications->links() }}
            </div>
        </div>
    @endif
</div>
