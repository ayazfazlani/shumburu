<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Production Report - SHUMBRO PLASTIC FACTORY</title>
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
        .container { padding: 24px; background: white; border: 1px solid #d1d5db; border-radius: 8px; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .logo-section { display: flex; align-items: center; gap: 8px; }
        .logo { width: 64px; height: 64px; background: #3b82f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px; }
        .title-section { font-weight: bold; font-size: 18px; line-height: 1.2; }
        .subtitle { font-size: 12px; color: #6b7280; }
        .meta-section { text-align: right; font-size: 12px; }
        .font-semibold { font-weight: 600; }
        .month-info { font-size: 11px; margin-bottom: 10px; padding: 6px 10px; background: #f1f5f9; border-radius: 4px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 11px; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: left; }
        thead th { background: #e5e7eb; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #e5e7eb; font-weight: bold; }
        .comments { margin-top: 16px; padding: 12px; border-radius: 4px; border: 1px solid #d1d5db; background: #f9fafb; }
        .comments-title { font-weight: 600; margin-bottom: 8px; text-decoration: underline; }
        .signature-section { margin-top: 24px; display: flex; justify-content: space-between; font-size: 11px; }
        .signature-box { display: flex; flex-direction: column; gap: 16px; }
        .signature-item { display: flex; flex-direction: column; gap: 4px; }
        .underline { border-bottom: 1px solid #000; display: inline-block; min-width: 120px; text-align: center; }
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
                    <div class="subtitle">Quality Control Monthly Production and Raw Materials Report</div>
                </div>
            </div>
            <div class="meta-section">
                <div><span class="font-semibold">Document no</span> S/P/E/PR/QC:004</div>
                <div><span class="font-semibold">Month:</span> {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</div>
            </div>
        </div>

        <!-- Month Information -->
        <div class="month-info">
            <span class="font-semibold">Report Period: </span>
            {{ \Carbon\Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d') }} to {{ \Carbon\Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d') }}
        </div>

        <!-- Table -->
        <div class="overflow-x-auto mt-2">
            <table>
                <thead>
                    <tr>
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

                        $allOvality = [];
                        // Ovality tracking
                    $totalStartOvality = 0;
                    $totalEndOvality = 0;
                    $totalOvalityCount = 0;
                        $allThickness = [];
                        $allOuterDiameter = [];
                    @endphp
                    
                    @forelse($grouped as $rawMaterial => $byProduct)
                        @foreach($byProduct as $productName => $bySize)
                            @foreach($bySize as $size => $records)
                                @php
                                    $qtyConsumed = $records->sum(fn($rec) => $rec->materialStockOutLines->sum('quantity_consumed'));
                                    $totalQuantityConsumed += $qtyConsumed;
                                    
                                    $productWeight = $records->sum('total_weight');
                                    if ($productWeight <= 0) {
                                        $productWeight = $records->sum('quantity') * ($records->first()->product->weight_per_meter ?? 0);
                                    }
                                    $totalProductWeight += $productWeight;
                                    
                                    $waste = max(0, $qtyConsumed - $productWeight);
                                    $totalWaste += $waste;
                                    
                                    $gross = $qtyConsumed;
                                    $totalGross += $gross;
                                    
                                    // $ovality = $records->avg('ovality');
                                     $startOvality = null;
                                    $endOvality = null;
                                    foreach ($records as $rec) {
                                        if ($rec->start_ovality !== null) {
                                            $totalStartOvality += $rec->start_ovality;
                                            $startOvality = $rec->start_ovality;
                                        }
                                        if ($rec->end_ovality !== null) {
                                            $totalEndOvality += $rec->end_ovality;
                                            $endOvality = $rec->end_ovality;
                                        }
                                    }
                                    if ($startOvality !== null || $endOvality !== null) {
                                        $totalOvalityCount++;
                                    }

                                    $thickness = $records->avg('thickness');
                                    $outerDiameter = $records->avg('outer_diameter');

                                    // if ($ovality) $allOvality[] = $ovality;
                                    if ($thickness) $allThickness[] = $thickness;
                                    if ($outerDiameter) $allOuterDiameter[] = $outerDiameter;

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
                                    <td class="text-center">{{ $startOvality ? number_format($startOvality, 1) : '-' }}-{{ $endOvality ? number_format($endOvality, 1) : '-' }}</td>
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
                        <td>Total / Avg</td>
                        <td class="text-right">{{ number_format($totalQuantityConsumed, 2) }}</td>
                        <td></td>
                        @foreach($lengths as $length)
                            <td class="text-right">{{ $totals[$length] }}</td>
                        @endforeach
                        <td class="text-right">{{ number_format($totalProductWeight, 2) }}</td>
                        <td class="text-right">{{ number_format($totalWaste, 2) }}</td>
                        <td class="text-right">{{ number_format($totalGross, 2) }}</td>
                         <td class="border border-black">
                            {{ $totalOvalityCount > 0 ? number_format($totalStartOvality / $totalOvalityCount, 1) : '-' }}
                            -
                            {{ $totalOvalityCount > 0 ? number_format($totalEndOvality / $totalOvalityCount, 1) : '-' }}
                        </td>
                        <td class="text-center">{{ count($allThickness) ? number_format(array_sum($allThickness)/count($allThickness), 3) : '-' }}</td>
                        <td class="text-center">{{ count($allOuterDiameter) ? number_format(array_sum($allOuterDiameter)/count($allOuterDiameter), 3) : '-' }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Quality Comments -->
        <div class="comments">
            @if($qualityReport)
                <div class="comments-title">Comment of Quality</div>
                <div class="mb-2">{{ $qualityReport->quality_comment ?: 'In this month all products were produced according to the standards...' }}</div>
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
