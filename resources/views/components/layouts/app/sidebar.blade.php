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
                @can('dashboard.view')
                    <flux:navlist.item icon="squares-2x2" href="{{ route('admin.index') }}" wire:navigate
                        class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">Dashboard</flux:navlist.item>
                @endcan
                @can('management.management-dashboard')
                    <flux:navlist.item icon="presentation-chart-bar" href="{{ route('management.cockpit') }}" wire:navigate
                        class="{{ request()->routeIs('management.cockpit') ? 'active' : '' }}">Executive Cockpit
                    </flux:navlist.item>
                @endcan
            </flux:navlist.group>

            <!-- Administration Section -->
            <flux:navlist.group heading="Administration" class="grid">
                @can('admin.customers-crud')
                    <flux:navlist.item icon="users" href="{{ route('admin.customers-crud') }}" wire:navigate
                        class="{{ request()->routeIs('admin.customers-crud') ? 'active' : '' }}">Customers
                    </flux:navlist.item>
                @endcan
                @can('admin.suppliers-crud')
                    <flux:navlist.item icon="building-office-2" href="{{ route('admin.suppliers-crud') }}" wire:navigate
                        class="{{ request()->routeIs('admin.suppliers-crud') ? 'active' : '' }}">Suppliers
                    </flux:navlist.item>
                @endcan
                @can('admin.users-crud')
                    <flux:navlist.item icon="user-group" href="{{ route('admin.users-crud') }}" wire:navigate
                        class="{{ request()->routeIs('admin.users-crud') ? 'active' : '' }}">{{ __('users.title') }}
                    </flux:navlist.item>
                @endcan
                @can('admin.roles-crud')
                    <flux:navlist.item icon="shield-exclamation" href="{{ route('admin.roles-crud') }}" wire:navigate
                        class="{{ request()->routeIs('admin.roles-crud') ? 'active' : '' }}">{{ __('roles.title') }}
                    </flux:navlist.item>
                @endcan
            </flux:navlist.group>

            @canany(['warehouse.dashboard', 'admin.raw-materials-crud', 'admin.products-crud', 'warehouse.stock-overview', 'warehouse.pending-receipts', 'warehouse.material-issue-requests', 'warehouse.demand-aggregation', 'warehouse.demand-control'])
                <!-- Warehouse Section -->
                <flux:navlist.group heading="Warehouse" class="grid">
                    @can('warehouse.dashboard')
                        <flux:navlist.item icon="building-storefront" href="{{ route('warehouse.index') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.index') ? 'active' : '' }}">Warehouse Dashboard
                        </flux:navlist.item>
                    @endcan
                    @can('admin.raw-materials-crud')
                        <flux:navlist.item icon="cube" href="{{ route('admin.admin.raw-materials.index') }}" wire:navigate
                            class="{{ request()->routeIs('admin.admin.raw-materials.index') ? 'active' : '' }}">
                            {{ __('Raw materials') }}
                        </flux:navlist.item>
                    @endcan
                    @can('admin.products-crud')
                        <flux:navlist.item icon="cube" href="{{ route('warehouse.products') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.products') ? 'active' : '' }}">Products</flux:navlist.item>
                    @endcan
                    @can('warehouse.production-machine')
                        <flux:navlist.item icon="view-columns" href="{{ route('warehouse.lines') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.lines') ? 'active' : '' }}">
                            Lines
                        </flux:navlist.item>
                    @endcan

                    @can('warehouse.pending-receipts')
                        <flux:navlist.item icon="arrow-down-tray" href="{{ route('warehouse.stock-in') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.stock-in') ? 'active' : '' }}">
                            Stock In
                        </flux:navlist.item>
                    @endcan
                    @can('warehouse.material-issue-requests')
                        <flux:navlist.item icon="arrow-up-tray" href="{{ route('warehouse.stock-out') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.stock-out') ? 'active' : '' }}">
                            Stock Out
                        </flux:navlist.item>
                    @endcan
                    @can('warehouse.stock-overview')
                        <flux:navlist.item icon="table-cells" href="{{ route('warehouse.fg-stock') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.fg-stock') ? 'active' : '' }}">FG Stock</flux:navlist.item>
                    @endcan

                    @can('warehouse.pending-receipts')
                        <!-- GRN Receipts Split -->
                        <flux:navlist.item icon="arrow-down-on-square-stack"
                            href="{{ route('warehouse.pending-receipts', ['tab' => 'fg']) }}" wire:navigate
                            class="{{ request()->get('tab') === 'fg' ? 'active' : '' }}">Production Receipts (FG)
                        </flux:navlist.item>
                        <flux:navlist.item icon="truck" href="{{ route('warehouse.pending-receipts', ['tab' => 'rm']) }}"
                            wire:navigate class="{{ request()->get('tab') === 'rm' ? 'active' : '' }}">Supplier Receipts (RM)
                        </flux:navlist.item>
                    @endcan

                    @can('warehouse.material-issue-requests')
                        <!-- Fulfillment & Planning -->
                        <flux:navlist.item icon="document-text" href="{{ route('warehouse.material-requests') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.material-requests') ? 'active' : '' }}">Planning PR Demands
                        </flux:navlist.item>
                    @endcan
                    @can('warehouse.demand-aggregation')
                        <flux:navlist.item icon="variable" href="{{ route('warehouse.demand-aggregation') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.demand-aggregation') ? 'active' : '' }}">Demand Aggregator
                            (Bulk PR)</flux:navlist.item>
                    @endcan



                    @can('warehouse.demand-control')
                        <flux:navlist.item icon="shield-check" href="{{ route('warehouse.demand-control') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.demand-control') ? 'active' : '' }}">Authorizations
                        </flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            @canany(['operations.demand-control', 'operations.production-planning', 'production.manager'])
                <!-- Operations Section -->
                <flux:navlist.group heading="Planning & Productions" class="grid">
                    @can('operations.demand-control')
                        <flux:navlist.item icon="shield-check" href="{{ route('operations.demand-control') }}" wire:navigate
                            class="{{ request()->routeIs('operations.demand-control') ? 'active' : '' }}">Demands Control
                        </flux:navlist.item>
                    @endcan
                    @can('operations.production-planning')
                        <flux:navlist.item icon="calendar-days" href="{{ route('operations.planning') }}" wire:navigate
                            class="{{ request()->routeIs('operations.planning') ? 'active' : '' }}">Production Planning
                        </flux:navlist.item>
                    @endcan
                    @can('production.manager')
                        <flux:navlist.item icon="calendar-days" href="{{ route('operations.manage-production') }}" wire:navigate
                            class="{{ request()->routeIs('operations.manage.production') ? 'active' : '' }}">Production Manager
                        </flux:navlist.item>
                    @endcan

                    @can('warehouse.material-stock-out-line-crud')
                        <flux:navlist.item icon="queue-list" href="{{ route('warehouse.material-stock-out-lines') }}"
                            wire:navigate
                            class="{{ request()->routeIs('warehouse.material-stock-out-lines') ? 'active' : '' }}">Material
                            Stock Out Lines</flux:navlist.item>
                    @endcan
                    @can('warehouse.finished-goods')
                        <flux:navlist.item icon="check-badge" href="{{ route('warehouse.finished-goods') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.finished-goods') ? 'active' : '' }}">Finished Goods
                        </flux:navlist.item>
                    @endcan
                    @can('warehouse.finished-good-material-stock-out-line-crud')
                        <flux:navlist.item icon="link" href="{{ route('warehouse.finished-good-material') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.finished-good-material') ? 'active' : '' }}">FG Material
                            Stock Out Links</flux:navlist.item>
                    @endcan
                    @can('warehouse.scrap-waste-crud')
                        <flux:navlist.item icon="trash" href="{{ route('warehouse.scrap-wastes') }}" wire:navigate
                            class="{{ request()->routeIs('warehouse.scrap-wastes') ? 'active' : '' }}">Scrap/Waste
                        </flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            @canany(['operations.dashboard', 'operations.production-orders', 'operations.downtime-record', 'reports.production-report', 'reports.weekly-production-report', 'reports.monthly-production-report'])
                <!-- Detailed Operations -->
                <flux:navlist.group heading="Operations Management" class="grid">
                    @can('operations.dashboard')
                        <flux:navlist.item icon="squares-2x2" href="{{ route('operations.index') }}" wire:navigate
                            class="{{ request()->routeIs('operations.index') ? 'active' : '' }}">Operations Dashboard
                        </flux:navlist.item>
                    @endcan
                    @can('operations.production-orders')
                        <flux:navlist.item icon="clipboard-document-list" href="{{ route('operations.production-orders') }}"
                            wire:navigate class="{{ request()->routeIs('operations.production-orders') ? 'active' : '' }}">
                            Production Orders</flux:navlist.item>
                    @endcan
                    @can('operations.downtime-record')
                        <flux:navlist.item icon="clock" href="{{ route('operations.downtime-record') }}" wire:navigate
                            class="{{ request()->routeIs('operations.downtime-record') ? 'active' : '' }}">Downtime Record
                        </flux:navlist.item>
                    @endcan
                    @can('reports.production-report')
                        <flux:navlist.item icon="chart-bar" href="{{ route('operations.production-report') }}" wire:navigate
                            class="{{ request()->routeIs('operations.production-report') ? 'active' : '' }}">Daily Prod. Report
                        </flux:navlist.item>
                    @endcan
                    @can('reports.weekly-production-report')
                        <flux:navlist.item icon="chart-bar-square" href="{{ route('operations.reports.weekly') }}" wire:navigate
                            class="{{ request()->routeIs('operations.reports.weekly') ? 'active' : '' }}">Weekly Prod. Report
                        </flux:navlist.item>
                    @endcan
                    @can('reports.monthly-production-report')
                        <flux:navlist.item icon="chart-pie" href="{{ route('operations.reports.monthly') }}" wire:navigate
                            class="{{ request()->routeIs('operations.reports.monthly') ? 'active' : '' }}">Monthly Prod. Report
                        </flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            @canany(['sales.dashboard', 'sales.create-order', 'sales.orders-overview', 'sales.deliveries', 'sales.daily-sales-report', 'sales.weekly-sales-report', 'sales.monthly-sales-report'])
                <!-- Sales Section -->
                <flux:navlist.group heading="Sales" class="grid">
                    @can('sales.dashboard')
                        <flux:navlist.item icon="squares-2x2" href="{{ route('sales.index') }}" wire:navigate
                            class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">Sales Dashboard</flux:navlist.item>
                    @endcan
                    @can('sales.create-order')
                        <flux:navlist.item icon="plus-circle" href="{{ route('sales.create-order') }}" wire:navigate
                            class="{{ request()->routeIs('sales.create-order') ? 'active' : '' }}">Create Order
                        </flux:navlist.item>
                    @endcan
                    @can('sales.orders-overview')
                        <flux:navlist.item icon="clipboard-document-list" href="{{ route('sales.orders') }}" wire:navigate
                            class="{{ request()->routeIs('sales.orders') ? 'active' : '' }}">Orders</flux:navlist.item>
                    @endcan
                    @can('sales.deliveries')
                        <flux:navlist.item icon="truck" href="{{ route('sales.deliveries') }}" wire:navigate
                            class="{{ request()->routeIs('sales.deliveries') ? 'active' : '' }}">Deliveries</flux:navlist.item>
                    @endcan
                    @canany(['sales.daily-sales-report', 'sales.weekly-sales-report', 'sales.monthly-sales-report'])
                        <flux:navlist.item icon="document-chart-bar" href="{{ route('sales.reports') }}" wire:navigate
                            class="{{ request()->routeIs('sales.reports') ? 'active' : '' }}">Sales Reports</flux:navlist.item>
                    @endcanany
                </flux:navlist.group>
            @endcanany

            @canany(['finance.dashboard', 'finance.procurement', 'finance.purchase-payments', 'sales.payments', 'finance.revenue-report'])
                <!-- Finance Section -->
                <flux:navlist.group heading="Finance" class="grid">
                    @can('finance.dashboard')
                        <flux:navlist.item icon="squares-2x2" href="{{ route('finance.index') }}" wire:navigate
                            class="{{ request()->routeIs('finance.index') ? 'active' : '' }}">Finance Dashboard
                        </flux:navlist.item>
                    @endcan
                    @can('finance.procurement')
                        <flux:navlist.item icon="shopping-cart" href="{{ route('finance.procurement') }}" wire:navigate
                            class="{{ request()->routeIs('finance.procurement') ? 'active' : '' }}">Procurement Lifecycle
                        </flux:navlist.item>
                    @endcan
                    @can('finance.purchase-payments')
                        <flux:navlist.item icon="credit-card" href="{{ route('finance.purchase-payments') }}" wire:navigate
                            class="{{ request()->routeIs('finance.purchase-payments') ? 'active' : '' }}">Purchase Payments (AP)
                        </flux:navlist.item>
                    @endcan
                    @can('sales.payments')
                        <flux:navlist.item icon="banknotes" href="{{ route('sales.payments') }}" wire:navigate
                            class="{{ request()->routeIs('sales.payments') ? 'active' : '' }}">Customer Payments (AR)
                        </flux:navlist.item>
                    @endcan
                    @can('finance.revenue-report')
                        <flux:navlist.item icon="banknotes" href="{{ route('finance.revenue-report') }}" wire:navigate
                            class="{{ request()->routeIs('finance.revenue-report') ? 'active' : '' }}">Revenue Report
                        </flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            @canany(['sales.daily-sales-report', 'reports.raw-material-stock-balance-report'])
                <!-- Reports Section -->
                <flux:navlist.group heading="General Reports" class="grid">
                    @can('sales.daily-sales-report')
                        <flux:navlist.item icon="calendar" href="{{ route('sales.daily-sales-report') }}" wire:navigate
                            class="{{ request()->routeIs('sales.daily-sales-report') ? 'active' : '' }}">Daily Sales
                        </flux:navlist.item>
                    @endcan
                    @can('reports.raw-material-stock-balance-report')
                        <flux:navlist.item icon="scale" href="{{ route('reports.raw-material-stock-balance') }}" wire:navigate
                            class="{{ request()->routeIs('reports.raw-material-stock-balance') ? 'active' : '' }}">Material
                            Balance</flux:navlist.item>
                    @endcan
                </flux:navlist.group>
            @endcanany

            @can('quality.quality-report-manager')
                <!-- Quality Section -->
                <flux:navlist.group heading="Quality" class="grid">
                    <flux:navlist.item icon="clipboard-document-check" href="{{ route('settings.quality-reports') }}"
                        wire:navigate class="{{ request()->routeIs('settings.quality-reports') ? 'active' : '' }}">Quality
                        Reports</flux:navlist.item>
                </flux:navlist.group>
            @endcan

            <!-- Notifications Section -->
            <flux:navlist.group heading="Notifications" class="grid">
                <flux:navlist.item icon="bell" href="{{ route('notifications.index') }}" wire:navigate
                    class="{{ request()->routeIs('notifications.index') ? 'active' : '' }}">All Notifications
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group heading="Account" class="grid">
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    @method('POST')
                    <flux:navlist.item as="button" type="submit" icon="arrow-right" class="w-full text-left">Logout
                    </flux:navlist.item>
                </form>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        @auth
            <flux:dropdown position="bottom" align="start">
                <flux:menu class="w-[220px]">
                    <flux:menu.item href="/settings/profile" icon="cog-6-tooth" wire:navigate>Settings</flux:menu.item>
                    <flux:menu.separator />
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right" class="w-full">Log out</flux:menu.item>
                    </form>
                </flux:menu>
                <flux:button variant="ghost" class="w-full !justify-start px-2">
                    <span class="truncate text-sm font-medium">{{ auth()->user()->name }}</span>
                </flux:button>
            </flux:dropdown>
        @endauth
    </flux:sidebar>

    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" />
        <flux:spacer />
    </flux:header>

    {{ $slot }}

    @fluxScripts
    <x-livewire-alert::scripts />
    <x-livewire-alert::flash />

    <script>
        document.addEventListener('livewire:navigated', () => {
            highlightActiveLinks();
        });

        document.addEventListener('DOMContentLoaded', () => {
            highlightActiveLinks();
        });

        function highlightActiveLinks() {
            document.querySelectorAll('.flux-navlist-item').forEach(item => {
                item.classList.remove('active');
            });
            const currentPath = window.location.pathname;
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