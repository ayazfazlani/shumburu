<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
    <!-- BoxHero Theme CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        /* ─── Active link styling - BoxHero Purple ─── */
        .flux-navlist-item[aria-current="page"],
        .flux-navlist-item.active {
            background: linear-gradient(135deg, #6B4EFF 0%, #7C5CFF 100%) !important;
            color: white !important;
            box-shadow: 0 2px 8px rgba(107, 78, 255, 0.35) !important;
            font-weight: 600 !important;
            position: relative;
        }

        .flux-navlist-item[aria-current="page"] .flux-icon,
        .flux-navlist-item.active .flux-icon {
            color: white !important;
        }

        .flux-navlist-item[aria-current="page"]::before,
        .flux-navlist-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: white;
            border-radius: 0 3px 3px 0;
            opacity: 0.6;
        }

        .dark .flux-navlist-item[aria-current="page"],
        .dark .flux-navlist-item.active {
            background: linear-gradient(135deg, #6B4EFF 0%, #7C5CFF 100%) !important;
        }

        /* ─── Sidebar Styles ─── */
        .app-sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 40;
            width: 260px;
            flex-shrink: 0;
            background: var(--bx-white) !important;
            border-right: 1px solid var(--bx-gray-200) !important;
            box-shadow: var(--bx-shadow-sm) !important;
        }

        .dark .app-sidebar {
            background: #18181B !important;
            border-right-color: var(--bx-gray-200) !important;
        }

        /* Sidebar Logo Area */
        .app-sidebar .sidebar-logo {
            padding: var(--bx-space-4) var(--bx-space-5) !important;
            margin-bottom: var(--bx-space-2) !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Sidebar Scrollbar */
        .app-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .app-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .app-sidebar::-webkit-scrollbar-thumb {
            background: var(--bx-gray-300);
            border-radius: var(--bx-radius-full);
        }

        .app-sidebar::-webkit-scrollbar-thumb:hover {
            background: var(--bx-gray-400);
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 39;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* ─── Mobile Bottom Navigation ─── */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bx-white) !important;
            border-top: 1px solid var(--bx-gray-200) !important;
            padding: 8px 0 env(safe-area-inset-bottom, 8px);
            justify-content: space-around;
            z-index: 50;
            box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05) !important;
        }

        .dark .mobile-bottom-nav {
            background: #18181B !important;
            border-top-color: var(--bx-gray-200) !important;
        }

        .mobile-bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.6rem;
            color: var(--bx-gray-500);
            text-decoration: none;
            padding: 4px 12px;
            border-radius: 8px;
            transition: all 150ms ease;
            gap: 2px;
            min-width: 60px;
        }

        .mobile-bottom-nav a svg {
            width: 24px;
            height: 24px;
            color: var(--bx-gray-400);
            transition: color 150ms ease;
        }

        .mobile-bottom-nav a.active {
            color: var(--bx-primary) !important;
            background: var(--bx-primary-light) !important;
        }

        .mobile-bottom-nav a.active svg {
            color: var(--bx-primary) !important;
        }

        /* ─── Responsive ─── */
        @media (max-width: 1024px) {
            .app-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                height: 100vh;
                width: 280px;
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1) !important;
            }

            .app-sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                padding-bottom: 80px !important;
            }

            .mobile-bottom-nav {
                display: flex;
            }
        }

        @media (min-width: 1025px) {
            .app-sidebar {
                transform: none !important;
                position: sticky;
            }

            .sidebar-overlay {
                display: none !important;
            }
        }

        /* ─── Layout Container ─── */
        .app-layout {
            display: flex;
            min-height: 100vh;
            background: var(--bx-gray-50);
        }

        .dark .app-layout {
            background: #18181B;
        }

        .main-content {
            flex: 1;
            min-width: 0;
            background: var(--bx-gray-50);
        }

        .dark .main-content {
            background: #18181B;
        }

        /* ─── BoxHero Text Gradient ─── */
        .bx-text-gradient {
            background: linear-gradient(135deg, var(--bx-primary), var(--bx-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ─── BoxHero Button Styles ─── */
        .bx-button-outline {
            border: 1.5px solid var(--bx-gray-200) !important;
            background: var(--bx-white) !important;
            color: var(--bx-gray-700) !important;
            font-weight: 600 !important;
            padding: var(--bx-space-2) var(--bx-space-5) !important;
            border-radius: var(--bx-radius-md) !important;
            transition: all var(--bx-transition-fast) !important;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            text-decoration: none;
        }

        .bx-button-outline:hover {
            border-color: var(--bx-primary) !important;
            color: var(--bx-primary) !important;
            background: var(--bx-primary-light) !important;
            text-decoration: none;
        }

        /* ─── Mobile Header ─── */
        .mobile-header {
            display: none;
        }

        @media (max-width: 1024px) {
            .mobile-header {
                display: flex;
                position: sticky;
                top: 0;
                z-index: 30;
                background: var(--bx-white) !important;
                border-bottom: 1px solid var(--bx-gray-200) !important;
                padding: 0 1rem;
                height: 56px;
                align-items: center;
                justify-content: space-between;
                backdrop-filter: blur(8px);
                background-color: rgba(255, 255, 255, 0.9) !important;
            }

            .dark .mobile-header {
                background: rgba(24, 24, 27, 0.9) !important;
                border-bottom-color: var(--bx-gray-200) !important;
            }

            .mobile-header .menu-btn {
                padding: 0.5rem;
                border-radius: var(--bx-radius-md);
                transition: background var(--bx-transition-fast);
                background: transparent;
                border: none;
                cursor: pointer;
                color: var(--bx-gray-600);
            }

            .mobile-header .menu-btn:hover {
                background: var(--bx-gray-100);
            }

            .dark .mobile-header .menu-btn:hover {
                background: var(--bx-gray-200);
            }
        }
    </style>
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ─── APP LAYOUT ─── -->
    <div class="app-layout">
        <!-- ─── SIDEBAR ─── -->
        <aside class="app-sidebar" id="mainSidebar">
            <!-- Close button (mobile only) -->
            <button class="lg:hidden absolute top-4 right-4 p-2 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors z-50"
                    id="closeSidebarBtn" aria-label="Close sidebar" style="background: var(--bx-white); color: var(--bx-gray-600);">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <!-- Logo -->
            <a href="{{ route('home') }}" class="sidebar-logo" wire:navigate>
                <x-app-logo class="size-8"></x-app-logo>
                <span class="text-xl font-bold bx-text-gradient hidden lg:block">YourApp</span>
            </a>

            <!-- Top Actions -->
            <div class="px-4 space-y-3">
                <a href="{{ route('home') }}" class="bx-button-outline w-full justify-center" wire:navigate>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('global.go_to_frontend') }}
                </a>

                <!-- Notification Center -->
                <div class="w-full">
                    @livewire('components.notification-center')
                </div>
            </div>

            <!-- ─── NAVIGATION ─── -->
            <flux:navlist variant="outline" class="px-2 pb-20" id="mainNavlist">

                <!-- ═══════ PLATFORM SECTION ═══════ -->
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

                <!-- ═══════ ADMINISTRATION SECTION ═══════ -->
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

                <!-- ═══════ WAREHOUSE SECTION ═══════ -->
                @canany(['warehouse.dashboard', 'admin.raw-materials-crud', 'admin.products-crud', 'warehouse.stock-overview', 'warehouse.pending-receipts', 'warehouse.material-issue-requests', 'warehouse.demand-aggregation', 'warehouse.demand-control'])
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
                                class="{{ request()->routeIs('warehouse.products') ? 'active' : '' }}">Products
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.production-machine')
                            <flux:navlist.item icon="view-columns" href="{{ route('warehouse.lines') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.lines') ? 'active' : '' }}">Lines
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.pending-receipts')
                            <flux:navlist.item icon="arrow-down-tray" href="{{ route('warehouse.stock-in') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.stock-in') ? 'active' : '' }}">Stock In
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.material-issue-requests')
                            <flux:navlist.item icon="arrow-up-tray" href="{{ route('warehouse.stock-out') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.stock-out') ? 'active' : '' }}">Stock Out
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.stock-overview')
                            <flux:navlist.item icon="table-cells" href="{{ route('warehouse.fg-stock') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.fg-stock') ? 'active' : '' }}">FG Stock
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.pending-receipts')
                            <flux:navlist.item icon="arrow-down-on-square-stack"
                                href="{{ route('warehouse.pending-receipts', ['tab' => 'fg']) }}" wire:navigate
                                class="{{ request()->get('tab') === 'fg' ? 'active' : '' }}">Production Receipts (FG)
                            </flux:navlist.item>
                            <flux:navlist.item icon="truck" href="{{ route('warehouse.pending-receipts', ['tab' => 'rm']) }}"
                                wire:navigate class="{{ request()->get('tab') === 'rm' ? 'active' : '' }}">Supplier Receipts (RM)
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.material-issue-requests')
                            <flux:navlist.item icon="document-text" href="{{ route('warehouse.material-requests') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.material-requests') ? 'active' : '' }}">Planning PR Demands
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.demand-aggregation')
                            <flux:navlist.item icon="variable" href="{{ route('warehouse.demand-aggregation') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.demand-aggregation') ? 'active' : '' }}">Demand Aggregator (Bulk PR)
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.demand-control')
                            <flux:navlist.item icon="shield-check" href="{{ route('warehouse.demand-control') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.demand-control') ? 'active' : '' }}">Authorizations
                            </flux:navlist.item>
                        @endcan
                    </flux:navlist.group>
                @endcanany

                <!-- ═══════ PLANNING & PRODUCTIONS SECTION ═══════ -->
                @canany(['operations.demand-control', 'operations.production-planning', 'production.manager'])
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
                            <flux:navlist.item icon="queue-list" href="{{ route('warehouse.material-stock-out-lines') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.material-stock-out-lines') ? 'active' : '' }}">Material Stock Out Lines
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.finished-goods')
                            <flux:navlist.item icon="check-badge" href="{{ route('warehouse.finished-goods') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.finished-goods') ? 'active' : '' }}">Finished Goods
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.finished-good-material-stock-out-line-crud')
                            <flux:navlist.item icon="link" href="{{ route('warehouse.finished-good-material') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.finished-good-material') ? 'active' : '' }}">FG Material Stock Out Links
                            </flux:navlist.item>
                        @endcan
                        @can('warehouse.scrap-waste-crud')
                            <flux:navlist.item icon="trash" href="{{ route('warehouse.scrap-wastes') }}" wire:navigate
                                class="{{ request()->routeIs('warehouse.scrap-wastes') ? 'active' : '' }}">Scrap/Waste
                            </flux:navlist.item>
                        @endcan
                    </flux:navlist.group>
                @endcanany

                <!-- ═══════ OPERATIONS MANAGEMENT SECTION ═══════ -->
                @canany(['operations.dashboard', 'operations.production-orders', 'operations.downtime-record', 'reports.production-report', 'reports.weekly-production-report', 'reports.monthly-production-report'])
                    <flux:navlist.group heading="Operations Management" class="grid">
                        @can('operations.dashboard')
                            <flux:navlist.item icon="squares-2x2" href="{{ route('operations.index') }}" wire:navigate
                                class="{{ request()->routeIs('operations.index') ? 'active' : '' }}">Operations Dashboard
                            </flux:navlist.item>
                        @endcan
                        @can('operations.production-orders')
                            <flux:navlist.item icon="clipboard-document-list" href="{{ route('operations.production-orders') }}" wire:navigate
                                class="{{ request()->routeIs('operations.production-orders') ? 'active' : '' }}">Production Orders
                            </flux:navlist.item>
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

                <!-- ═══════ SALES SECTION ═══════ -->
                @canany(['sales.dashboard', 'sales.create-order', 'sales.orders-overview', 'sales.deliveries', 'sales.daily-sales-report', 'sales.weekly-sales-report', 'sales.monthly-sales-report'])
                    <flux:navlist.group heading="Sales" class="grid">
                        @can('sales.dashboard')
                            <flux:navlist.item icon="squares-2x2" href="{{ route('sales.index') }}" wire:navigate
                                class="{{ request()->routeIs('sales.index') ? 'active' : '' }}">Sales Dashboard
                            </flux:navlist.item>
                        @endcan
                        @can('sales.create-order')
                            <flux:navlist.item icon="plus-circle" href="{{ route('sales.create-order') }}" wire:navigate
                                class="{{ request()->routeIs('sales.create-order') ? 'active' : '' }}">Create Order
                            </flux:navlist.item>
                        @endcan
                        @can('sales.orders-overview')
                            <flux:navlist.item icon="clipboard-document-list" href="{{ route('sales.orders') }}" wire:navigate
                                class="{{ request()->routeIs('sales.orders') ? 'active' : '' }}">Orders
                            </flux:navlist.item>
                        @endcan
                        @can('sales.deliveries')
                            <flux:navlist.item icon="truck" href="{{ route('sales.deliveries') }}" wire:navigate
                                class="{{ request()->routeIs('sales.deliveries') ? 'active' : '' }}">Deliveries
                            </flux:navlist.item>
                        @endcan
                        @canany(['sales.daily-sales-report', 'sales.weekly-sales-report', 'sales.monthly-sales-report'])
                            <flux:navlist.item icon="document-chart-bar" href="{{ route('sales.reports') }}" wire:navigate
                                class="{{ request()->routeIs('sales.reports') ? 'active' : '' }}">Sales Reports
                            </flux:navlist.item>
                        @endcanany
                    </flux:navlist.group>
                @endcanany

                <!-- ═══════ FINANCE SECTION ═══════ -->
                @canany(['finance.dashboard', 'finance.procurement', 'finance.purchase-payments', 'sales.payments', 'finance.revenue-report'])
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

                <!-- ═══════ GENERAL REPORTS SECTION ═══════ -->
                @canany(['sales.daily-sales-report', 'reports.raw-material-stock-balance-report'])
                    <flux:navlist.group heading="General Reports" class="grid">
                        @can('sales.daily-sales-report')
                            <flux:navlist.item icon="calendar" href="{{ route('sales.daily-sales-report') }}" wire:navigate
                                class="{{ request()->routeIs('sales.daily-sales-report') ? 'active' : '' }}">Daily Sales
                            </flux:navlist.item>
                        @endcan
                        @can('reports.raw-material-stock-balance-report')
                            <flux:navlist.item icon="scale" href="{{ route('reports.raw-material-stock-balance') }}" wire:navigate
                                class="{{ request()->routeIs('reports.raw-material-stock-balance') ? 'active' : '' }}">Material Balance
                            </flux:navlist.item>
                        @endcan
                    </flux:navlist.group>
                @endcanany

                <!-- ═══════ QUALITY SECTION ═══════ -->
                @can('quality.quality-report-manager')
                    <flux:navlist.group heading="Quality" class="grid">
                        <flux:navlist.item icon="clipboard-document-check" href="{{ route('settings.quality-reports') }}" wire:navigate
                            class="{{ request()->routeIs('settings.quality-reports') ? 'active' : '' }}">Quality Reports
                        </flux:navlist.item>
                    </flux:navlist.group>
                @endcan

                <!-- ═══════ NOTIFICATIONS SECTION ═══════ -->
                <flux:navlist.group heading="Notifications" class="grid">
                    <flux:navlist.item icon="bell" href="{{ route('notifications.index') }}" wire:navigate
                        class="{{ request()->routeIs('notifications.index') ? 'active' : '' }}">All Notifications
                    </flux:navlist.item>
                </flux:navlist.group>

                <!-- ═══════ ACCOUNT SECTION ═══════ -->
                <flux:navlist.group heading="Account" class="grid">
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        @method('POST')
                        <flux:navlist.item as="button" type="submit" icon="arrow-right" class="w-full text-left">Logout
                        </flux:navlist.item>
                    </form>
                </flux:navlist.group>
            </flux:navlist>

            <!-- User Profile (bottom of sidebar) -->
            @auth
                <div class="border-t border-zinc-200 dark:border-zinc-700 p-4 mt-auto" style="background: var(--bx-white);">
                    <flux:dropdown position="bottom" align="start" class="w-full">
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
                </div>
            @endauth
        </aside>

        <!-- ─── MAIN CONTENT ─── -->
        <main class="main-content" id="mainContent">
            <!-- Mobile Header -->
            <div class="mobile-header lg:hidden">
                <button class="menu-btn" id="openSidebarBtn" aria-label="Open sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <span class="font-bold bx-text-gradient text-sm">YourApp</span>
                <div class="w-8"></div>
            </div>

            {{ $slot }}
        </main>
    </div>

    <!-- ─── MOBILE BOTTOM NAV ─── -->
    @include('partials.mobile-bottom-nav')

    @fluxScripts
    <x-livewire-alert::scripts />
    <x-livewire-alert::flash />

    <script>
        // ─── Sidebar State ───
        let sidebarOpen = false;
        let isMobile = window.innerWidth < 1025;

        function updateIsMobile() {
            isMobile = window.innerWidth < 1025;
        }

        function openSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (isMobile) {
                sidebar.classList.add('open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                sidebarOpen = true;
            }
        }

        function closeSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (isMobile) {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
                sidebarOpen = false;
            }
        }

        function toggleSidebar() {
            if (sidebarOpen) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        // ─── Setup Event Listeners ───
        function setupEventListeners() {
            // Open button
            const openBtn = document.getElementById('openSidebarBtn');
            if (openBtn) {
                const newOpenBtn = openBtn.cloneNode(true);
                openBtn.parentNode.replaceChild(newOpenBtn, openBtn);
                newOpenBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            // Close button (X)
            const closeBtn = document.getElementById('closeSidebarBtn');
            if (closeBtn) {
                const newCloseBtn = closeBtn.cloneNode(true);
                closeBtn.parentNode.replaceChild(newCloseBtn, closeBtn);
                newCloseBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    closeSidebar();
                });
            }

            // Overlay click
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                const newOverlay = overlay.cloneNode(true);
                overlay.parentNode.replaceChild(newOverlay, overlay);
                newOverlay.addEventListener('click', function(e) {
                    closeSidebar();
                });
            }
        }

        // ─── Highlight Active Links ───
        function highlightActiveLinks() {
            const currentPath = window.location.pathname;

            document.querySelectorAll('.flux-navlist-item').forEach(item => {
                item.classList.remove('active');
                const href = item.getAttribute('href');
                if (href) {
                    if (currentPath === href || (href !== '/' && currentPath.startsWith(href))) {
                        item.classList.add('active');
                    }
                }
            });

            document.querySelectorAll('.mobile-bottom-nav a').forEach(item => {
                item.classList.remove('active');
                const href = item.getAttribute('href');
                if (href) {
                    if (currentPath === href || (href !== '/' && currentPath.startsWith(href))) {
                        item.classList.add('active');
                    }
                }
            });
        }

        // ─── Document Ready ───
        document.addEventListener('DOMContentLoaded', function() {
            updateIsMobile();
            setupEventListeners();
            highlightActiveLinks();

            window.addEventListener('resize', function() {
                const wasMobile = isMobile;
                updateIsMobile();
                if (wasMobile && !isMobile) {
                    closeSidebar();
                }
                if (!wasMobile && isMobile) {
                    closeSidebar();
                }
            });

            // Close sidebar on outside click
            document.addEventListener('click', function(e) {
                const sidebar = document.getElementById('mainSidebar');
                const openBtn = document.getElementById('openSidebarBtn');

                if (sidebarOpen && isMobile) {
                    const isClickInsideSidebar = sidebar.contains(e.target);
                    const isClickOnToggle = openBtn && openBtn.contains(e.target);

                    if (!isClickInsideSidebar && !isClickOnToggle) {
                        closeSidebar();
                    }
                }
            });
        });

        // ─── Livewire Navigation ───
        document.addEventListener('livewire:navigated', function() {
            closeSidebar();
            updateIsMobile();

            setTimeout(function() {
                setupEventListeners();
                highlightActiveLinks();
            }, 100);
        });

        document.addEventListener('livewire:navigating', function() {
            closeSidebar();
        });
    </script>
</body>
</html>
