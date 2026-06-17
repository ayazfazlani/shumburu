<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────
        // EXISTING PERMISSIONS — DO NOT MODIFY OR REMOVE
        // These are already assigned to roles in production.
        // ─────────────────────────────────────────────────────────────
        $existingPermissions = [
            'Selse',
            'can manage customers',
            'can see customer name',
            'can manage quality control',
            'can manage fya-warehouse',
            'can view orders',
            'can record order payments',
            'can deliver orders',
            'can create orders',
            'can see sales dashboard',
            'can see monthly production report',
            'can see weekly production report',
            'can see daily production report',
            'can see scrape or waste report',
            'can see weekly sales reports',
            'can see daily sales reports',
            'can see raw meterial balance',
            'can see monthly sales reports',
            'can add downtime record',
            'can see operation dashboard',
            'can see material stock out lines',
            'can see scrap waste',
            'can see FG material stock out links',
            'can record finished goods',
            'can perform material stock out',
            'can perform material stock in',
            'can view finance section',
            'can view operations section',
            'can view reports section',
            'can view sales section',
            'can view users section',
            'view customer names',
            'view inventory reports',
            'view revenue reports',
            'access finance',
            'view sales reports',
            'record payments',
            'record deliveries',
            'create orders',
            'access sales',
            'view production reports',
            'view waste reports',
            'record downtime',
            'access operations',
            'record scrap waste',
            'record finished goods',
            'perform stock-out',
            'perform stock-in',
            'access warehouse',
            'update permissions',
            'create permissions',
            'view permissions',
            'delete permissions',
            'delete roles',
            'update roles',
            'create roles',
            'view roles',
            'delete users',
            'update users',
            'create users',
            'view users',
            'impersonate',
            'access dashboard',
            'can manage production orders',
            'can view warehouse section',
        ];

        // ─────────────────────────────────────────────────────────────
        // NEW PERMISSIONS — Clean module.action naming convention
        // Added for new modules and features.
        // ─────────────────────────────────────────────────────────────
        $newPermissions = [
            // Administration
            'admin.customers-crud',
            'admin.suppliers-crud',
            'admin.raw-materials-crud',
            'admin.products-crud',
            'admin.users-crud',
            'admin.roles-crud',

            // Warehouse
            'warehouse.stock-overview',
            'warehouse.pending-receipts',
            'warehouse.material-issue-requests',
            'warehouse.demand-aggregation',
            'warehouse.material-stock-out-line-crud',
            'warehouse.finished-goods',
            'warehouse.finished-good-material-stock-out-line-crud',
            'warehouse.scrap-waste-crud',
            'warehouse.dashboard',

            // Operations
            'operations.production-planning',
            'operations.production-orders',
            'operations.demand-control',
            'operations.dashboard',
            'operations.downtime-record',

            // Production
            'production.manager',

            // Finance
            'finance.procurement',
            'finance.purchase-payments',
            'finance.revenue-report',
            'finance.inventory-report',
            'finance.dashboard',

            // Reports
            'reports.production-report',
            'reports.weekly-production-report',
            'reports.monthly-production-report',
            'reports.raw-material-stock-balance-report',

            // Sales
            'sales.daily-sales-report',
            'sales.weekly-sales-report',
            'sales.monthly-sales-report',
            'sales.orders-overview',
            'sales.create-order',
            'sales.deliveries',
            'sales.payments',
            'sales.dashboard',

            // Quality
            'quality.quality-report-manager',

            // Management
            'management.management-dashboard',

            // Dashboard
            'dashboard.view',
        ];

        // ─────────────────────────────────────────────────────────────
        // PERMISSION MAPPING — Renaming old names to new standard
        // This preserves assignments (linked by ID) while updating names.
        // ─────────────────────────────────────────────────────────────
        $mappings = [
            'can manage customers' => 'admin.customers-crud',
            'can manage suppliers' => 'admin.suppliers-crud',
            'can manage raw materials' => 'admin.raw-materials-crud',
            'can manage products' => 'admin.products-crud',
            'can manage users' => 'admin.users-crud',
            'can manage roles' => 'admin.roles-crud',
            'view users' => 'admin.users-crud',
            'view roles' => 'admin.roles-crud',

            'warehouse.view-fg-stock' => 'warehouse.stock-overview',
            'warehouse.confirm-receipts' => 'warehouse.pending-receipts',
            'warehouse.manage-material-requests' => 'warehouse.material-issue-requests',
            'warehouse.manage-demand-aggregation' => 'warehouse.demand-aggregation',
            'warehouse.manage-authorizations' => 'warehouse.demand-control',
            'warehouse.manage-production-lines' => 'warehouse.production-machine',
            'warehouse.view-dashboard' => 'warehouse.dashboard',
            'can see material stock out lines' => 'warehouse.material-stock-out-line-crud',
            'can record finished goods' => 'warehouse.finished-goods',
            'can see FG material stock out links' => 'warehouse.finished-good-material-stock-out-line-crud',
            'can see scrap waste' => 'warehouse.scrap-waste-crud',

            'operations.manage-demand-control' => 'operations.demand-control',
            'operations.manage-production-planning' => 'operations.production-planning',
            'operations.manage-production-manager' => 'production.manager',
            'operations.view-dashboard' => 'operations.dashboard',
            'operations.manage-production-orders' => 'operations.production-orders',
            'operations.add-downtime' => 'operations.downtime-record',
            'operations.view-daily-production-report' => 'reports.production-report',
            'operations.view-weekly-production-report' => 'reports.weekly-production-report',
            'operations.view-monthly-production-report' => 'reports.monthly-production-report',

            'sales.view-dashboard' => 'sales.dashboard',
            'sales.create-orders' => 'sales.create-order',
            'sales.manage-orders' => 'sales.orders-overview',
            'sales.manage-deliveries' => 'sales.deliveries',
            'sales.view-daily-sales-report' => 'sales.daily-sales-report',
            'sales.view-weekly-sales-report' => 'sales.weekly-sales-report',
            'sales.view-monthly-sales-report' => 'sales.monthly-sales-report',

            'finance.view-dashboard' => 'finance.dashboard',
            'finance.manage-procurement' => 'finance.procurement',
            'finance.manage-purchase-payments' => 'finance.purchase-payments',
            'sales.manage-payments' => 'sales.payments',
            'finance.view-revenue-report' => 'finance.revenue-report',
            'finance.view-inventory-report' => 'finance.inventory-report',

            'reports.view-material-balance' => 'reports.raw-material-stock-balance-report',
            'quality.manage-reports' => 'quality.quality-report-manager',
            'management.view-cockpit' => 'management.management-dashboard',

            'access dashboard' => 'dashboard.view',
        ];

        foreach ($mappings as $old => $new) {
            $permission = Permission::where('name', $old)->first();
            if ($permission) {
                // Only rename if the new name doesn't already exist for another ID
                $exists = Permission::where('name', $new)->where('id', '!=', $permission->id)->first();
                if (!$exists) {
                    $permission->update(['name' => $new]);
                }
            }
        }

        // Seed all existing permissions (idempotent)
        // We still keep the list for those that didn't get mapped or are still needed as is
        foreach ($existingPermissions as $permission) {
            Permission::query()->updateOrCreate([
                'name' => $permission,
            ]);
        }

        // Seed all new permissions (idempotent)
        foreach ($newPermissions as $permission) {
            Permission::query()->updateOrCreate([
                'name' => $permission,
            ]);
        }
    }
}
