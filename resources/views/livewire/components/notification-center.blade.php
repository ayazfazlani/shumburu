<div class="relative w-full">
    <!-- Notification Bell Button - Sidebar Style -->
    <button 
        wire:click="toggleNotificationCenter" 
        class="relative w-full flex items-center justify-center p-3 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg transition-all duration-200 group hover:shadow-md hover:scale-[1.02]"
        title="Notifications"
    >
        <div class="flex items-center space-x-3 w-full">
            <div class="relative">
                <!-- Bell Icon -->
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z"/>
                </svg>
                
                <!-- Unread Count Badge -->
                @if($unreadCount > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse font-medium">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </div>
            <span class="text-sm font-medium">Notifications</span>
        </div>
    </button>

    <!-- Notification Dropdown -->
    @if($showNotificationCenter)
        @teleport('body')
            <!-- Dropdown -->
            <div class="notification-dropdown fixed left-4 top-20 w-80 bg-white dark:bg-zinc-800 rounded-xl shadow-2xl border border-gray-200 dark:border-zinc-700 z-[9999] max-h-80 overflow-hidden transform transition-all duration-300 ease-out animate-in slide-in-from-top-2 fade-in">
            <!-- Header -->
            <div class="px-4 py-3 border-b border-gray-200 dark:border-zinc-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-zinc-800 dark:to-zinc-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                        @if($unreadCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $unreadCount }} new
                            </span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($unreadCount > 0)
                            <button 
                                wire:click="markAllAsRead" 
                                class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium hover:underline transition-colors"
                            >
                                Mark all read
                            </button>
                        @endif
                        <button 
                            wire:click="toggleNotificationCenter" 
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                            title="Close"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="max-h-64 overflow-y-auto">
                @forelse($notifications as $notification)
                    <div 
                        wire:click="viewNotification('{{ $notification->id }}')"
                        class="px-4 py-3 border-b border-gray-100 dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer transition-all duration-200 group {{ is_null($notification->read_at) ? 'bg-blue-50/50 dark:bg-blue-900/10 border-l-4 border-l-blue-500' : 'hover:border-l-4 hover:border-l-gray-200 dark:hover:border-l-zinc-600' }}"
                    >
                        <div class="flex items-start space-x-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0 mt-1">
                                @if(isset($notification->data['icon']))
                                    <div class="w-8 h-8 bg-{{ $notification->data['color'] ?? 'blue' }}-100 dark:bg-{{ $notification->data['color'] ?? 'blue' }}-900/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                        <i class="{{ $notification->data['icon'] }} text-{{ $notification->data['color'] ?? 'blue' }}-600 dark:text-{{ $notification->data['color'] ?? 'blue' }}-400 text-sm"></i>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                        <i class="fas fa-bell text-blue-600 dark:text-blue-400 text-sm"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </p>
                                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1 line-clamp-2">
                                            {{ Str::limit($notification->data['message'] ?? $notification->data['title'], 70) }}
                                        </p>
                                    </div>
                                    @if(is_null($notification->read_at))
                                        <div class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 animate-pulse"></div>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-zinc-700 dark:to-zinc-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">All caught up!</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">No new notifications</p>
                    </div>
                @endforelse
            </div>

            <!-- Footer -->
            @if(count($notifications) > 0)
                <div class="px-4 py-3 border-t border-gray-200 dark:border-zinc-700 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-zinc-800 dark:to-zinc-700">
                    <a 
                        href="{{ route('notifications.index') }}" 
                        class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium hover:underline transition-colors"
                    >
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        View all notifications
                    </a>
                </div>
            @endif
        </div>
        @endteleport
    @endif

    <!-- Notification Detail Modal -->
    @if($selectedNotification)
        @teleport('body')
            <div class="fixed inset-0 flex items-center justify-center z-[10000]" wire:click="$set('selectedNotification', null)">
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all duration-300 ease-out animate-in zoom-in-95 fade-in" wire:click.stop>
                <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $selectedNotification->data['title'] ?? 'Notification' }}
                        </h3>
                        <button wire:click="$set('selectedNotification', null)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="px-6 py-4">
                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                        {{ $selectedNotification->data['message'] ?? $selectedNotification->data['title'] }}
                    </p>
                    
                    @if(isset($selectedNotification->data['action_url']))
                        <a 
                            href="{{ $selectedNotification->data['action_url'] }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200"
                        >
                            <i class="fas fa-external-link-alt mr-2"></i>
                            View Details
                        </a>
                    @endif
                </div>
                
                <div class="px-6 py-3 bg-gray-50 dark:bg-zinc-700 border-t border-gray-200 dark:border-zinc-600 rounded-b-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $selectedNotification->created_at->format('M d, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>
        </div>
        @endteleport
    @endif
</div>

<script>
    // Listen for real-time notifications
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.private('notifications')
                .listen('ProductionOrderCreated', (e) => {
                    @this.call('refreshNotifications');
                    // Show toast notification
                    showToast(e.message, 'info');
                })
                .listen('ProductionOrderStatusChanged', (e) => {
                    @this.call('refreshNotifications');
                    // Show toast notification
                    showToast(e.message, 'success');
                });
        }

        // Handle click outside to close notification dropdown
        document.addEventListener('click', function(event) {
            const notificationButton = event.target.closest('[wire\\:click="toggleNotificationCenter"]');
            const notificationDropdown = event.target.closest('.notification-dropdown');
            
            if (!notificationButton && !notificationDropdown && @this.showNotificationCenter) {
                @this.call('toggleNotificationCenter');
            }
        });
    });

    function showToast(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'green' : 'blue';
        toast.className = `fixed top-4 right-4 bg-${bgColor}-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-transform duration-300 translate-x-full`;
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 5 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 5000);
    }
</script>
