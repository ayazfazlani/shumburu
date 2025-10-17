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
            'Selse',

            // User impersonation
            'can manage customers',

            // User management
            'can see customer name',
            'can manage quality control',
            'can manage fya-warehouse',
            'can view orders',

            // Order management
            'can record order payments',
            'can deliver orders',
            'can create orders',
            'can see sales dashboard',

            // Production Report management
            'can see monthly production report',
            'can see weekly production report',
            'can see daily production report',
            'can see scrape or waste report',
            'can see weekly sales reports',
            'can see daily sales reports',
            'can see raw meterial balance',
            'can see monthly sales reports',
             'can add downtime record',
            // operations 
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
            'can see material stock out lines',
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


            'create orders'


        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate([
                'name' => $permission,
            ]);
        }
    }
}
