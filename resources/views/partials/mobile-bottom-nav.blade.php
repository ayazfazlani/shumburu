<!-- resources/views/partials/mobile-bottom-nav.blade.php -->
@auth
<nav class="mobile-bottom-nav">
    @php
        // Helper function to check if current route matches
        function isActiveRoute($routeName) {
            return request()->routeIs($routeName) ? 'active' : '';
        }

        // Helper to check if any route matches a pattern
        function isActiveRoutePattern($pattern) {
            return request()->routeIs($pattern) ? 'active' : '';
        }
    @endphp

    <!-- ─── DASHBOARD ─── -->
    @can('dashboard.view')
        <a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}" wire:navigate>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span>Dashboard</span>
        </a>
    @endcan

    <!-- ─── WAREHOUSE ─── -->
    @canany(['warehouse.dashboard', 'warehouse.stock-overview', 'warehouse.pending-receipts', 'admin.raw-materials-crud', 'admin.products-crud'])
        <a href="{{ route('warehouse.index') }}" class="{{ request()->routeIs('warehouse.*') ? 'active' : '' }}" wire:navigate>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Warehouse</span>
        </a>
    @endcan

    <!-- ─── PLANNING & PRODUCTION ─── -->
    @canany(['operations.production-planning', 'production.manager', 'warehouse.material-stock-out-line-crud', 'warehouse.finished-goods'])
        <a href="{{ route('operations.planning') }}" class="{{ request()->routeIs('operations.planning') || request()->routeIs('operations.manage-production') || request()->routeIs('warehouse.material-stock-out-lines') || request()->routeIs('warehouse.finished-goods') ? 'active' : '' }}" wire:navigate>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
            </svg>
            <span>Production</span>
        </a>
    @endcan

    <!-- ─── SALES ─── -->
    @canany(['sales.dashboard', 'sales.create-order', 'sales.orders-overview'])
        <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'active' : '' }}" wire:navigate>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <span>Sales</span>
        </a>
    @endcan

    <!-- ─── FINANCE ─── -->
    @canany(['finance.dashboard', 'finance.procurement', 'finance.purchase-payments', 'sales.payments', 'finance.revenue-report'])
        <a href="{{ route('finance.index') }}" class="{{ request()->routeIs('finance.*') ? 'active' : '' }}" wire:navigate>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 1v1m0 1V9m0 1V8"/>
            </svg>
            <span>Finance</span>
        </a>
    @endcan

    <!-- ─── OPERATIONS ─── -->
    @canany(['operations.dashboard', 'operations.production-orders', 'operations.downtime-record', 'reports.production-report'])
        <a href="{{ route('operations.index') }}" class="{{ request()->routeIs('operations.index') ? 'active' : '' }}" wire:navigate>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Operations</span>
        </a>
    @endcan

    <!-- ─── ADMIN ─── -->
    @canany(['admin.customers-crud', 'admin.suppliers-crud', 'admin.users-crud', 'admin.roles-crud'])
        <a href="{{ route('admin.customers-crud') }}" class="{{ request()->routeIs('admin.customers-crud') || request()->routeIs('admin.suppliers-crud') || request()->routeIs('admin.users-crud') || request()->routeIs('admin.roles-crud') ? 'active' : '' }}" wire:navigate>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span>Admin</span>
        </a>
    @endcan

    <!-- ─── NOTIFICATIONS ─── -->
    @can('dashboard.view')
        <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.index') ? 'active' : '' }}" wire:navigate>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.828 7l2.586 2.586a2 2 0 002.828 0L12.828 7H4.828zM4.828 17h8l-2.586-2.586a2 2 0 00-2.828 0L4.828 17z"/>
            </svg>
            <span>Alerts</span>
        </a>
    @endcan
</nav>
@endauth
