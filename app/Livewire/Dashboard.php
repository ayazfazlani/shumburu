<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Delivery;
use App\Models\FinishedGood;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductionOrder;
use App\Models\RawMaterial;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    #[Layout('components.layouts.app')]
    public function render(): View
    {
        $now = now();
        $today = $now->copy()->startOfDay();
        $thisWeek = $now->copy()->startOfWeek();
        $thisMonth = $now->copy()->startOfMonth();
        $lastMonth = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();
        
        // Revenue Metrics - All Dynamic Calculations
        $totalRevenue = Payment::sum('amount') ?? 0;
        $todayRevenue = Payment::whereDate('payment_date', $today)->sum('amount') ?? 0;
        $weeklyRevenue = Payment::where('payment_date', '>=', $thisWeek)->sum('amount') ?? 0;
        $monthlyRevenue = Payment::where('payment_date', '>=', $thisMonth)->sum('amount') ?? 0;
        $lastMonthRevenue = Payment::whereBetween('payment_date', [$lastMonth, $lastMonthEnd])->sum('amount') ?? 0;
        
        // Revenue Growth Calculations
        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : ($monthlyRevenue > 0 ? 100 : 0);
        $weeklyGrowth = $lastMonthRevenue > 0 
            ? (($weeklyRevenue - ($lastMonthRevenue / 4)) / ($lastMonthRevenue / 4)) * 100 
            : ($weeklyRevenue > 0 ? 100 : 0);
        
        // Order Metrics - Dynamic
        $totalOrders = ProductionOrder::count();
        $todayOrders = ProductionOrder::whereDate('created_at', $today)->count();
        $weeklyOrders = ProductionOrder::where('created_at', '>=', $thisWeek)->count();
        $monthlyOrders = ProductionOrder::where('created_at', '>=', $thisMonth)->count();
        $activeOrders = ProductionOrder::whereIn('status', ['pending', 'approved', 'in_production'])->count();
        
        // Calculate Average Order Value from actual order items
        $totalOrderValue = OrderItem::sum('total_price') ?? 0;
        $averageOrderValue = $totalOrders > 0 ? ($totalOrderValue / $totalOrders) : 0;
        
        // Customer & Product Metrics
        $totalCustomers = Customer::where('is_active', true)->count();
        $newCustomersThisMonth = Customer::where('is_active', true)
            ->where('created_at', '>=', $thisMonth)
            ->count();
        $totalProducts = Product::where('is_active', true)->count();
        
        // Inventory Metrics - Real Calculations
        $totalRawMaterials = RawMaterial::where('is_active', true)->sum('quantity') ?? 0;
        $rawMaterialsCount = RawMaterial::where('is_active', true)->count();
        $totalFinishedGoods = FinishedGood::sum('quantity') ?? 0;
        
        // Calculate inventory value (if we had unit prices, for now just quantity)
        $lowStockThreshold = 100;
        $lowStockMaterials = RawMaterial::where('is_active', true)
            ->where('quantity', '<', $lowStockThreshold)
            ->count();
        
        // Delivery Metrics
        $totalDeliveries = Delivery::count();
        $todayDeliveries = Delivery::whereDate('delivery_date', $today)->count();
        $monthlyDeliveries = Delivery::where('delivery_date', '>=', $thisMonth)->count();
        $totalDeliveryValue = Delivery::sum('total_amount') ?? 0;
        
        // Revenue Trends (Last 30 days) - Dynamic
        $revenueTrend = Payment::select(
            DB::raw('DATE(payment_date) as date'),
            DB::raw('SUM(amount) as total')
        )
            ->where('payment_date', '>=', $now->copy()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Weekly Revenue Trend (Last 7 weeks)
        $weeklyTrend = Payment::select(
            DB::raw('YEARWEEK(payment_date) as week'),
            DB::raw('SUM(amount) as total')
        )
            ->where('payment_date', '>=', $now->copy()->subWeeks(7))
            ->groupBy('week')
            ->orderBy('week')
            ->get();
        
        // Production Orders by Status - Dynamic
        $ordersByStatus = ProductionOrder::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Orders Status Breakdown
        $pendingOrders = ProductionOrder::where('status', 'pending')->count();
        $approvedOrders = ProductionOrder::where('status', 'approved')->count();
        $inProductionOrders = ProductionOrder::where('status', 'in_production')->count();
        $completedOrders = ProductionOrder::where('status', 'completed')->count();
        $deliveredOrders = ProductionOrder::where('status', 'delivered')->count();
        
        // Recent Activities - Real Data
        $recentOrders = ProductionOrder::with(['customer', 'orderItems'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentPayments = Payment::with(['customer', 'productionOrder'])
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();
        
        $recentDeliveries = Delivery::with(['customer', 'product', 'productionOrder'])
            ->orderBy('delivery_date', 'desc')
            ->limit(5)
            ->get();
        
        // Top Customers by Revenue - Real Calculation
        $topCustomers = Customer::withSum('payments', 'amount')
            ->having('payments_sum_amount', '>', 0)
            ->orderBy('payments_sum_amount', 'desc')
            ->limit(5)
            ->get();
        
        // Top Products by Sales - Real Calculation
        $topProducts = Product::withSum('deliveries', 'total_amount')
            ->having('deliveries_sum_total_amount', '>', 0)
            ->orderBy('deliveries_sum_total_amount', 'desc')
            ->limit(5)
            ->get();
        
        // Pending Payments (Orders with no payments or partial payments)
        $ordersWithPayments = ProductionOrder::withSum('payments', 'amount')
            ->with('orderItems')
            ->get();
        
        $pendingPaymentsCount = $ordersWithPayments->filter(function($order) {
            $orderTotal = $order->total_price ?? 0;
            $paidAmount = $order->payments_sum_amount ?? 0;
            return $paidAmount < $orderTotal;
        })->count();
        
        $pendingPaymentsAmount = $ordersWithPayments->sum(function($order) {
            $orderTotal = $order->total_price ?? 0;
            $paidAmount = $order->payments_sum_amount ?? 0;
            return max(0, $orderTotal - $paidAmount);
        });
        
        return view('livewire.dashboard', [
            // Revenue Metrics
            'totalRevenue' => $totalRevenue,
            'todayRevenue' => $todayRevenue,
            'weeklyRevenue' => $weeklyRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'lastMonthRevenue' => $lastMonthRevenue,
            'revenueGrowth' => $revenueGrowth,
            'weeklyGrowth' => $weeklyGrowth,
            
            // Order Metrics
            'totalOrders' => $totalOrders,
            'todayOrders' => $todayOrders,
            'weeklyOrders' => $weeklyOrders,
            'monthlyOrders' => $monthlyOrders,
            'activeOrders' => $activeOrders,
            'averageOrderValue' => $averageOrderValue,
            
            // Customer & Product
            'totalCustomers' => $totalCustomers,
            'newCustomersThisMonth' => $newCustomersThisMonth,
            'totalProducts' => $totalProducts,
            
            // Inventory
            'totalRawMaterials' => $totalRawMaterials,
            'rawMaterialsCount' => $rawMaterialsCount,
            'totalFinishedGoods' => $totalFinishedGoods,
            'lowStockMaterials' => $lowStockMaterials,
            
            // Delivery Metrics
            'totalDeliveries' => $totalDeliveries,
            'todayDeliveries' => $todayDeliveries,
            'monthlyDeliveries' => $monthlyDeliveries,
            'totalDeliveryValue' => $totalDeliveryValue,
            
            // Trends
            'revenueTrend' => $revenueTrend,
            'weeklyTrend' => $weeklyTrend,
            'ordersByStatus' => $ordersByStatus,
            
            // Recent Activities
            'recentOrders' => $recentOrders,
            'recentPayments' => $recentPayments,
            'recentDeliveries' => $recentDeliveries,
            
            // Top Performers
            'topCustomers' => $topCustomers,
            'topProducts' => $topProducts,
            
            // Status Breakdown
            'pendingOrders' => $pendingOrders,
            'approvedOrders' => $approvedOrders,
            'inProductionOrders' => $inProductionOrders,
            'completedOrders' => $completedOrders,
            'deliveredOrders' => $deliveredOrders,
            
            // Financial
            'pendingPaymentsCount' => $pendingPaymentsCount,
            'pendingPaymentsAmount' => $pendingPaymentsAmount,
        ]);
    }
}
