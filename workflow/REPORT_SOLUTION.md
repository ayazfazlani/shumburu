# Dynamic Daily Production Report Solution

## Overview

This document outlines the approach for generating a dynamic daily production report in the Shumburo system. The report aggregates data from multiple sources and displays it in a flexible, filterable table.

---

## Data Sources & Relationships

-   **FinishedGood**: Stores finished product records. Key fields: `id`, `product_id`, `production_line_id`, `size`, `length_m`, `quantity`, `created_at`.
-   **ProductionLine**: Stores production line info. Key fields: `id`, `name`, `shift` (shift is a property of the production line).
-   **Product**: Stores product info. Key fields: `id`, `name`, `weight_per_meter` (used for total weight calculation).
-   **MaterialStockOutLine**: (if needed for raw material tracking)

---

## Key Field Mappings

-   **Shift**: Retrieved via the relationship from `FinishedGood` → `ProductionLine` → `shift`.
-   **Size**: Directly from `FinishedGood.size`.
-   **Length Columns**: Dynamically generated from unique `length_m` values in `FinishedGood` for the selected date.
-   **Total Weight**: Calculated as `quantity * length_m * product.weight_per_meter` for each record.
-   **Waste, Gross, etc.**: Add fields/calculations as needed, e.g., from a `waste` field or related table.

---

## Steps to Generate the Report

1. **Filter Data**
    - By date (required)
    - By shift (optional, via production line)
    - By product (optional)
2. **Fetch All Unique Lengths**
    - Get all unique `length_m` values for the selected date and filters to build dynamic columns.
3. **Fetch and Join Data**
    - Eager load `product` and `productionLine` relationships for each `FinishedGood`.
    - If filtering by shift, filter by `productionLine.shift`.
4. **Group Data**
    - Group by product, shift, size, and other relevant fields.
5. **Calculate Totals**
    - For each row, calculate total weight, waste, gross, etc.
    - At the bottom, sum each column for grand totals.
6. **Display in Blade**
    - Render a table with dynamic columns for each length.
    - Show all required fields and totals.
    - Add filter controls at the top.

---

## Example Code Structure

### Livewire Component (Pseudo-code)

```php
public $date, $shift = '', $product_id = '';

public function render() {
    $query = FinishedGood::with(['product', 'productionLine'])
        ->whereDate('created_at', $this->date);
    if ($this->product_id) {
        $query->where('product_id', $this->product_id);
    }
    if ($this->shift) {
        $query->whereHas('productionLine', fn($q) => $q->where('shift', $this->shift));
    }
    $finishedGoods = $query->get();
    $lengths = $finishedGoods->pluck('length_m')->unique()->sort()->values();
    // Group and calculate as needed
    return view('...', compact('finishedGoods', 'lengths', ...));
}
```

### Blade Table (Pseudo-code)

```blade
@foreach($grouped as $group)
    <tr>
        <td>{{ $group->product->name }}</td>
        <td>{{ $group->productionLine->shift }}</td>
        <td>{{ $group->size }}</td>
        @foreach($lengths as $length)
            <td>{{ $group->where('length_m', $length)->sum('quantity') }}</td>
        @endforeach
        <td>{{ $group->sum(fn($rec) => $rec->quantity * $rec->length_m * $rec->product->weight_per_meter) }}</td>
        <!-- More fields as needed -->
    </tr>
@endforeach
```

---

## Notes

-   Always eager load relationships to avoid N+1 queries.
-   Adjust field names as per your actual schema.
-   Add more filters (e.g., by line, operator) as needed.
-   For waste/gross, join or sum from related tables if not in `FinishedGood`.

---

## Next Steps

-   Implement the above logic in your Livewire component and Blade view.
-   Test with real data and refine as needed.
