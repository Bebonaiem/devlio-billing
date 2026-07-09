<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; line-height: 1.5; }
        .invoice-container { max-width: 800px; margin: 0 auto; padding: 30px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 20px; }
        .company-info { flex: 1; }
        .company-name { font-size: 24px; font-weight: bold; color: #4f46e5; margin-bottom: 5px; }
        .company-details { color: #666; font-size: 11px; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 28px; color: #4f46e5; text-transform: uppercase; }
        .invoice-number { font-size: 14px; color: #666; margin-top: 5px; }
        .invoice-meta { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .meta-box { flex: 1; padding: 15px; background: #f8fafc; border-radius: 8px; margin-right: 15px; }
        .meta-box:last-child { margin-right: 0; }
        .meta-label { font-size: 10px; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; margin-bottom: 5px; }
        .meta-value { font-size: 13px; font-weight: 600; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #4f46e5; color: white; padding: 12px 15px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table th:last-child { text-align: right; }
        .items-table td { padding: 12px 15px; border-bottom: 1px solid #e2e8f0; }
        .items-table td:last-child { text-align: right; font-weight: 600; }
        .items-table tr:nth-child(even) { background: #f8fafc; }
        .totals { display: flex; justify-content: flex-end; margin-bottom: 30px; }
        .totals-box { width: 300px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0; }
        .totals-row:last-child { border-bottom: none; }
        .totals-row.total { font-size: 16px; font-weight: bold; color: #4f46e5; border-top: 2px solid #4f46e5; padding-top: 12px; margin-top: 5px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #e5e7eb; color: #374151; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center; color: #94a3b8; font-size: 10px; }
        .payment-info { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 15px; margin-bottom: 20px; }
        .payment-info h3 { color: #0369a1; font-size: 13px; margin-bottom: 8px; }
        .payment-info p { font-size: 11px; color: #0c4a6e; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ config('settings.company_name', 'Your Company') }}</div>
                <div class="company-details">
                    {{ config('settings.company_address', '') }}<br>
                    {{ config('settings.company_city', '') }}<br>
                    {{ config('settings.company_country', '') }}<br>
                    {{ config('settings.company_email', '') }}
                </div>
            </div>
            <div class="invoice-title">
                <h1>Invoice</h1>
                <div class="invoice-number">#{{ $invoice->number }}</div>
                <div class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</div>
            </div>
        </div>

        <div class="invoice-meta">
            <div class="meta-box">
                <div class="meta-label">{{ $invoice->bill_to }}</div>
                <div class="meta-value">
                    {{ $invoice->user_name }}<br>
                    @if(!empty($invoice->user_properties))
                        @foreach($invoice->user_properties as $key => $value)
                            {{ $value }}<br>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="meta-box">
                <div class="meta-label">Invoice Date</div>
                <div class="meta-value">{{ $invoice->created_at->format('M d, Y') }}</div>
            </div>
            <div class="meta-box">
                <div class="meta-label">Due Date</div>
                <div class="meta-value">{{ $invoice->due_at ? $invoice->due_at->format('M d, Y') : 'Upon Receipt' }}</div>
            </div>
        </div>

        @if(!$invoice->isPaid())
        <div class="payment-info">
            <h3>Payment Instructions</h3>
            <p>Please pay this invoice by the due date. You can pay online through your dashboard or contact us for alternative payment methods.</p>
        </div>
        @endif

        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $invoice->currency->prefix ?? '' }}{{ number_format($item->price, 2) }}{{ $invoice->currency->suffix ?? '' }}</td>
                    <td>{{ $invoice->currency->prefix ?? '' }}{{ number_format($item->quantity * $item->price, 2) }}{{ $invoice->currency->suffix ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-box">
                <div class="totals-row">
                    <span>Subtotal</span>
                    <span>{{ $invoice->currency->prefix ?? '' }}{{ number_format($invoice->total(), 2) }}{{ $invoice->currency->suffix ?? '' }}</span>
                </div>
                @if(($totals['tax_amount'] ?? 0) > 0)
                <div class="totals-row">
                    <span>Tax ({{ number_format($totals['tax_rate'] ?? 0, 1) }}%)</span>
                    <span>{{ $invoice->currency->prefix ?? '' }}{{ number_format($totals['tax_amount'], 2) }}{{ $invoice->currency->suffix ?? '' }}</span>
                </div>
                @endif
                @if(($totals['discount'] ?? 0) > 0)
                <div class="totals-row">
                    <span>Discount</span>
                    <span>-{{ $invoice->currency->prefix ?? '' }}{{ number_format($totals['discount'], 2) }}{{ $invoice->currency->suffix ?? '' }}</span>
                </div>
                @endif
                <div class="totals-row total">
                    <span>{{ $invoice->isPaid() ? 'Amount Paid' : 'Amount Due' }}</span>
                    <span>{{ $invoice->currency->prefix ?? '' }}{{ number_format($totals['total'] ?? $invoice->total(), 2) }}{{ $invoice->currency->suffix ?? '' }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>{{ config('settings.invoice_footer', 'Thank you for your business!') }}</p>
            <p style="margin-top: 5px;">{{ config('settings.company_name', 'Your Company') }} | {{ config('settings.company_email', '') }} | {{ config('settings.company_website', '') }}</p>
        </div>
    </div>
</body>
</html>
