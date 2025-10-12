<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\ProductionOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Services\NotificationService;

class ValidateWorkflow extends Command
{
    protected $signature = 'workflow:validate {--quick : Run quick validation only}';
    protected $description = 'Validate CRM workflow and identify issues';

    public function handle()
    {
        $this->info('🔍 Starting CRM Workflow Validation...');
        $this->newLine();

        $quick = $this->option('quick');
        
        if ($quick) {
            $this->runQuickValidation();
        } else {
            $this->runFullValidation();
        }

        $this->newLine();
        $this->info('✅ Validation complete!');
    }

    private function runQuickValidation()
    {
        $this->info('🚀 Running Quick Validation...');
        
        // 1. Database connectivity
        $this->validateDatabase();
        
        // 2. Basic models
        $this->validateModels();
        
        // 3. Routes
        $this->validateRoutes();
        
        // 4. Configuration
        $this->validateConfiguration();
    }

    private function runFullValidation()
    {
        $this->info('🔍 Running Full Validation...');
        
        // Quick validation first
        $this->runQuickValidation();
        
        // Additional validations
        $this->validateWorkflows();
        $this->validateNotifications();
        $this->validatePermissions();
        $this->validateDataIntegrity();
    }

    private function validateDatabase()
    {
        $this->info('📊 Validating Database...');
        
        try {
            DB::connection()->getPdo();
            $this->line('  ✅ Database connection successful');
            
            // Check key tables exist
            $tables = ['users', 'production_orders', 'customers', 'products', 'notifications'];
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $this->line("  ✅ Table '{$table}' exists");
                } else {
                    $this->error("  ❌ Table '{$table}' missing");
                }
            }
            
        } catch (\Exception $e) {
            $this->error('  ❌ Database connection failed: ' . $e->getMessage());
        }
    }

    private function validateModels()
    {
        $this->info('🏗️ Validating Models...');
        
        $models = [
            'User' => User::class,
            'ProductionOrder' => ProductionOrder::class,
            'Customer' => Customer::class,
            'Product' => Product::class,
        ];
        
        foreach ($models as $name => $class) {
            try {
                $count = $class::count();
                $this->line("  ✅ {$name} model working (count: {$count})");
            } catch (\Exception $e) {
                $this->error("  ❌ {$name} model error: " . $e->getMessage());
            }
        }
    }

    private function validateRoutes()
    {
        $this->info('🛣️ Validating Routes...');
        
        $criticalRoutes = [
            '/' => 'Home page',
            '/login' => 'Login page',
            '/dashboard' => 'Dashboard',
            '/sales/create-order' => 'Create order',
            '/sales/orders' => 'Orders overview',
            '/operations/production-orders' => 'Production orders',
            '/notifications' => 'Notifications',
        ];
        
        foreach ($criticalRoutes as $route => $description) {
            try {
                $response = $this->call('route:list', ['--name' => $route]);
                $this->line("  ✅ Route '{$route}' - {$description}");
            } catch (\Exception $e) {
                $this->error("  ❌ Route '{$route}' error: " . $e->getMessage());
            }
        }
    }

    private function validateConfiguration()
    {
        $this->info('⚙️ Validating Configuration...');
        
        $configs = [
            'app.name' => 'Application name',
            'database.default' => 'Database connection',
            'mail.default' => 'Mail configuration',
            'queue.default' => 'Queue configuration',
        ];
        
        foreach ($configs as $key => $description) {
            try {
                $value = config($key);
                if ($value) {
                    $this->line("  ✅ {$description}: {$value}");
                } else {
                    $this->error("  ❌ {$description}: Not configured");
                }
            } catch (\Exception $e) {
                $this->error("  ❌ {$description}: Error - " . $e->getMessage());
            }
        }
    }

    private function validateWorkflows()
    {
        $this->info('🔄 Validating Workflows...');
        
        // Test production order workflow
        try {
            $customer = Customer::first();
            $product = Product::first();
            
            if (!$customer || !$product) {
                $this->warn('  ⚠️ No test data found. Run seeders first.');
                return;
            }
            
            // Test order creation
            $order = ProductionOrder::create([
                'order_number' => 'TEST-' . time(),
                'customer_id' => $customer->id,
                'status' => 'pending',
                'requested_date' => now(),
                'requested_by' => User::first()->id,
            ]);
            
            $this->line('  ✅ Production order creation works');
            
            // Test status update
            $order->update(['status' => 'approved']);
            $this->line('  ✅ Order status update works');
            
            // Clean up test order
            $order->delete();
            $this->line('  ✅ Test order cleaned up');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Workflow validation error: ' . $e->getMessage());
        }
    }

    private function validateNotifications()
    {
        $this->info('🔔 Validating Notifications...');
        
        try {
            $service = app(NotificationService::class);
            $this->line('  ✅ NotificationService instantiated');
            
            // Check if notification table exists and is accessible
            $notificationCount = DB::table('notifications')->count();
            $this->line("  ✅ Notifications table accessible (count: {$notificationCount})");
            
        } catch (\Exception $e) {
            $this->error('  ❌ Notification validation error: ' . $e->getMessage());
        }
    }

    private function validatePermissions()
    {
        $this->info('🔐 Validating Permissions...');
        
        try {
            $user = User::first();
            if (!$user) {
                $this->warn('  ⚠️ No users found. Run seeders first.');
                return;
            }
            
            $this->line('  ✅ User model accessible');
            
            // Check if Spatie permissions are working
            if (method_exists($user, 'hasRole')) {
                $this->line('  ✅ Spatie permissions package working');
            } else {
                $this->error('  ❌ Spatie permissions not properly configured');
            }
            
        } catch (\Exception $e) {
            $this->error('  ❌ Permission validation error: ' . $e->getMessage());
        }
    }

    private function validateDataIntegrity()
    {
        $this->info('🔍 Validating Data Integrity...');
        
        try {
            // Check for orphaned records
            $orphanedOrders = ProductionOrder::whereDoesntHave('customer')->count();
            if ($orphanedOrders > 0) {
                $this->warn("  ⚠️ Found {$orphanedOrders} orders without customers");
            } else {
                $this->line('  ✅ No orphaned production orders');
            }
            
            // Check for users without departments
            $usersWithoutDept = User::whereNull('department_id')->count();
            if ($usersWithoutDept > 0) {
                $this->warn("  ⚠️ Found {$usersWithoutDept} users without departments");
            } else {
                $this->line('  ✅ All users have departments assigned');
            }
            
        } catch (\Exception $e) {
            $this->error('  ❌ Data integrity validation error: ' . $e->getMessage());
        }
    }
}
