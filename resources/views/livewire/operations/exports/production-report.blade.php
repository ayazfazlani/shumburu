<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
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
        .date-info {
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
            text-decoration: underline;
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
            background: #e5e7eb;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #9ca3af;
            border-bottom: 2px solid #9ca3af;
        }
        .list-disc {
            list-style-type: disc;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">SPF</div>
                <div>
                    <div class="title-section">SHUMBRO PLASTIC FACTORY</div>
                    <div class="subtitle">Quality Control Daily Production of Pipe & Raw Material Reports</div>
                </div>
            </div>
            <div class="meta-section">
                <div><span class="font-semibold">Document no</span> S/P/E/PR QC:001</div>
                <div><span class="font-semibold">Date:</span> {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</div>
            </div>
        </div>

        <!-- Date Information -->
        <div class="date-info">
            <span class="font-semibold">Report Date: </span>
            {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}
        </div>

        <!-- Table -->
        <div class="overflow-x-auto mt-2">
            <table>
                <thead>
                    <tr class="bg-gray-200">
                        <th>Raw Material</th>
                        <th>Weight (kg)</th>
                        <th>Size of Pipe</th>
                        @foreach($lengths as $length)
                            <th class="text-center">{{ $length }}m</th>
                        @endforeach
                        <th>Total Product Weight (kg)</th>
                        <th>Waste (kg)</th>
                        <th>Gross (kg)</th>
                        <th>Ovality</th>
                        <th>Thickness</th>
                        <th>Outer Diameter</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totals = array_fill_keys($lengths->toArray(), 0);
                        $totalQuantityConsumed = 0;
                        $totalProductWeight = 0;
                        $totalWaste = 0;
                        $totalGross = 0;
                    @endphp
                    
                    @forelse($grouped as $rawMaterial => $byProduct)
                        @foreach($byProduct as $productName => $bySize)
                            @foreach($bySize as $size => $records)
                                @php
                                    // Raw material consumed
                                    $qtyConsumed = $records->sum(function($rec) { 
                                        return $rec->materialStockOutLines->sum('quantity_consumed'); 
                                    });
                                    $totalQuantityConsumed += $qtyConsumed;
                                    
                                    // Use the actual total_weight field from database if available, otherwise calculate
                                    $productWeight = $records->sum('total_weight');
                                    if ($productWeight <= 0) {
                                        // Fallback calculation if total_weight is not set
                                        $productWeight = $records->sum('quantity') * ($records->first()->product->weight_per_meter ?? 0);
                                    }
                                    $totalProductWeight += $productWeight;
                                    
                                    // Calculate waste
                                    $waste = max(0, $qtyConsumed - $productWeight);
                                    $totalWaste += $waste;
                                    
                                    // Gross weight
                                    $gross = $qtyConsumed;
                                    $totalGross += $gross;
                                    
                                    // Get quality metrics - use average values for the group
                                    $ovality = $records->avg('ovality');
                                    $thickness = $records->avg('thickness');
                                    $outerDiameter = $records->avg('outer_diameter');
                                    
                                    // Get the actual size from the first record if size is empty
                                    $displaySize = $size ?: ($records->first()->size ?? 'N/A');
                                @endphp
                                <tr>
                                    <td>{{ $rawMaterial }}</td>
                                    <td class="text-right">{{ number_format($qtyConsumed, 2) }}</td>
                                    <td>{{ $displaySize }}</td>
                                    @foreach($lengths as $length)
                                        @php
                                            $qty = $records->where('length_m', $length)->sum('quantity');
                                            $totals[$length] += $qty;
                                        @endphp
                                        <td class="text-right">{{ $qty ?: '' }}</td>
                                    @endforeach
                                    <td class="text-right">{{ number_format($productWeight, 2) }}</td>
                                    <td class="text-right">{{ number_format($waste, 2) }}</td>
                                    <td class="text-right">{{ number_format($gross, 2) }}</td>
                                    <td class="text-center">{{ $ovality ? number_format($ovality, 3) : '-' }}</td>
                                    <td class="text-center">{{ $thickness ? number_format($thickness, 3) : '-' }}</td>
                                    <td class="text-center">{{ $outerDiameter ? number_format($outerDiameter, 3) : '-' }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="{{ 10 + count($lengths) }}" class="text-center py-4">No production data found for the selected filters</td>
                        </tr>
                    @endforelse
                    
                    @if($grouped->count() > 0)
                    <tr class="total-row">
                        <td>Total</td>
                        <td class="text-right">{{ number_format($totalQuantityConsumed, 2) }}</td>
                        <td></td>
                        @foreach($lengths as $length)
                            <td class="text-right">{{ $totals[$length] }}</td>
                        @endforeach
                        <td class="text-right">{{ number_format($totalProductWeight, 2) }}</td>
                        <td class="text-right">{{ number_format($totalWaste, 2) }}</td>
                        <td class="text-right">{{ number_format($totalGross, 2) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Quality Comments -->
        <div class="comments">
            @if($qualityReport)
                <div class="comments-title">Comment of Quality</div>
                <div class="mb-2">{{ $qualityReport->quality_comment ?: 'Today all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following:' }}</div>
                
                @if($qualityReport->problems)
                    <div class="comments-title">Problems:</div>
                    <div class="mb-2">{!! nl2br(e($qualityReport->problems)) !!}</div>
                @endif
                
                @if($qualityReport->corrective_actions)
                    <div class="comments-title">Corrective action:</div>
                    <div class="mb-2">{!! nl2br(e($qualityReport->corrective_actions)) !!}</div>
                @endif
                
                @if($qualityReport->remarks)
                    <div class="comments-title">Remark:</div>
                    <div class="mb-2">{!! nl2br(e($qualityReport->remarks)) !!}</div>
                @endif
            @else
                @if($grouped->count() > 0)
                <div class="comments-title">Comment of Quality</div>
                <div class="mb-2">Today all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following:</div>
                
                <div class="comments-title">Problems:</div>
                <ul class="list-disc pl-5 mb-2">
                    <li>Example: 110mm PN10 products had a problem of weight (over), thickness, high ovality difference, blue stripe fluctuation, electric power fluctuation, and Outer Diameter problem.</li>
                </ul>
                
                <div class="comments-title">Corrective action:</div>
                <div class="mb-2">The problems were reduced by communicating with the shift leader and operators. However, weight and raw material quality problem were not reduced because of the thickness of the products did not fulfill the standard parameter when it was produced in the standard weight, so in order to reduce this problem we increased the weight by prioritizing the thickness of the products and shrinkage problem in 125mm products was reduced by increasing the length and OD of products and raw material problems were reduced by changing the raw materials.</div>
                
                <div class="comments-title">Remark:</div>
                <div class="mb-2">As quality we recommended that the double type raw materials quality (purity and density) should be checked.</div>
                @endif
            @endif
        </div>

        <!-- Signature Section -->
        @if($grouped->count() > 0)
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-item">
                    <div class="mb-1">Prepared by: <span class="underline">{{ $qualityReport->prepared_by ?? 'Yohannes Choma' }}</span></div>
                    <div>Date: <span class="underline">{{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</span></div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-item">
                    <div class="mb-1">Checked by: <span class="underline">{{ $qualityReport->checked_by ?? 'Yeshiamb A.' }}</span></div>
                    <div>Date: <span class="underline">{{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</span></div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-item">
                    <div class="mb-1">Approved by: <span class="underline">{{ $qualityReport->approved_by ?? 'Aschalew' }}</span></div>
                    <div>Date: <span class="underline">{{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</span></div>
                </div>
            </div>
        </div>
        @endif
    </div>
</body>
</html>