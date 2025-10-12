<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\Department;
use App\Models\OrderItem;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class WorkflowTestSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🧪 Creating test data for workflow validation...');

        // Create departments
        $this->createDepartments();
        
        // Create roles and permissions
        $this->createRolesAndPermissions();
        
        // Create test users
        $this->createTestUsers();
        
        // Create test customers
        $this->createTestCustomers();
        
        // Create test products
        $this->createTestProducts();
        
        // Create test production orders
        $this->createTestOrders();

        $this->command->info('✅ Test data created successfully!');
        $this->command->info('You can now test your workflows with real data.');
    }

    private function createDepartments()
    {
        $departments = [
            ['name' => 'Sales', 'description' => 'Sales Department'],
            ['name' => 'Operations', 'description' => 'Operations Department'],
            ['name' => 'Finance', 'description' => 'Finance Department'],
            ['name' => 'Warehouse', 'description' => 'Warehouse Department'],
            ['name' => 'Administration', 'description' => 'Administration Department'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['name' => $dept['name']],
                $dept
            );
        }

        $this->command->info('  ✅ Departments created');
    }

    private function createRolesAndPermissions()
    {
        // Create roles
        $roles = [
            'Super Admin',
            'Admin',
            'Sales Manager',
            'Operations Manager',
            'Finance Manager',
            'Warehouse Manager',
            'Sales Representative',
            'Production Worker',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Create permissions
        $permissions = [
            'access sales',
            'access operations',
            'access finance',
            'access warehouse',
            'access admin',
            'create orders',
            'edit orders',
            'delete orders',
            'view reports',
            'manage users',
            'manage products',
            'manage customers',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Assign permissions to roles
        $superAdmin = Role::findByName('Super Admin');
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::findByName('Admin');
        $admin->givePermissionTo(['access sales', 'access operations', 'access finance', 'access warehouse', 'view reports']);

        $salesManager = Role::findByName('Sales Manager');
        $salesManager->givePermissionTo(['access sales', 'create orders', 'edit orders', 'view reports', 'manage customers']);

        $operationsManager = Role::findByName('Operations Manager');
        $operationsManager->givePermissionTo(['access operations', 'edit orders', 'view reports']);

        $this->command->info('  ✅ Roles and permissions created');
    }

    private function createTestUsers()
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'department_id' => Department::where('name', 'Administration')->first()->id,
                'role' => 'Super Admin',
            ],
            [
                'name' => 'Sales Manager',
                'email' => 'sales@test.com',
                'password' => Hash::make('password'),
                'department_id' => Department::where('name', 'Sales')->first()->id,
                'role' => 'Sales Manager',
            ],
            [
                'name' => 'Operations Manager',
                'email' => 'operations@test.com',
                'password' => Hash::make('password'),
                'department_id' => Department::where('name', 'Operations')->first()->id,
                'role' => 'Operations Manager',
            ],
            [
                'name' => 'Finance Manager',
                'email' => 'finance@test.com',
                'password' => Hash::make('password'),
                'department_id' => Department::where('name', 'Finance')->first()->id,
                'role' => 'Finance Manager',
            ],
            [
                'name' => 'Warehouse Manager',
                'email' => 'warehouse@test.com',
                'password' => Hash::make('password'),
                'department_id' => Department::where('name', 'Warehouse')->first()->id,
                'role' => 'Warehouse Manager',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            $user->assignRole($role);
        }

        $this->command->info('  ✅ Test users created');
        $this->command->info('  📧 Login credentials: email@test.com / password');
    }

    private function createTestCustomers()
    {
        $customers = [
            [
                'name' => 'ABC Manufacturing Ltd',
                'email' => 'orders@abcmanufacturing.com',
                'phone' => '+1-555-0123',
                'address' => '123 Industrial Blvd, Manufacturing City, MC 12345',
                'contact_person' => 'John Smith',
                'is_active' => true,
            ],
            [
                'name' => 'XYZ Industries Inc',
                'email' => 'procurement@xyzindustries.com',
                'phone' => '+1-555-0456',
                'address' => '456 Production Ave, Industrial Park, IP 67890',
                'contact_person' => 'Sarah Johnson',
                'is_active' => true,
            ],
            [
                'name' => 'Global Solutions Corp',
                'email' => 'orders@globalsolutions.com',
                'phone' => '+1-555-0789',
                'address' => '789 Business Plaza, Corporate District, CD 11111',
                'contact_person' => 'Mike Davis',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customerData) {
            Customer::firstOrCreate(
                ['email' => $customerData['email']],
                $customerData
            );
        }

        $this->command->info('  ✅ Test customers created');
    }

    private function createTestProducts()
    {
        $products = [
            [
                'name' => 'Steel Pipe 6 inch',
                'description' => 'High-quality steel pipe, 6 inch diameter',
                'unit' => 'meter',
                'unit_price' => 25.50,
                'category' => 'Steel Products',
                'is_active' => true,
            ],
            [
                'name' => 'Aluminum Sheet 2mm',
                'description' => 'Premium aluminum sheet, 2mm thickness',
                'unit' => 'square meter',
                'unit_price' => 15.75,
                'category' => 'Aluminum Products',
                'is_active' => true,
            ],
            [
                'name' => 'Copper Wire 12 AWG',
                'description' => 'Electrical copper wire, 12 AWG gauge',
                'unit' => 'meter',
                'unit_price' => 8.25,
                'category' => 'Electrical Products',
                'is_active' => true,
            ],
            [
                'name' => 'Plastic Container 50L',
                'description' => 'Heavy-duty plastic container, 50 liter capacity',
                'unit' => 'piece',
                'unit_price' => 45.00,
                'category' => 'Plastic Products',
                'is_active' => true,
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                ['name' => $productData['name']],
                $productData
            );
        }

        $this->command->info('  ✅ Test products created');
    }

    private function createTestOrders()
    {
        $customers = Customer::all();
        $products = Product::all();
        $salesUser = User::where('email', 'sales@test.com')->first();

        if (!$customers->count() || !$products->count() || !$salesUser) {
            $this->command->warn('  ⚠️ Cannot create orders - missing customers, products, or sales user');
            return;
        }

        $orders = [
            [
                'order_number' => 'PO-2024-001',
                'customer_id' => $customers->random()->id,
                'status' => 'pending',
                'requested_date' => now()->subDays(5),
                'requested_by' => $salesUser->id,
                'notes' => 'Urgent order for Q1 production',
            ],
            [
                'order_number' => 'PO-2024-002',
                'customer_id' => $customers->random()->id,
                'status' => 'approved',
                'requested_date' => now()->subDays(3),
                'requested_by' => $salesUser->id,
                'approved_by' => User::where('email', 'admin@test.com')->first()->id,
                'notes' => 'Standard production order',
            ],
            [
                'order_number' => 'PO-2024-003',
                'customer_id' => $customers->random()->id,
                'status' => 'in_production',
                'requested_date' => now()->subDays(1),
                'production_start_date' => now()->subDay(),
                'requested_by' => $salesUser->id,
                'approved_by' => User::where('email', 'admin@test.com')->first()->id,
                'notes' => 'Currently in production',
            ],
        ];

        foreach ($orders as $orderData) {
            $order = ProductionOrder::firstOrCreate(
                ['order_number' => $orderData['order_number']],
                $orderData
            );

            // Add order items
            $randomProducts = $products->random(rand(1, 3));
            foreach ($randomProducts as $product) {
                $quantity = rand(10, 100);
                $unitPrice = $product->unit_price;
                $totalPrice = $quantity * $unitPrice;

                OrderItem::firstOrCreate([
                    'production_order_id' => $order->id,
                    'product_id' => $product->id,
                ], [
                    'quantity' => $quantity,
                    'unit' => $product->unit,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }
        }

        $this->command->info('  ✅ Test production orders created');
    }
}
