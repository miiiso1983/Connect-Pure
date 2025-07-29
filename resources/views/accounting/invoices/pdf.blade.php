<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .company-details {
            font-size: 11px;
            color: #666;
            line-height: 1.5;
        }
        
        .invoice-title {
            text-align: right;
            flex: 1;
        }
        
        .invoice-title h1 {
            font-size: 28px;
            color: #007bff;
            margin-bottom: 10px;
        }
        
        .invoice-meta {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .invoice-meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .invoice-meta-row:last-child {
            margin-bottom: 0;
        }
        
        .label {
            font-weight: bold;
            color: #495057;
        }
        
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .billing-info {
            flex: 1;
            margin-right: 20px;
        }
        
        .billing-info h3 {
            font-size: 14px;
            color: #007bff;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        
        .customer-details {
            font-size: 11px;
            line-height: 1.6;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th {
            background: #007bff;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        
        .items-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #dee2e6;
            font-size: 11px;
        }
        
        .items-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-section {
            float: right;
            width: 300px;
            margin-bottom: 30px;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .totals-table .label {
            text-align: right;
            background: #f8f9fa;
        }
        
        .totals-table .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .total-row {
            background: #007bff !important;
            color: white !important;
            font-weight: bold;
            font-size: 14px;
        }
        
        .footer {
            clear: both;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        
        .notes {
            margin-bottom: 20px;
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
        }
        
        .notes h4 {
            color: #856404;
            margin-bottom: 8px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-draft { background: #6c757d; color: white; }
        .status-sent { background: #007bff; color: white; }
        .status-paid { background: #28a745; color: white; }
        .status-overdue { background: #dc3545; color: white; }
        .status-cancelled { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-details">
                    {{ $company['address'] }}<br>
                    {{ $company['city'] }}, {{ $company['postal_code'] }}<br>
                    {{ $company['country'] }}<br>
                    Phone: {{ $company['phone'] }}<br>
                    Email: {{ $company['email'] }}<br>
                    @if($company['tax_number'])
                        Tax ID: {{ $company['tax_number'] }}
                    @endif
                </div>
            </div>
            <div class="invoice-title">
                <h1>INVOICE</h1>
                <span class="status-badge status-{{ strtolower($invoice->status) }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </div>
        </div>

        <!-- Invoice Meta Information -->
        <div class="invoice-meta">
            <div class="invoice-meta-row">
                <span class="label">Invoice Number:</span>
                <span>{{ $invoice->invoice_number }}</span>
            </div>
            <div class="invoice-meta-row">
                <span class="label">Invoice Date:</span>
                <span>{{ $invoice->invoice_date->format($settings['date_format']) }}</span>
            </div>
            <div class="invoice-meta-row">
                <span class="label">Due Date:</span>
                <span>{{ $invoice->due_date->format($settings['date_format']) }}</span>
            </div>
            @if($invoice->po_number)
            <div class="invoice-meta-row">
                <span class="label">PO Number:</span>
                <span>{{ $invoice->po_number }}</span>
            </div>
            @endif
        </div>

        <!-- Billing Information -->
        <div class="billing-section">
            <div class="billing-info">
                <h3>Bill To:</h3>
                <div class="customer-details">
                    <strong>{{ $invoice->customer->name }}</strong><br>
                    @if($invoice->customer->company_name)
                        {{ $invoice->customer->company_name }}<br>
                    @endif
                    {{ $invoice->customer->billing_address }}<br>
                    {{ $invoice->customer->city }}, {{ $invoice->customer->postal_code }}<br>
                    {{ $invoice->customer->country }}<br>
                    @if($invoice->customer->phone)
                        Phone: {{ $invoice->customer->phone }}<br>
                    @endif
                    @if($invoice->customer->email)
                        Email: {{ $invoice->customer->email }}<br>
                    @endif
                    @if($invoice->customer->tax_number)
                        Tax ID: {{ $invoice->customer->tax_number }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 45%">Description</th>
                    <th style="width: 10%" class="text-center">Qty</th>
                    <th style="width: 15%" class="text-right">Unit Price</th>
                    <th style="width: 10%" class="text-right">Tax</th>
                    <th style="width: 15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->description }}</strong>
                        @if($item->notes)
                            <br><small style="color: #666;">{{ $item->notes }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, $settings['decimal_places']) }}</td>
                    <td class="text-right">{{ number_format($item->tax_amount, $settings['decimal_places']) }}</td>
                    <td class="text-right">{{ number_format($item->total_amount, $settings['decimal_places']) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="amount">{{ number_format($invoice->subtotal, $settings['decimal_places']) }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                <tr>
                    <td class="label">Discount:</td>
                    <td class="amount">-{{ number_format($invoice->discount_amount, $settings['decimal_places']) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Tax:</td>
                    <td class="amount">{{ number_format($invoice->tax_amount, $settings['decimal_places']) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">Total:</td>
                    <td class="amount">{{ $settings['currency_symbol'] }} {{ number_format($invoice->total_amount, $settings['decimal_places']) }}</td>
                </tr>
            </table>
        </div>

        <!-- Notes -->
        @if($invoice->notes)
        <div class="notes">
            <h4>Notes:</h4>
            <p>{{ $invoice->notes }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This invoice was generated on {{ now()->format($settings['date_format']) }} by {{ $company['name'] }}</p>
        </div>
    </div>
</body>
</html>
