<?php

namespace App\Livewire\Sales;

use Carbon\Carbon;
use App\Models\Payment;
use App\Models\Product;
use Livewire\Component;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\ProductionOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class MonthlySalesReport extends Component
{
    public $startDate;
    public $endDate;
    public $customer_id = '';
    public $product_id = '';
    public $month;

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
        // $this->month = Carbon::now()->month;
        // dd($this->month);
    }

    public function render()
    {
        $startOfMonth = Carbon::parse($this->startDate)->startOfDay();
        $endOfMonth = Carbon::parse($this->endDate)->endOfDay();

        // Get delivered orders for the selected month
        $deliveredOrders = ProductionOrder::with(['customer', 'items.product', 'payments'])
            ->where('status', 'delivered')
            ->whereBetween('delivery_date', [$startOfMonth, $endOfMonth])
            ->when($this->customer_id, function($query) {
                $query->where('customer_id', $this->customer_id);
            })
            ->when($this->product_id, function($query) {
                $query->whereHas('items', function($q) {
                    $q->where('product_id', $this->product_id);
                });
            })
            ->get();

        // Get deliveries for the selected month
        $deliveries = Delivery::with(['customer', 'product', 'productionOrder'])
            ->whereBetween('delivery_date', [$startOfMonth, $endOfMonth])
            ->when($this->customer_id, function($query) {
                $query->where('customer_id', $this->customer_id);
            })
            ->when($this->product_id, function($query) {
                $query->where('product_id', $this->product_id);
            })
            ->get();

        // Group sales data by date, customer, and product
        $groupedSales = $this->groupSalesData($deliveredOrders, $deliveries);

        // Calculate summary statistics
        $summary = $this->calculateSummary($deliveredOrders, $deliveries);

        // For filter dropdowns
        $customers = Customer::select('id','code', 'name')->orderBy('name')->get();
        $products = Product::select('id', 'name')->orderBy('name')->get();

        return view('livewire.sales.monthly-sales-report', [
            'groupedSales' => $groupedSales,
            'summary' => $summary,
            'customers' => $customers,
            'products' => $products,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    private function groupSalesData($orders, $deliveries)
    {
        $salesData = collect();

        // Process delivered orders
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $salesData->push([
                    'date' => $order->delivery_date,
                    'customer_name' => $order->customer->name ?? 'Unknown',
                    'item_description' => $this->getItemDescription($item->product),
                    'unit_measurement' => $this->getUnitMeasurement($item->product),
                    'quantity' => $item->quantity,
                    'net_weight' => $this->calculateNetWeight($item),
                    'sales_price' => $item->unit_price ?? 0,
                    'total' => $item->total_price ?? 0,
                    'remark' => '',
                ]);
            }
        }

        // Process direct deliveries
        foreach ($deliveries as $delivery) {
            $salesData->push([
                'date' => $delivery->delivery_date,
                'customer_name' => $delivery->customer->name ?? 'Unknown',
                'item_description' => $this->getItemDescription($delivery->product),
                'unit_measurement' => $this->getUnitMeasurement($delivery->product),
                'quantity' => $delivery->quantity ?? 0,
                'net_weight' => $this->calculateNetWeightFromDelivery($delivery),
                'sales_price' => $delivery->unit_price ?? 0,
                'total' => $delivery->total_amount ?? 0,
                'remark' => '',
            ]);
        }

        return $salesData->sortBy('date');
    }

    private function getItemDescription($product)
    {
        if (!$product) return 'Unknown';
        
        // Extract size and PN type from product name
        $name = $product->name ?? '';
        if (preg_match('/(\d+)mm.*?(PN\d+)/i', $name, $matches)) {
            return $matches[1] . 'mm' . $matches[2];
        }
        
        return $name;
    }

    private function getUnitMeasurement($product)
    {
        if (!$product) return 'meter';
        
        // Check if product is sold by roll or meter
        $name = strtolower($product->name ?? '');
        if (str_contains($name, 'roll')) {
            return 'roll';
        }
        
        return 'meter';
    }

    private function calculateNetWeight($item)
    {
        if (!$item->product) return 0;
        
        $weightPerUnit = $item->product->weight_per_meter ?? 0;
        $quantity = $item->quantity ?? 0;
        
        return $weightPerUnit * $quantity;
    }

    private function calculateNetWeightFromDelivery($delivery)
    {
        if (!$delivery->product) return 0;
        
        $weightPerUnit = $delivery->product->weight_per_meter ?? 0;
        $quantity = $delivery->quantity ?? 0;
        
        return $weightPerUnit * $quantity;
    }

    private function calculateSummary($orders, $deliveries)
    {
        $totalNetWeight = 0;
        $totalSales = 0;

        // Calculate from orders
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $totalNetWeight += $this->calculateNetWeight($item);
                $totalSales += $item->total_price ?? 0;
            }
        }

        // Calculate from deliveries
        foreach ($deliveries as $delivery) {
            $totalNetWeight += $this->calculateNetWeightFromDelivery($delivery);
            $totalSales += $delivery->total_amount ?? 0;
        }

        return [
            'total_net_weight' => $totalNetWeight,
            'total_sales' => $totalSales,
            'total_orders' => $orders->count(),
            'total_deliveries' => $deliveries->count(),
        ];
    }

    public function exportToPdf()
    {
        $startOfMonth = Carbon::parse($this->startDate)->startOfDay();
        $endOfMonth = Carbon::parse($this->endDate)->endOfDay();

        // Get delivered orders for the selected month
        $deliveredOrders = ProductionOrder::with(['customer', 'items.product', 'payments'])
            ->where('status', 'delivered')
            ->whereBetween('delivery_date', [$startOfMonth, $endOfMonth])
            ->when($this->customer_id, function($query) {
                $query->where('customer_id', $this->customer_id);
            })
            ->when($this->product_id, function($query) {
                $query->whereHas('items', function($q) {
                    $q->where('product_id', $this->product_id);
                });
            })
            ->get();

        // Get deliveries for the selected month
        $deliveries = Delivery::with(['customer', 'product', 'productionOrder'])
            ->whereBetween('delivery_date', [$startOfMonth, $endOfMonth])
            ->when($this->customer_id, function($query) {
                $query->where('customer_id', $this->customer_id);
            })
            ->when($this->product_id, function($query) {
                $query->where('product_id', $this->product_id);
            })
            ->get();

        // Group sales data by date, customer, and product
        $groupedSales = $this->groupSalesData($deliveredOrders, $deliveries);

        // Calculate summary statistics
        $summary = $this->calculateSummary($deliveredOrders, $deliveries);

        // For filter dropdowns
        $customers = Customer::select('id','code', 'name')->orderBy('name')->get();
        $products = Product::select('id', 'name')->orderBy('name')->get();
        $data = [
            'groupedSales' => $groupedSales,
            'summary' => $summary,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'customers' => $customers,
            'products' => $products,
        ];

        // dd($data);

        $pdf = Pdf::loadView('livewire.sales.Exports.Monthly-Sale-Report', $data)->setPaper('a4','portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'monthly-sales-report.pdf');
    }

    public function printReport()
    {
        $this->dispatch('print-report');
    }
}
