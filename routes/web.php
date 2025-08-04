<?php

use App\Livewire\Home;
use Livewire\Livewire;
use App\Livewire\Dashboard;
use App\Livewire\Sales\Reports;
use App\Livewire\Sales\Payments;
use App\Livewire\Settings\Locale;
use App\Livewire\Sales\Deliveries;
use App\Livewire\Sales\OrderItems;
use App\Livewire\Settings\Profile;
use App\Livewire\Sales\CreateOrder;
use App\Livewire\Settings\Password;
use App\Livewire\Warehouse\StockIn;
use App\Livewire\Admin\ProductsCrud;
use App\Livewire\Warehouse\StockOut;
use App\Livewire\Finance\WasteReport;
use App\Livewire\Settings\Appearance;
use App\Livewire\TestReportComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\Warehouse\Production;
use App\Livewire\Finance\RevenueReport;
use App\Livewire\Admin\RawMaterialsCrud;
use App\Livewire\Warehouse\FinishedGoods;
use App\Livewire\Warehouse\ScrapWasteCrud;
use App\Livewire\Operations\DowntimeRecord;
use App\Livewire\Sales\Index as SalesIndex;
use App\Livewire\Warehouse\ScrapWasteRecord;
use App\Livewire\Operations\ProductionReport;
use App\Livewire\Warehouse\ProductionMachine;
use App\Livewire\Finance\Index as FinanceIndex;
use App\Livewire\Reports\WeeklyProductionReport;
use App\Http\Controllers\ImpersonationController;
use App\Livewire\Reports\MonthlyProductionReport;
use App\Livewire\Warehouse\Index as WarehouseIndex;
use App\Livewire\Warehouse\MaterialStockOutLineCrud;
use App\Livewire\Operations\Index as OperationsIndex;
use App\Livewire\Reports\RawMaterialStockBalanceReport;
use App\Livewire\Warehouse\FinishedGoodMaterialStockOutLineCrud;
use App\Livewire\Operations\WasteReport as OperationsWasteReport;

Route::get('/', \App\Livewire\Home::class)->name('home');

