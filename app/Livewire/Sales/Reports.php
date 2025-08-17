<?php

namespace App\Livewire\Sales;

use App\Models\ProductionOrder;
use App\Models\Delivery;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Reports extends Component
{
    use WithPagination;

    public $reportType = 'daily';
    public $startDate;
    public $endDate;
    public $validationErrors = [];
    public $businessInsights = [];

    public function mount()
    {
        $this->startDate = now()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function render()
    {
        $user = Auth::user();

        // Get delivered orders only
        $deliveredOrders = ProductionOrder::with(['customer', 'items.product', 'payments'])
            ->where('status', 'delivered')
            ->when($this->reportType === 'daily', function ($query) {
                $query->whereDate('delivery_date', $this->startDate);
            })
            ->when($this->reportType === 'weekly', function ($query) {
                $query->whereBetween('delivery_date', [
                    Carbon::parse($this->startDate)->startOfWeek(),
                    Carbon::parse($this->endDate)->endOfWeek()
                ]);
            })
            ->when($this->reportType === 'monthly', function ($query) {
                $query->whereYear('delivery_date', Carbon::parse($this->startDate)->year)
                      ->whereMonth('delivery_date', Carbon::parse($this->startDate)->month);
            })
            ->latest()
            ->paginate(15);

        // Get deliveries for the period
        $deliveries = Delivery::with(['customer','product', 'productionOrder'])
            ->when($this->reportType === 'daily', function ($query) {
                $query->whereDate('delivery_date', $this->startDate);
            })
            ->when($this->reportType === 'weekly', function ($query) {
                $query->whereBetween('delivery_date', [
                    Carbon::parse($this->startDate)->startOfWeek(),
                    Carbon::parse($this->endDate)->endOfWeek()
                ]);
            })
            ->when($this->reportType === 'monthly', function ($query) {
                $query->whereYear('delivery_date', Carbon::parse($this->startDate)->year)
                      ->whereMonth('delivery_date', Carbon::parse($this->startDate)->month);
            })
            ->latest()
            ->paginate(15);

        // Get payments for the period
        $payments = Payment::with(['customer', 'productionOrder'])
            ->when($this->reportType === 'daily', function ($query) {
                $query->whereDate('payment_date', $this->startDate);
            })
            ->when($this->reportType === 'weekly', function ($query) {
                $query->whereBetween('payment_date', [
                    Carbon::parse($this->startDate)->startOfWeek(),
                    Carbon::parse($this->endDate)->endOfWeek()
                ]);
            })
            ->when($this->reportType === 'monthly', function ($query) {
                $query->whereYear('payment_date', Carbon::parse($this->startDate)->year)
                      ->whereMonth('payment_date', Carbon::parse($this->startDate)->month);
            })
            ->latest()
            ->paginate(15);

        // Calculate summary statistics
        $summary = $this->calculateSummary($deliveredOrders, $deliveries, $payments);

        // Run validations and business insights
        $this->runValidations($deliveredOrders, $deliveries, $payments);
        $this->generateBusinessInsights($deliveredOrders, $deliveries, $payments);

        return view('livewire.sales.reports', [
            'deliveredOrders' => $deliveredOrders,
            'deliveries' => $deliveries,
            'payments' => $payments,
            'summary' => $summary,
            'validationErrors' => $this->validationErrors,
            'businessInsights' => $this->businessInsights,
        ]);
    }

    private function calculateSummary($orders, $deliveries, $payments)
    {
        $totalOrders = $orders->total();
        $totalDeliveries = $deliveries->total();
        $totalPayments = $payments->total();

        $totalOrderValue = $orders->getCollection()->sum(function ($order) {
            return $order->items->sum('total_price');
        });

        $totalDeliveryValue = $deliveries->getCollection()->sum('total_amount');
        $totalPaymentValue = $payments->getCollection()->sum('amount');

        return [
            'total_orders' => $totalOrders,
            'total_deliveries' => $totalDeliveries,
            'total_payments' => $totalPayments,
            'total_order_value' => $totalOrderValue,
            'total_delivery_value' => $totalDeliveryValue,
            'total_payment_value' => $totalPaymentValue,
        ];
    }

    /**
     * Run comprehensive data validations
     */
    private function runValidations($orders, $deliveries, $payments)
    {
        $this->validationErrors = [];

        // 1. Data Integrity Validations
        $this->validateOrderItemRelationships($orders);
        $this->validatePaymentOrderRelationships($payments);
        $this->validateDeliveryOrderRelationships($deliveries);

        // 2. Financial Validations
        $this->validatePaymentAmounts($orders, $payments);
        $this->validateDeliveryAmounts($orders, $deliveries);

        // 3. Date Validations
        $this->validateDateConsistency($orders, $deliveries, $payments);

        // 4. Business Logic Validations
        $this->validateOrderCompletion($orders, $deliveries);
        $this->validatePaymentCollection($orders, $payments);
    }

    /**
     * Validate that all orders have items
     */
    private function validateOrderItemRelationships($orders)
    {
        foreach ($orders->getCollection() as $order) {
            if ($order->items->isEmpty()) {
                $this->validationErrors[] = [
                    'type' => 'error',
                    'message' => "Order {$order->order_number} has no items",
                    'order_id' => $order->id
                ];
            }
        }
    }

    /**
     * Validate payment-order relationships
     */
    private function validatePaymentOrderRelationships($payments)
    {
        foreach ($payments->getCollection() as $payment) {
            if (!$payment->productionOrder) {
                $this->validationErrors[] = [
                    'type' => 'error',
                    'message' => "Payment {$payment->id} has no associated order",
                    'payment_id' => $payment->id
                ];
            }
        }
    }

    /**
     * Validate delivery-order relationships
     */
    private function validateDeliveryOrderRelationships($deliveries)
    {
        foreach ($deliveries->getCollection() as $delivery) {
            if (!$delivery->productionOrder) {
                $this->validationErrors[] = [
                    'type' => 'error',
                    'message' => "Delivery {$delivery->id} has no associated order",
                    'delivery_id' => $delivery->id
                ];
            }
        }
    }

    /**
     * Validate payment amounts don't exceed order totals
     */
    private function validatePaymentAmounts($orders, $payments)
    {
        foreach ($orders->getCollection() as $order) {
            $orderTotal = $order->items->sum('total_price');
            $totalPaid = $order->payments->sum('amount');
            
            if ($totalPaid > $orderTotal) {
                $this->validationErrors[] = [
                    'type' => 'warning',
                    'message' => "Order {$order->order_number} has overpayment: Paid \${$totalPaid} vs Order \${$orderTotal}",
                    'order_id' => $order->id
                ];
            }
        }
    }

    /**
     * Validate delivery amounts match order amounts
     */
    private function validateDeliveryAmounts($orders, $deliveries)
    {
        foreach ($orders->getCollection() as $order) {
            $orderTotal = $order->items->sum('total_price');
            $deliveryTotal = $deliveries->getCollection()
                ->where('production_order_id', $order->id)
                ->sum('total_amount');
            
            if (abs($deliveryTotal - $orderTotal) > 0.01) {
                $this->validationErrors[] = [
                    'type' => 'warning',
                    'message' => "Order {$order->order_number} delivery amount mismatch: Order \${$orderTotal} vs Delivery \${$deliveryTotal}",
                    'order_id' => $order->id
                ];
            }
        }
    }

    /**
     * Validate date consistency
     */
    private function validateDateConsistency($orders, $deliveries, $payments)
    {
        foreach ($orders->getCollection() as $order) {
            if ($order->delivery_date && $order->requested_date) {
                if ($order->delivery_date < $order->requested_date) {
                    $this->validationErrors[] = [
                        'type' => 'error',
                        'message' => "Order {$order->order_number} delivery date is before request date",
                        'order_id' => $order->id
                    ];
                }
            }
        }
    }

    /**
     * Validate order completion
     */
    private function validateOrderCompletion($orders, $deliveries)
    {
        foreach ($orders->getCollection() as $order) {
            $orderItems = $order->items->sum('quantity');
            $deliveredItems = $deliveries->getCollection()
                ->where('production_order_id', $order->id)
                ->sum('quantity');
            
            if ($deliveredItems < $orderItems) {
                $this->validationErrors[] = [
                    'type' => 'warning',
                    'message' => "Order {$order->order_number} incomplete delivery: Ordered {$orderItems} vs Delivered {$deliveredItems}",
                    'order_id' => $order->id
                ];
            }
        }
    }

    /**
     * Validate payment collection
     */
    private function validatePaymentCollection($orders, $payments)
    {
        foreach ($orders->getCollection() as $order) {
            $orderTotal = $order->items->sum('total_price');
            $totalPaid = $order->payments->sum('amount');
            
            if ($totalPaid < $orderTotal) {
                $outstandingBalance = $orderTotal - $totalPaid;
                $this->validationErrors[] = [
                    'type' => 'info',
                    'message' => "Order {$order->order_number} has outstanding balance: \${$outstandingBalance}",
                    'order_id' => $order->id
                ];
            }
        }
    }

    /**
     * Generate business insights
     */
    private function generateBusinessInsights($orders, $deliveries, $payments)
    {
        $this->businessInsights = [];

        // Payment collection rate
        $totalOrderValue = $orders->getCollection()->sum(function ($order) {
            return $order->items->sum('total_price');
        });
        $totalPaid = $payments->getCollection()->sum('amount');
        $collectionRate = $totalOrderValue > 0 ? ($totalPaid / $totalOrderValue) * 100 : 0;

        $this->businessInsights[] = [
            'type' => 'success',
            'title' => 'Payment Collection Rate',
            'value' => number_format($collectionRate, 1) . '%',
            'description' => "Collected \${$totalPaid} out of \${$totalOrderValue} total orders"
        ];

        // Average order value
        $avgOrderValue = $orders->getCollection()->count() > 0 
            ? $orders->getCollection()->avg(function ($order) {
                return $order->items->sum('total_price');
            }) 
            : 0;

        $this->businessInsights[] = [
            'type' => 'info',
            'title' => 'Average Order Value',
            'value' => '$' . number_format($avgOrderValue, 2),
            'description' => 'Average value per delivered order'
        ];

        // Outstanding payments
        $outstandingAmount = $totalOrderValue - $totalPaid;
        if ($outstandingAmount > 0) {
            $this->businessInsights[] = [
                'type' => 'warning',
                'title' => 'Outstanding Payments',
                'value' => '$' . number_format($outstandingAmount, 2),
                'description' => 'Total amount yet to be collected'
            ];
        }
    }

    public function updatedReportType()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }
}
