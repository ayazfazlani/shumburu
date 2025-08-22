<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Daily Production Report - SHUMBRO PLASTIC FACTORY</title>
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
        .filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            margin-bottom: 10px;
            padding: 6px 10px;
            background: #f1f5f9;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
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
        .comments {
            margin-top: 16px;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
        }
        .comments-title {
            font-weight: 600;
            margin-bottom: 8px;
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
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .total-row {
            background: #dbeafe;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #93c5fd;
            border-bottom: 2px solid #93c5fd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-section">
                <div class="logo">SPF</div>
                <div>
                    <div class="title-section">SHUMBRO PLASTIC FACTORY</div>
                    <div class="subtitle">Daily Production Report</div>
                </div>
            </div>
            <div class="meta-section">
                <div><span class="font-semibold">Document No:</span> SPF/PR/QC:002</div>
                <div><span class="font-semibold">Date:</span> {{ $date ?? now()->format('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <div>
                <span class="font-semibold">Production Details: </span>
                {{ $shift ?? 'All Shifts' }} | 
                {{ $product_id ?? 'All Products' }}
            </div>
            <div><span class="font-semibold">Page:</span> 1 of 1</div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto mt-2">
            <table>
                <thead>
                    <tr class="bg-gray-200">
                        <th>R-M Name</th>
                        <th>Qty Consumed</th>
                        <th>Shift</th>
                        <th>Product</th>
                        <th>Size</th>
                        @foreach($lengths as $length)
                            <th class="text-center">{{ $length }}m</th>
                        @endforeach
                        <th>Total Weight</th>
                        <th>Avg Weight</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totals = array_fill_keys($lengths->toArray(), 0);
                        $grandTotalWeight = 0;
                    @endphp
                    @foreach($grouped as $rawMaterial => $byShift)
                        @foreach($byShift as $shift => $byProduct)
                            @foreach($byProduct as $productName => $bySize)
                                @foreach($bySize as $size => $records)
                                    <tr>
                                        <td>{{ $rawMaterial }}</td>
                                        <td class="text-right">{{ $records->first()->materialStockOutLines->first()->quantity_consumed ?? '' }}</td>
                                        <td>{{ $shift }}</td>
                                        <td>{{ $productName }}</td>
                                        <td>{{ $size }}</td>
                                        @foreach($lengths as $length)
                                            @php
                                                $qty = $records->where('length_m', $length)->sum('quantity');
                                                $totals[$length] += $qty;
                                            @endphp
                                            <td class="text-right">{{ $qty > 0 ? $qty : '' }}</td>
                                        @endforeach
                                        @php
                                            $totalWeight = $records->sum(function($rec) {
                                                return $rec->quantity * $rec->length_m * ($rec->product->weight_per_meter ?? 0);
                                            });
                                            $grandTotalWeight += $totalWeight;
                                        @endphp
                                        <td class="text-right">{{ number_format($totalWeight, 2) }}</td>
                                        <td class="text-right">
                                            {{ $records->count() > 0 ? number_format($totalWeight / $records->count(), 2) : '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                    <tr class="total-row">
                        <td>Total</td>
                        <td colspan="4"></td>
                        @foreach($lengths as $length)
                            <td class="text-right">{{ $totals[$length] }}</td>
                        @endforeach
                        <td class="text-right">{{ number_format($grandTotalWeight, 2) }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Quality Comments -->
        <div class="comments">
            <div class="comments-title">Quality Comments</div>
            <div>
                {{ $qualityReport->quality_comment ?? 'Today all products were produced according to the standards...' }}
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-item">
                    <div class="mb-1">Prepared by: <span >_________________</span></div>
                    <div>Date: <span class="underline">{{ now()->format('d/m/Y') }}</span></div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-item">
                    <div class="mb-1">Checked by: <span >_________________</span></div>
                    <div>Date: <span class="underline">{{ now()->format('d/m/Y') }}</span></div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-item">
                    <div class="mb-1">Approved by: <span >_________________</span></div>
                    <div>Date: <span class="underline">{{ now()->format('d/m/Y') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>