Route::get('/dashboard', \App\Livewire\Dashboard::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function (): void {

    // Impersonations
    Route::post('/impersonate/{user}', [ImpersonationController::class, 'store'])->name('impersonate.store');
    Route::delete('/impersonate/stop', [ImpersonationController::class, 'destroy'])->name('impersonate.destroy');

    // Settings
    Route::redirect('settings', 'settings/profile');
    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
    Route::get('settings/locale', \App\Livewire\Settings\Locale::class)->name('settings.locale');

    // Warehouse Management
    Route::prefix('warehouse')->as('warehouse.')->group(function (): void {
        Route::get('/', \App\Livewire\Warehouse\Index::class)->name('index');
        Route::get('/products', ProductsCrud::class)->name('products');
        Route::get('/Lines', ProductionMachine::class)->name('lines');
        Route::get('/stock-in', \App\Livewire\Warehouse\StockIn::class)->name('stock-in');
        Route::get('/production', Production::class)->name('production.create');
        Route::get('/production/index', \App\Livewire\Warehouse\ProductionCrud::class)->name('production.index');
        Route::get('/production/{id}', \App\Livewire\Warehouse\ViewProduction::class)->name('production.view');
        Route::get('/production/{id}/edit', \App\Livewire\Warehouse\EditProduction::class)->name('production.edit');
        Route::get('/stock-out', \App\Livewire\Warehouse\StockOut::class)->name('stock-out');
        Route::get('/finished-goods', \App\Livewire\Warehouse\FinishedGoods::class)->name('finished-goods');
        Route::get('/scrap-waste', ScrapWasteRecord::class)->name('scrap-wastes');
        Route::get('/finished-good-material-stock-out', FinishedGoodMaterialStockOutLineCrud::class)->name('finished-good-material');
        // Route::get('/scrap-wastes', ScrapWasteCrud::class)->name('scrap-wastes');
        Route::get('/material-stock-out-lines', MaterialStockOutLineCrud::class)->name('material-stock-out-lines');
    });

    // Operations Management
    Route::prefix('operations')->as('operations.')->group(function (): void {
        Route::get('/', \App\Livewire\Operations\Index::class)->name('index');
        Route::get('/downtime-record', \App\Livewire\Operations\DowntimeRecord::class)->name('downtime-record');
        Route::get('/waste-report', \App\Livewire\Operations\WasteReport::class)->name('waste-report');
        Route::get('/production-report', \App\Livewire\Operations\ProductionReport::class)->name('production-report');
    });

    // Sales Management
    Route::prefix('sales')->as('sales.')->group(function (): void {
        Route::get('/', \App\Livewire\Sales\Index::class)->name('index');
        Route::get('/orders', \App\Livewire\Sales\OrdersOverview::class)->name('orders');
        Route::get('/create-order', \App\Livewire\Sales\CreateOrder::class)->name('create-order');
        Route::get('/deliveries', \App\Livewire\Sales\Deliveries::class)->name('deliveries');
        Route::get('/payments', \App\Livewire\Sales\Payments::class)->name('payments');
        Route::get('/reports', \App\Livewire\Sales\Reports::class)->name('reports');
    });

    // Finance Management
    Route::prefix('finance')->as('finance.')->group(function (): void {
        Route::get('/', \App\Livewire\Finance\Index::class)->name('index');
        Route::get('/revenue-report', \App\Livewire\Finance\RevenueReport::class)->name('revenue-report');
        Route::get('/inventory-report', \App\Livewire\Finance\InventoryReport::class)->name('inventory-report');
        // Route::get('/waste-report', \App\Livewire\Finance\WasteReport::class)->name('waste-report');
    });

    // Admin
    Route::prefix('admin')->as('admin.')->group(function (): void {
        Route::get('/', \App\Livewire\Admin\Index::class)->middleware(['auth', 'verified'])->name('index');
        // Route::get('/users', \App\Livewire\Admin\Users::class)->name('users.index');
        // Route::get('/users/create', \App\Livewire\Admin\Users\CreateUser::class)->name('users.create');
        // Route::get('/users/{user}', \App\Livewire\Admin\Users\ViewUser::class)->name('users.show');
        // Route::get('/users/{user}/edit', \App\Livewire\Admin\Users\EditUser::class)->name('users.edit');
        // Route::get('/roles', \App\Livewire\Admin\Roles::class)->name('roles.index');
        // Route::get('/roles/create', \App\Livewire\Admin\Roles\CreateRole::class)->name('roles.create');
        // Route::get('/roles/{role}/edit', \App\Livewire\Admin\Roles\EditRole::class)->name('roles.edit');
        // Route::get('/permissions', \App\Livewire\Admin\Permissions::class)->name('permissions.index');
        // Route::get('/permissions/create', \App\Livewire\Admin\Permissions\CreatePermission::class)->name('permissions.create');
        // Route::get('/permissions/{permission}/edit', \App\Livewire\Admin\Permissions\EditPermission::class)->name('permissions.edit');

        // New single-component CRUDs
        Route::get('/roles-crud', \App\Livewire\Admin\RolesCrud::class)->name('roles-crud');
        Route::get('/permissions-crud', \App\Livewire\Admin\PermissionsCrud::class)->name('permissions-crud');
        Route::get('/users-crud', \App\Livewire\Admin\UsersCrud::class)->name('users-crud');
        Route::get('/customers-crud', \App\Livewire\Admin\CustomersCrud::class)->name('customers-crud');

        Route::get('/raw-materials', RawMaterialsCrud::class)->name('admin.raw-materials.index');
    });
});



Route::get('test',TestReportComponent::class);
Route::get('/reports/weekly', WeeklyProductionReport::class)->name('reports.weekly');
Route::get('/reports/monthly', MonthlyProductionReport::class)->name('reports.monthly');
Route::get('/reports/raw-material-stock-balance', RawMaterialStockBalanceReport::class)->name('reports.raw-material-stock-balance');


// order item route with production order id
Route::get('/order-items/{productionOrderId}', OrderItems::class)->name('order-items');
require __DIR__ . '/auth.php';
