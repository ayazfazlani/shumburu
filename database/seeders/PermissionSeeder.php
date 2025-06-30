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
        $permissions = [
            // Dashboard access
            'access dashboard',

            // User impersonation
            'impersonate',

            // User management
            'view users',
            'create users',
            'update users',
            'delete users',

            // Role management
            'view roles',
            'create roles',
            'update roles',
            'delete roles',

            // Permission management
            'view permissions',
            'create permissions',
            'update permissions',
            'delete permissions',

            // Warehouse permissions
            'access warehouse',
            'perform stock-in',
            'perform stock-out',
            'record finished goods',
            'record scrap waste',

            // Operations permissions
            'access operations',
            'record downtime',
            'view waste reports',
            'view production reports',

            // Sales permissions
            'access sales',
            'create orders',
            'record deliveries',
            'record payments',
            'view sales reports',

            // Finance permissions
            'access finance',
            'view revenue reports',
            'view inventory reports',
            'view waste reports',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate([
                'name' => $permission,
            ]);
        }
    }
}
