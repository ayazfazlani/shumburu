<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        /* Active link styling */
        .flux-navlist-item[aria-current="page"],
        .flux-navlist-item.active {
            background-color: rgb(59 130 246);
            color: white;
        }
        
        .flux-navlist-item[aria-current="page"] .flux-icon,
        .flux-navlist-item.active .flux-icon {
            color: white;
        }
        
        .dark .flux-navlist-item[aria-current="page"],
        .dark .flux-navlist-item.active {
            background-color: rgb(37 99 235);
        }
    </style>
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    
    <flux:sidebar sticky stashable
        class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 lg:dark:bg-zinc-900/50">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('home') }}" class="mr-5 flex items-center space-x-2" wire:navigate>
            <x-app-logo class="size-8"></x-app-logo>
        </a>

        <div class="flex flex-col gap-3">
            <flux:button href="{{ route('home') }}" icon="arrow-left" size="sm" wire:navigate>
                {{ __('global.go_to_frontend') }}
            </flux:button>
            
            <!-- Notification Center -->
            <div class="w-full mb-4">
                @livewire('components.notification-center')
            </div>
        </div>

        <flux:navlist variant="outline">
            <!-- Platform Section -->
            <flux:navlist.group heading="Platform" class="grid">
                <flux:navlist.item icon="squares-2x2" href="{{ route('admin.index') }}" wire:navigate
                    class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">Dashboard</flux:navlist.item>
            </flux:navlist.group>

            @can(['can view users section'])
            <!-- Users Section -->
            <flux:navlist.group heading="Users" class="grid">
                <flux:navlist.item icon="users" href="{{ route('admin.users-crud') }}" wire:navigate
                    class="{{ request()->routeIs('admin.users-crud') ? 'active' : '' }}">
                    {{ __('users.title') }}
                </flux:navlist.item>
                <flux:navlist.item icon="shield-exclamation" href="{{ route('admin.roles-crud') }}" wire:navigate
                    class="{{ request()->routeIs('admin.roles-crud') ? 'active' : '' }}">
                    {{ __('roles.title') }}
                </flux:navlist.item>
                <flux:navlist.item icon="key" href="{{ route('admin.permissions-crud') }}" wire:navigate
                    class="{{ request()->routeIs('admin.permissions-crud') ? 'active' : '' }}">
                    {{ __('permissions.title') }}
                </flux:navlist.item>
                <flux:navlist.item icon="cube" href="{{ route('admin.admin.raw-materials.index') }}" wire:navigate
                    class="{{ request()->routeIs('admin.admin.raw-materials.index') ? 'active' : '' }}">
                    {{ __('Raw materials') }}
                </flux:navlist.item>
                @can('can manage customers')
                <flux:navlist.item icon="users" href="{{ route('admin.customers-crud') }}" wire:navigate
                    class="{{ request()->routeIs('admin.customers-crud') ? 'active' : '' }}">
                    {{ __('customers') }}
                </flux:navlist.item>
                @endcan
            </flux:navlist.group>              
            @endcan
            @can(['can view warehouse section'])
            <!-- Warehouse Section -->
            <flux:navlist.group heading="Warehouse" class="grid">
                <flux:navlist.item icon="building-storefront" href="{{ route('warehouse.index') }}" wire:navigate
                    class="{{ request()->routeIs('warehouse.index') ? 'active' : '' }}">
                    Warehouse Dashboard
                </flux:navlist.item>
                <flux:navlist.item icon="cube" href="{{ route('warehouse.products') }}" wire:navigate
                    class="{{ request()->routeIs('warehouse.products') ? 'active' : '' }}">
                    Products
                </flux:navlist.item>
                @can('can see material stock out lines')
                <flux:navlist.item icon="view-columns" href="{{ route('warehouse.lines') }}" wire:navigate
                    class="{{ request()->routeIs('warehouse.lines') ? 'active' : '' }}">
                    Lines
                </flux:navlist.item>                                   
                @endcan

                @can('can perform material stock in')
                <flux:navlist.item icon="arrow-down-tray" href="{{ route('warehouse.stock-in') }}" wire:navigate
                    class="{{ request()->routeIs('warehouse.stock-in') ? 'active' : '' }}">
                    Stock In
                </flux:navlist.item>                                   
                @endcan
                @can('can perform material stock out')
                <flux:navlist.item icon="arrow-up-tray" href="{{ route('warehouse.stock-out') }}" wire:navigate
                    class="{{ request()->routeIs('warehouse.stock-out') ? 'active' : '' }}">
                    Stock Out
                </flux:navlist.item>                                    
                @endcan
                @can('can record finished goods')
                <flux:navlist.item icon="check-badge" href="{{ route('warehouse.finished-goods') }}" wire:navigate
                    class="{{ request()->routeIs('warehouse.finished-goods') ? 'active' : '' }}">
                    Finished Goods
                </flux:navlist.item>                                   
                @endcan
                @can('can see FG material stock out links')
                      <flux:navlist.item icon="link" href="{{ route('warehouse.finished-good-material') }}" wire:navigate
                    class="{{ request()->routeIs('warehouse.finished-good-material') ? 'active' : '' }}">
                    FG Material Stock Out Links
                </flux:navlist.item>
                @endcan
              @can('can see scrap waste')
                <flux:navlist.item icon="trash" href="{{ route('warehouse.scrap-wastes') }}" wire:navigate
                    class="{{ request()->routeIs('warehouse.scrap-wastes') ? 'active' : '' }}">
                    Scrap/Waste
                </flux:navlist.item>                  
              @endcan
              @can('can see material stock out lines')
                <flux:navlist.item icon="queue-list" href="{{ route('warehouse.material-stock-out-lines') }}" wire:navigate
                    class="{{ request()->routeIs('warehouse.material-stock-out-lines') ? 'active' : '' }}">
                    Material Stock Out Lines
                </flux:navlist.item>                
              @endcan
            </flux:navlist.group>
            @endcan
            @can('can manage fya-warehouse')
             <flux:navlist.group heading="FYA Warehouse" class="grid">
                <flux:navlist.item icon="squares-2x2" href="{{ route('fya werehouse') }}" wire:navigate
                    class="{{ request()->routeIs('fya werehouse') ? 'active' : '' }}">in/out products</flux:navlist.item>
                  
            </flux:navlist.group>
            @endcan
            @can(['can view reports section'])  
            <!-- Reports Section -->
            <flux:navlist.group heading="Reports" class="grid">
                @can('can see daily sales reports')
                <flux:navlist.item icon="calendar" href="{{ route('sales.daily-sales-report') }}" wire:navigate
                    class="{{ request()->routeIs('sales.daily-sales-report') ? 'active' : '' }}">
                    Daily Report
                </flux:navlist.item>
                @endcan
                @can('can see weekly sales reports')
                <flux:navlist.item icon="calendar-days" href="{{ route('sales.weekly-sales-report') }}" wire:navigate
                    class="{{ request()->routeIs('sales.weekly-sales-report') ? 'active' : '' }}">
                    Weekly Report
                </flux:navlist.item>
                @endcan
                @can('can see monthly sales reports')
                <flux:navlist.item icon="calendar-days" href="{{ route('sales.monthly-sales-report') }}" wire:navigate
                    class="{{ request()->routeIs('sales.monthly-sales-report') ? 'active' : '' }}">
                    Monthly Report
                </flux:navlist.item>
                @endcan
                @can('can see raw meterial balance')
                <flux:navlist.item icon="scale" href="{{ route('reports.raw-material-stock-balance') }}" wire:navigate
                    class="{{ request()->routeIs('reports.raw-material-stock-balance') ? 'active' : '' }}">
                    Raw Material Balance
                </flux:navlist.item>
                @endcan
            </flux:navlist.group>
            @endcan

            @can(['can view operations section'])
            <!-- Operations Section -->
            <flux:navlist.group heading="Operations" class="grid">
                @can('can see operation dashboard')
                <flux:navlist.item icon="squares-2x2" href="{{ route('operations.index') }}" wire:navigate
                    class="{{ request()->routeIs('operations.index') ? 'active' : '' }}">
                    Operations Dashboard
                </flux:navlist.item>
                @endcan
                @can('can manage production orders')
                <flux:navlist.item icon="clipboard-document-list" href="{{ route('operations.production-orders') }}" wire:navigate
                    class="{{ request()->routeIs('operations.production-orders') ? 'active' : '' }}">
                    Production Orders
                </flux:navlist.item>
                @endcan
                @can('can add downtime record')
                <flux:navlist.item icon="clock" href="{{ route('operations.downtime-record') }}" wire:navigate
                    class="{{ request()->routeIs('operations.downtime-record') ? 'active' : '' }}">
                    Downtime Record
                </flux:navlist.item>
                @endcan
                @can('can see scrape  or waste report')
                <flux:navlist.item icon="trash" href="{{ route('operations.waste-report') }}" wire:navigate
                    class="{{ request()->routeIs('operations.waste-report') ? 'active' : '' }}">
                    Waste Report
                </flux:navlist.item>
                @endcan
                @can('can see daily production report')
                <flux:navlist.item icon="chart-bar" href="{{ route('operations.production-report') }}" wire:navigate
                    class="{{ request()->routeIs('operations.production-report') ? 'active' : '' }}">
                    Daily Production Report
                </flux:navlist.item>
                @endcan
                @can('can see weekly production report')
                <flux:navlist.item icon="chart-bar-square" href="{{ route('operations.reports.weekly') }}" wire:navigate
                    class="{{ request()->routeIs('operations.reports.weekly') ? 'active' : '' }}">
                    Weekly Production Report
                </flux:navlist.item>
                @endcan
                @can('can see monthly production report')
                <flux:navlist.item icon="chart-pie" href="{{ route('operations.reports.monthly') }}" wire:navigate
                    class="{{ request()->routeIs('operations.reports.monthly') ? 'active' : '' }}">
                    Monthly Production Report
                </flux:navlist.item>
                @endcan
            </flux:navlist.group>
             @endcan

             @can(['can view sales section'])  
            <!-- Sales Section -->
            <flux:navlist.group heading="Sales" class="grid">
                @can('can see sales dashboard')
                <flux:navlist.item icon="squares-2x2" href="{{ route('sales.index') }}" wire:navigate
                    class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">
                    Sales Dashboard
                </flux:navlist.item>
                @endcan
                @can('can create orders')
                <flux:navlist.item icon="plus-circle" href="{{ route('sales.create-order') }}" wire:navigate
                    class="{{ request()->routeIs('sales.create-order') ? 'active' : '' }}">
                    Create Order
                </flux:navlist.item>
                @endcan
                @can('can deliver orders')
                <flux:navlist.item icon="truck" href="{{ route('sales.deliveries') }}" wire:navigate
                    class="{{ request()->routeIs('sales.deliveries') ? 'active' : '' }}">
                    Deliveries
                </flux:navlist.item>
                @endcan
                @can('can record order payments')
                <flux:navlist.item icon="credit-card" href="{{ route('sales.payments') }}" wire:navigate
                    class="{{ request()->routeIs('sales.payments') ? 'active' : '' }}">
                    Payments
                </flux:navlist.item>
                @endcan
             
                <flux:navlist.item icon="document-chart-bar" href="{{ route('sales.reports') }}" wire:navigate
                    class="{{ request()->routeIs('sales.reports') ? 'active' : '' }}">
                    Reports
                </flux:navlist.item>
                @can('can create orders')
                <flux:navlist.item icon="clipboard-document-list" href="{{ route('sales.orders') }}" wire:navigate
                    class="{{ request()->routeIs('sales.orders') ? 'active' : '' }}">
                    Orders
                </flux:navlist.item>
                @endcan
            </flux:navlist.group>
             @endcan

             @can(['can view finance section'])

            <!-- Finance Section -->
            <flux:navlist.group heading="Finance" class="grid">
                <flux:navlist.item icon="squares-2x2" href="{{ route('finance.index') }}" wire:navigate
                    class="{{ request()->routeIs('finance.index') ? 'active' : '' }}">
                    Finance Dashboard
                </flux:navlist.item>
                <flux:navlist.item icon="banknotes" href="{{ route('finance.revenue-report') }}" wire:navigate
                    class="{{ request()->routeIs('finance.revenue-report') ? 'active' : '' }}">
                    Revenue Report
                </flux:navlist.item>
            </flux:navlist.group>  
             @endcan
             @can('can manage quality control')
            <!-- Settings Section -->
            <flux:navlist.group heading="Settings" class="grid">
                <flux:navlist.item icon="clipboard-document-check" href="{{ route('settings.quality-reports') }}" wire:navigate
                    class="{{ request()->routeIs('settings.quality-reports') ? 'active' : '' }}">
                    Quality Reports
                </flux:navlist.item>
            </flux:navlist.group>
            @endcan

            <!-- Notifications Section -->
            <flux:navlist.group heading="Notifications" class="grid">
                <flux:navlist.item icon="bell" href="{{ route('notifications.index') }}" wire:navigate
                    class="{{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                    All Notifications
                </flux:navlist.item>
                <flux:navlist.item icon="bell-alert" href="{{ route('notifications.index', ['filter' => 'unread']) }}" wire:navigate
                    class="{{ request()->get('filter') === 'unread' ? 'active' : '' }}">
                    Unread Only
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        @if (Session::has('admin_user_id'))
            <div
                class="py-2 flex items-center justify-center bg-zinc-100 dark:bg-zinc-600 dark:text-white mb-6 rounded">
                <form id="stop-impersonating" class="flex flex-col items-center gap-3"
                    action="{{ route('impersonate.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <p class="text-xs">
                        {{ __('users.you_are_impersonating') }}:
                        <strong>{{ auth()->user()->name }}</strong>
                    </p>
                    <flux:button type="submit" size="sm" variant="danger" form="stop-impersonating"
                        class="!w-full !flex !flex-row">
                        <div>
                            {{ __('users.stop_impersonating') }}
                        </div>
                    </flux:button>
                </form>
            </div>
        @endif

        @auth
            <flux:dropdown position="bottom" align="start">
                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog-6-tooth" wire:navigate>
                            {{ __('global.settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right" class="w-full">
                            {{ __('global.log_out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        @endauth
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />

        <flux:spacer />

        @auth
            <flux:dropdown position="top" align="end">
                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-left text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item href="/settings/profile" icon="cog-6-tooth" wire:navigate>
                            {{ __('global.settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right"
                            class="w-full">
                            {{ __('global.log_out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        @endauth
    </flux:header>

    {{ $slot }}

    @fluxScripts
    <x-livewire-alert::scripts />
    <x-livewire-alert::flash />
    
    <script>
        // Add active class based on current URL for better client-side navigation
        document.addEventListener('livewire:navigated', () => {
            // This will run after Livewire navigation
            highlightActiveLinks();
        });
        
        // Initial highlighting on page load
        document.addEventListener('DOMContentLoaded', () => {
            highlightActiveLinks();
        });
        
        function highlightActiveLinks() {
            // Remove all active classes first
            document.querySelectorAll('.flux-navlist-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Get current path
            const currentPath = window.location.pathname;
            
            // Add active class to matching links
            document.querySelectorAll('.flux-navlist-item').forEach(item => {
                const href = item.getAttribute('href');
                if (href && currentPath.startsWith(href) && href !== '/') {
                    item.classList.add('active');
                } else if (href === currentPath) {
                    item.classList.add('active');
                }
            });
        }
    </script>
</body>

</html>