<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Notifications</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Stay updated with production order activities</p>
    </div>

    <!-- Filters -->
    <div class="mb-6 flex flex-wrap gap-4">
        <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by status</label>
            <select wire:model.live="filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all">All Notifications</option>
                <option value="unread">Unread Only</option>
                <option value="read">Read Only</option>
            </select>
        </div>

        <div class="flex flex-col">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by type</label>
            <select wire:model.live="type" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all">All Types</option>
                <option value="production_order_created">Order Created</option>
                <option value="production_order_status_changed">Status Changed</option>
                <option value="order_ready">🚨 Order Ready (URGENT)</option>
                <option value="order_delivered">Order Delivered</option>
                <option value="urgent">🚨 Urgent Notifications Only</option>
            </select>
        </div>

        @if($notifications->where('read_at', null)->count() > 0)
            <div class="flex items-end">
                <button wire:click="markAllAsRead" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    Mark All as Read
                </button>
            </div>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="space-y-4">
        @forelse($notifications as $notification)
            <div class="bg-white dark:bg-zinc-800 rounded-lg border border-gray-200 dark:border-zinc-700 p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            @if(isset($notification->data['icon']))
                                <div class="w-10 h-10 bg-{{ $notification->data['color'] ?? 'blue' }}-100 rounded-full flex items-center justify-center">
                                    <i class="{{ $notification->data['icon'] }} text-{{ $notification->data['color'] ?? 'blue' }}-600"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-bell text-gray-600"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </h3>
                                @if(is_null($notification->read_at))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        New
                                    </span>
                                @endif
                                @if(isset($notification->data['priority']) && $notification->data['priority'] === 'urgent')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                        🚨 URGENT
                                    </span>
                                @endif
                                @if(isset($notification->data['urgency']) && $notification->data['urgency'] === 'high')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        ⚠️ HIGH PRIORITY
                                    </span>
                                @endif
                            </div>
                            
                            <p class="text-gray-600 dark:text-gray-300 mb-3">
                                {{ $notification->data['message'] ?? $notification->data['title'] }}
                            </p>

                            <!-- Order Details (if available) -->
                            @if(isset($notification->data['order_number']))
                                <div class="bg-gray-50 dark:bg-zinc-700 rounded-lg p-4 mb-3">
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Order Details</h4>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500 dark:text-gray-400">Order #:</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $notification->data['order_number'] }}</span>
                                        </div>
                                        @if(isset($notification->data['customer_name']))
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400">Customer:</span>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $notification->data['customer_name'] }}</span>
                                            </div>
                                        @endif
                                        @if(isset($notification->data['status']))
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400">Status:</span>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ ucfirst($notification->data['status']) }}</span>
                                            </div>
                                        @endif
                                        @if(isset($notification->data['total_price']))
                                            <div>
                                                <span class="text-gray-500 dark:text-gray-400">Total:</span>
                                                <span class="font-medium text-gray-900 dark:text-white">${{ number_format($notification->data['total_price'], 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $notification->created_at->format('M d, Y \a\t g:i A') }}
                                    <span class="mx-2">•</span>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>

                                <div class="flex items-center space-x-2">
                                    @if(isset($notification->data['action_url']))
                                        <a 
                                            href="{{ $notification->data['action_url'] }}" 
                                            class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        >
                                            View Details
                                        </a>
                                    @endif

                                    @if(is_null($notification->read_at))
                                        <button 
                                            wire:click="markAsRead('{{ $notification->id }}')" 
                                            class="px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        >
                                            Mark as Read
                                        </button>
                                    @endif

                                    <button 
                                        wire:click="deleteNotification('{{ $notification->id }}')" 
                                        wire:confirm="Are you sure you want to delete this notification?"
                                        class="px-3 py-1.5 text-sm font-medium text-red-600 hover:text-red-700 bg-white dark:bg-zinc-800 border border-red-300 dark:border-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 dark:bg-zinc-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell-slash text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No notifications found</h3>
                <p class="text-gray-500 dark:text-gray-400">
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

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
