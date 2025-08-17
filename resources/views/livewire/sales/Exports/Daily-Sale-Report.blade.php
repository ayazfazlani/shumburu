<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daily Sales Report - SHUMBRO PLASTIC FACTORY</title>
    <style>
        @page { margin: 18px; }
        body { 
            font-family: DejaVu Sans, Arial, sans-serif; 
            font-size: 11px; 
            color: #000; 
            margin: 0;
            padding: 0;
            background: white;
        }
        .container {
            padding: 24px;
            background: white;
            max-width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .logo-section {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .logo {
            width: 64px;
            height: 64px;
            background: #3b82f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        .title-section {
            font-weight: bold;
            font-size: 18px;
            line-height: 1.2;
        }
        .subtitle {
            font-size: 12px;
            color: #6b7280;
        }
        .meta-section {
            text-align: right;
            font-size: 12px;
        }
        .meta-section div {
            margin-bottom: 2px;
        }
        .font-semibold {
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
        }
        thead th {
            background: #e5e7eb;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-gray-500 {
            color: #6b7280;
        }
        .font-bold {
            font-weight: bold;
        }
        .bg-gray-200 {
            background: #e5e7eb;
        }
        .bg-gray-50 {
            background: #f9fafb;
        }
        .summary-section {
            margin-top: 16px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            font-size: 11px;
        }
        .summary-box {
            background: #f9fafb;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
        }
        .summary-title {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .summary-content {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
        }
        .signature-section {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }
        .signature-box {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .signature-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 120px;
            text-align: center;
        }
        .space-y-1 > * + * {
            margin-top: 4px;
        }
        .space-y-4 > * + * {
            margin-top: 16px;
        }
        .mb-1 { margin-bottom: 4px; }
        .mb-2 { margin-bottom: 8px; }
        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }
        .p-3 { padding: 12px; }
        .rounded { border-radius: 4px; }
        .border { border: 1px solid #d1d5db; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-section">
                <div class="logo">SPF</div>
                <div>
                    <div class="title-section">SHUMBRO PLASTIC FACTORY</div>
                    <div class="subtitle">Daily Sales Report</div>
                </div>
            </div>
            <div class="meta-section">
                <div><span class="font-semibold">Document No:</span> SPF-DSR-001</div>
                <div><span class="font-semibold">Period:</span> 
                    @if(isset($startDate) && isset($endDate))
                        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                    @else
                        {{ now()->format('d/m/Y') }}
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto mt-2">
            <table>
                <thead>
                    <tr class="bg-gray-200">
                        <th>Date</th>
                        <th>Customer Name</th>
                        <th>Item Description</th>
                        <th>Unit of Meas.</th>
                        <th>Qty</th>
                        <th>Net Weight</th>
                        <th>Sales Price</th>
                        <th>Total</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalNetWeight = 0;
                        $totalSales = 0;
                    @endphp
                    
                    @if(isset($groupedSales) && $groupedSales->count() > 0)
                        @foreach($groupedSales as $sale)
                            @php
                                $totalNetWeight += $sale['net_weight'] ?? 0;
                                $totalSales += $sale['total'] ?? 0;
                            @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($sale['date'])->format('d/m/Y') }}</td>
                                <td>{{ $sale['customer_name'] ?? 'Unknown' }}</td>
                                <td>{{ $sale['item_description'] ?? '' }}</td>
                                <td>{{ $sale['unit_measurement'] ?? 'meter' }}</td>
                                <td class="text-right">{{ number_format($sale['quantity'] ?? 0) }}</td>
                                <td class="text-right">{{ number_format($sale['net_weight'] ?? 0, 2) }}</td>
                                <td class="text-right">{{ number_format($sale['sales_price'] ?? 0, 2) }}</td>
                                <td class="text-right">{{ number_format($sale['total'] ?? 0, 2) }}</td>
                                <td>{{ $sale['remark'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center text-gray-500">
                                No sales data found for the selected period
                            </td>
                        </tr>
                    @endif
                    
                    <!-- Summary Row -->
                    @if(isset($groupedSales) && $groupedSales->count() > 0)
                        <tr class="font-bold bg-gray-200">
                            <td colspan="5" class="text-center">Total</td>
                            <td class="text-right">{{ number_format($totalNetWeight, 2) }}</td>
                            <td></td>
                            <td class="text-right">{{ number_format($totalSales, 2) }}</td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Summary Statistics -->
        @if(isset($summary))
            <div class="summary-section">
                <div class="summary-box">
                    <div class="summary-title">Summary</div>
                    <div class="summary-content">
                        <div class="summary-item">
                            <span>Total Orders:</span>
                            <span class="font-semibold">{{ $summary['total_orders'] ?? 0 }}</span>
                        </div>
                        <div class="summary-item">
                            <span>Total Deliveries:</span>
                            <span class="font-semibold">{{ $summary['total_deliveries'] ?? 0 }}</span>
                        </div>
                        <div class="summary-item">
                            <span>Total Net Weight:</span>
                            <span class="font-semibold">{{ number_format($summary['total_net_weight'] ?? 0, 2) }} kg</span>
                        </div>
                        <div class="summary-item">
                            <span>Total Sales:</span>
                            <span class="font-semibold">{{ number_format($summary['total_sales'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section flex">
            <div class="signature-box flex-1/3">
                <div class="signature-item">
                    <div class="mb-1">Prepared by: <span >_________________</span></div>
                    <div>Date: <span >{{ now()->format('d/m/Y') }}</span></div>
                    <div>Sign: <span >_________________</span></div>
                </div>
            </div>
            <div class="signature-box flex-1/3">
                <div class="signature-item">
                    <div class="mb-1">Checked by: <span >_________________</span></div>
                    <div>Date: <span >{{ now()->format('d/m/Y') }}</span></div>
                    <div>Sign: <span >_________________</span></div>
                </div>
            </div>
            <div class="signature-box flex-1/3">
                <div class="signature-item">
                    <div class="mb-1">Approved by: <span >_________________</span></div>
                    <div>Date: <span >{{ now()->format('d/m/Y') }}</span></div>
                    <div>Sign: <span >_________________</span></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>