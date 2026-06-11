<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $pr->po_number }}</title>
    <style>
        @page { size: A4; margin: 0; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            margin: 0; 
            padding: 40px; 
            color: #18181b; 
            background: #fff;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
        }
        .container { max-width: 800px; margin: 0 auto; }
        
        /* Header Section */
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start;
            border-bottom: 4px solid #18181b; 
            padding-bottom: 24px; 
            margin-bottom: 32px; 
        }
        .brand {
            display: flex;
            flex-direction: column;
        }
        .logo { font-size: 24px; font-weight: 900; letter-spacing: -0.05em; text-transform: uppercase; line-height: 1; }
        .division { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.2em; color: #71717a; margin-top: 4px; }
        
        .doc-meta { text-align: right; }
        .doc-meta h1 { font-size: 32px; font-weight: 900; margin: 0; line-height: 0.8; letter-spacing: -0.02em; color: #18181b; }
        .po-ref { font-size: 12px; font-weight: 800; color: #71717a; margin-top: 8px; text-transform: uppercase; }
        .po-number { color: #18181b; }

        /* Stakeholders */
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .addr-box h3 { font-size: 9px; font-weight: 900; text-transform: uppercase; color: #a1a1aa; margin-bottom: 8px; letter-spacing: 0.1em; }
        .addr-box p { font-size: 11px; margin: 0; }
        .addr-box strong { font-size: 13px; display: block; margin-bottom: 2px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th { 
            background: #f4f4f5; 
            text-align: left; 
            padding: 10px 12px; 
            font-size: 10px; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 0.05em;
            border-top: 1px solid #18181b;
            border-bottom: 1px solid #e4e4e7;
        }
        td { padding: 12px; font-size: 11px; border-bottom: 1px solid #f4f4f5; vertical-align: top; }
        .col-qty { text-align: center; font-weight: 700; width: 80px; }
        .col-price { text-align: right; width: 100px; }
        .col-total { text-align: right; font-weight: 800; width: 120px; }
        
        .item-desc strong { font-size: 12px; text-transform: uppercase; }
        .item-notes { font-size: 10px; color: #71717a; margin-top: 4px; display: block; }

        /* Totals */
        .summary { display: flex; justify-content: flex-end; margin-bottom: 40px; }
        .summary-box { width: 240px; }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f4f4f5; }
        .summary-row.final { border-bottom: none; padding-top: 12px; border-top: 2px solid #18181b; margin-top: 4px; }
        .summary-row.final span { font-size: 18px; font-weight: 900; }
        .summary-row span:first-child { font-size: 10px; font-weight: 800; text-transform: uppercase; color: #71717a; }
        .summary-row span:last-child { font-size: 12px; font-weight: 700; }

        /* Additional Info */
        .meta-info { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 60px; }
        .info-item h3 { font-size: 9px; font-weight: 900; text-transform: uppercase; color: #a1a1aa; margin-bottom: 6px; }
        .info-item p { font-size: 11px; font-weight: 700; margin: 0; }

        /* Footer */
        .footer { 
            border-top: 1px solid #e4e4e7; 
            padding-top: 24px; 
            font-size: 9px; 
            color: #a1a1aa; 
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-ref { font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; }
        .footer-stamp { text-align: right; }
        .stamp-box { 
            width: 80px; 
            height: 80px; 
            border: 2px dashed #e4e4e7; 
            border-radius: 12px; 
            margin-left: auto;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            color: #d1d5db;
        }

        .no-print { 
            position: fixed; 
            top: 20px; 
            right: 20px; 
            background: #18181b; 
            color: white; 
            padding: 12px 24px; 
            border-radius: 8px; 
            font-weight: 800; 
            text-transform: uppercase; 
            font-size: 11px;
            cursor: pointer;
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            z-index: 100;
        }
        .no-print:hover { background: #000; }

        @media print {
            .no-print { display: none; }
            body { padding: 40px; }
        }
    </style>
</head>
<body>
    <button class="no-print" onclick="window.print()">Print Document</button>

    <div class="container">
        <div class="header">
            <div class="brand">
                <span class="logo">FACTORY ERP</span>
                <span class="division">Procurement Division</span>
            </div>
            <div class="doc-meta">
                <h1>PURCHASE ORDER</h1>
                <p class="po-ref">
                    PO Number: <span class="po-number">{{ $pr->po_number }}</span><br>
                    Issued Date: <span class="po-number">{{ $pr->po_issued_at ? $pr->po_issued_at->format('d M Y') : now()->format('d M Y') }}</span>
                </p>
            </div>
        </div>

        <div class="grid">
            <div class="addr-box">
                <h3>From:</h3>
                <strong>Factory Operations</strong>
                <p>Industrial Zone, Sector 4<br>Main City Highway<br>Email: procurement@factory.com</p>
            </div>
            <div class="addr-box">
                <h3>To (Supplier):</h3>
                <strong>{{ $pr->supplier->name ?? 'N/A' }}</strong>
                <p>
                    {{ $pr->supplier->address ?? 'Address not specified' }}<br>
                    Contact: {{ $pr->supplier->contact_person ?? 'N/A' }}<br>
                    Phone: {{ $pr->supplier->phone ?? 'N/A' }}
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="col-qty">Quantity</th>
                    <th class="col-price">Unit Price</th>
                    <th class="col-total">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="item-desc">
                        <strong>{{ $pr->rawMaterial->name }}</strong>
                        <span class="item-notes">Raw material procurement for production use. Linked to Demand: #{{ $pr->production_request_id ?? 'Manual' }}</span>
                    </td>
                    <td class="col-qty">{{ number_format($pr->quantity, 2) }} {{ $pr->rawMaterial->unit }}</td>
                    <td class="col-price">{{ number_format($pr->unit_price, 2) }}</td>
                    <td class="col-total">{{ number_format($pr->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-box">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>{{ number_format($pr->total_amount, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Tax (%)</span>
                    <span>0.00</span>
                </div>
                <div class="summary-row final">
                    <span>NET PAYABLE</span>
                    <span>{{ number_format($pr->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="meta-info">
            <div class="info-item">
                <h3>Expected Delivery Date</h3>
                <p>{{ $pr->expected_delivery_date ? $pr->expected_delivery_date->format('d M Y') : 'As soon as possible' }}</p>
            </div>
            <div class="info-item">
                <h3>Payment Terms</h3>
                <p>{{ $pr->supplier->payment_terms ?? 'Standard Payment Terms' }}</p>
            </div>
        </div>

        <div class="footer">
            <div>
                <p class="footer-ref">Ref: PR-{{ str_pad($pr->id, 5, '0', STR_PAD_LEFT) }} | Issued by: {{ $pr->requestedBy->name ?? 'System' }}</p>
                <p>This is a computer-generated document. No signature is required.</p>
            </div>
            <div class="footer-stamp">
                <div class="stamp-box">Official Stamp</div>
                <p class="footer-ref">Approved By: {{ $pr->approvedBy->name ?? 'Admin' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
