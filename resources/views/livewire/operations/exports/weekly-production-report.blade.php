<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Production Report - {{ $startDate }} to {{ $endDate }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .company-name { font-size: 18px; font-weight: bold; }
        .report-title { font-size: 14px; margin: 5px 0; }
        .document-info { font-size: 9px; text-align: right; }
        .filters { margin: 10px 0; padding: 8px; background: #f0f0f0; border-radius: 4px; }
        .filter-item { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #333; padding: 4px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bg-gray { background-color: #f9f9f9; }
        .totals-row { font-weight: bold; background-color: #e6e6e6; }
        .quality-section { margin-top: 15px; padding: 10px; border: 1px solid #333; border-radius: 4px; }
        .signature-section { margin-top: 20px; display: flex; justify-content: space-between; }
        .signature-box { width: 30%; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">SHUMBRO PLASTIC FACTORY</div>
        <div class="report-title">Quality Control Weekly Production of Pipe & Raw Material Reports</div>
        <div class="document-info">
            <div>Document no: S/P/E/PR QC:003</div>
            <div>Week: {{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</div>
        </div>
    </div>

    <div class="filters">
        <div class="filter-item"><strong>Report Period:</strong> {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</div>
        @if($filters['shift'] || $filters['product'] !== 'All' || $filters['raw_material'] !== 'All')
        <div class="filter-item"><strong>Filters Applied:</strong></div>
        @if($filters['shift'])<div class="filter-item">Shift: {{ $filters['shift'] }}</div>@endif
        @if($filters['product'] !== 'All')<div class="filter-item">Product: {{ $filters['product'] }}</div>@endif
        @if($filters['raw_material'] !== 'All')<div class="filter-item">Raw Material: {{ $filters['raw_material'] }}</div>@endif
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Raw Material</th>
                <th>Qty (kg)</th>
                <th>Size of Pipe</th>
                <th>Shift</th>
                <th>Line</th>
                @foreach($lengths as $length)
                    <th>{{ $length }}m</th>
                @endforeach
                <th>Total Product Weight (kg)</th>
                <th>Waste (kg)</th>
                <th>Gross (kg)</th>
                <th>Ovality (start-end)</th>
                <th>Thickness</th>
                <th>Outer Diameter</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalsByLength = array_fill_keys($lengths->toArray(), 0);
                $grandRawQty = 0;
                $grandProductWeight = 0;
                $grandWaste = 0;
                $grandGross = 0;
            @endphp

            @forelse($grouped as $productName => $rows)
                @foreach($rows as $row)
                    @php
                        $raws = $row['raw_materials_list'] ?? [];
                        $rawCount = count($raws) ?: 1;

                        $qtyConsumed = $row['total_raw_consumed'] ?? 0;
                        $productWeight = $row['total_product_weight'] ?? 0;
                        $waste = max(0, $qtyConsumed - $productWeight);
                        $gross = $qtyConsumed;

                        $grandRawQty += $qtyConsumed;
                        $grandProductWeight += $productWeight;
                        $grandWaste += $waste;
                        $grandGross += $gross;

                        $qtyByLength = $row['qty_by_length'] ?? [];

                        $startOval = $row['avg_start_ovality'] ?? 0;
                        $endOval = $row['avg_end_ovality'] ?? 0;
                        $thicknessAvg = $row['avg_thickness'] ?? null;
                        $outerAvg = $row['avg_outer'] ?? null;
                    @endphp

                    @foreach($raws as $index => $rm)
                        <tr>
                            <td class="text-left">{{ $rm['name'] }}</td>
                            <td class="text-right">{{ number_format($rm['qty'], 2) }}</td>

                            @if($index === 0)
                                <td class="text-left" rowspan="{{ $rawCount }}">{{ $row['size'] }}</td>
                                <td rowspan="{{ $rawCount }}">{{ $row['shift'] ?: '-' }}</td>
                                <td rowspan="{{ $rawCount }}">{{ $row['production_line_name'] ?? $row['production_line_id'] ?? '-' }}</td>

                                @foreach($lengths as $l)
                                    @php
                                        $qtyL = $qtyByLength[$l] ?? 0;
                                        $totalsByLength[$l] += $qtyL;
                                    @endphp
                                    <td class="text-right" rowspan="{{ $rawCount }}">{{ $qtyL ? number_format($qtyL, 2) : '' }}</td>
                                @endforeach

                                <td class="text-right" rowspan="{{ $rawCount }}">{{ number_format($productWeight, 2) }}</td>
                                <td class="text-right" rowspan="{{ $rawCount }}">{{ number_format($waste, 2) }}</td>
                                <td class="text-right" rowspan="{{ $rawCount }}">{{ number_format($gross, 2) }}</td>
                                <td rowspan="{{ $rawCount }}">{{ number_format($startOval, 3) }} - {{ number_format($endOval, 3) }}</td>
                                <td rowspan="{{ $rawCount }}">{{ $thicknessAvg ? number_format($thicknessAvg, 3) : '-' }}</td>
                                <td rowspan="{{ $rawCount }}">{{ $outerAvg ? number_format($outerAvg, 3) : '-' }}</td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            @empty
                <tr>
                    <td colspan="{{ 11 + count($lengths) }}" style="text-align: center; padding: 20px;">No production data found for the selected filters</td>
                </tr>
            @endforelse

            @if($grouped->count() > 0)
                <tr class="totals-row">
                    <td>Total</td>
                    <td class="text-right">{{ number_format($grandRawQty, 2) }}</td>
                    <td></td>
                    <td></td>
                    <td></td>

                    @foreach($lengths as $length)
                        <td class="text-right">{{ number_format($totalsByLength[$length] ?? 0, 2) }}</td>
                    @endforeach

                    <td class="text-right">{{ number_format($grandProductWeight, 2) }}</td>
                    <td class="text-right">{{ number_format($grandWaste, 2) }}</td>
                    <td class="text-right">{{ number_format($grandGross, 2) }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>

    @if($grouped->count() > 0)
    <div class="quality-section">
        <div style="font-weight: bold; text-decoration: underline; margin-bottom: 5px;">Comment of Quality</div>
        
        @if($qualityReport)
            <div style="margin-bottom: 10px;">{{ $qualityReport->quality_comment ?: 'In this week all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following for the next products:' }}</div>

            @if($qualityReport->problems)
                <div style="font-weight: bold; text-decoration: underline; margin: 5px 0;">Problems:</div>
                <div style="margin-bottom: 10px;">{!! nl2br(e($qualityReport->problems)) !!}</div>
            @endif

            @if($qualityReport->corrective_actions)
                <div style="font-weight: bold; text-decoration: underline; margin: 5px 0;">Corrective action:</div>
                <div style="margin-bottom: 10px;">{!! nl2br(e($qualityReport->corrective_actions)) !!}</div>
            @endif

            @if($qualityReport->remarks)
                <div style="font-weight: bold; text-decoration: underline; margin: 5px 0;">Remark:</div>
                <div style="margin-bottom: 10px;">{!! nl2br(e($qualityReport->remarks)) !!}</div>
            @endif
        @else
            <div style="margin-bottom: 10px;">In this week all products were produced according to the standards and in a good quality, but we have observed some problems and recommended the following for the next products:</div>
            <div style="font-weight: bold; text-decoration: underline; margin: 5px 0;">Problems:</div>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li>Example: 110mm PN10 products had a problem of weight (over), thickness, high ovality difference, blue stripe fluctuation, electric power fluctuation, and Outer Diameter problem.</li>
            </ul>
        @endif
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div>Prepared by: ___________________</div>
            <div>{{ $qualityReport->prepared_by ?? 'Yohannes Choma' }}</div>
            <div>Date: {{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</div>
        </div>
        <div class="signature-box">
            <div>Checked by: ___________________</div>
            <div>{{ $qualityReport->checked_by ?? 'Yeshiamb A.' }}</div>
            <div>Date: {{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</div>
        </div>
        <div class="signature-box">
            <div>Approved by: ___________________</div>
            <div>{{ $qualityReport->approved_by ?? 'Aschalew' }}</div>
            <div>Date: {{ $qualityReport ? $qualityReport->created_at->format('d-m-Y') : now()->format('d-m-Y') }}</div>
        </div>
    </div>
    @endif
</body>
</html>