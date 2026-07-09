<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1a1a2e; background: #fff; font-size: 13px; line-height: 1.5; }
        .invoice-container { width: 100%; max-width: 800px; margin: 0 auto; padding: 40px 50px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 3px solid #1a1a2e; }
        .company-info h1 { font-size: 28px; font-weight: 700; color: #1a1a2e; margin-bottom: 6px; }
        .company-info p { font-size: 12px; color: #555; line-height: 1.6; }
        .invoice-title { text-align: right; }
        .invoice-title h2 { font-size: 36px; font-weight: 700; color: #1a1a2e; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title .invoice-number { font-size: 14px; color: #555; margin-top: 4px; }
        .meta-section { display: flex; justify-content: space-between; margin-bottom: 35px; }
        .meta-box { width: 48%; }
        .meta-box h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #888; margin-bottom: 10px; font-weight: 600; }
        .meta-box p { font-size: 13px; color: #333; line-height: 1.7; }
        .meta-box .label { font-weight: 600; color: #1a1a2e; }
        .status-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 6px; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .status-cancelled { background: #e2e3e5; color: #383d41; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        thead th { background: #1a1a2e; color: #fff; padding: 12px 16px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600; }
        thead th:last-child { text-align: right; }
        thead th.text-center { text-align: center; }
        tbody td { padding: 12px 16px; border-bottom: 1px solid #eee; font-size: 13px; color: #333; }
        tbody td:last-child { text-align: right; font-weight: 600; }
        tbody td.text-center { text-align: center; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        .totals-section { display: flex; justify-content: flex-end; margin-bottom: 40px; }
        .totals-table { width: 300px; }
        .totals-table .row { display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; color: #555; }
        .totals-table .row.total { border-top: 2px solid #1a1a2e; padding-top: 12px; margin-top: 4px; font-size: 18px; font-weight: 700; color: #1a1a2e; }
        .footer { text-align: center; padding-top: 30px; border-top: 1px solid #eee; color: #888; font-size: 11px; }
        .footer p { margin-bottom: 4px; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <h1>{{ \App\Models\Setting::get('company_name', config('app.name', 'My Company')) }}</h1>
                @php
                    $companyAddress = \App\Models\Setting::get('company_address', '');
                    $companyEmail = \App\Models\Setting::get('company_email', '');
                    $companyPhone = \App\Models\Setting::get('company_phone', '');
                @endphp
                @if($companyAddress)
                    <p>{!! nl2br(e($companyAddress)) !!}</p>
                @endif
                @if($companyEmail)
                    <p>{{ $companyEmail }}</p>
                @endif
                @if($companyPhone)
                    <p>{{ $companyPhone }}</p>
                @endif
            </div>
            <div class="invoice-title">
                <h2>Invoice</h2>
                <div class="invoice-number">{{ $invoice->number }}</div>
            </div>
        </div>

        <div class="meta-section">
            <div class="meta-box">
                <h3>Bill To</h3>
                @if($invoice->snapshot && $invoice->snapshot->bill_to)
                    <p class="label">{{ $invoice->snapshot->bill_to }}</p>
                @else
                    <p class="label">{{ $invoice->user->name }}</p>
                    <p>{{ $invoice->user->email }}</p>
                @endif
                @if($invoice->user->first_name || $invoice->user->last_name)
                    <p>{{ trim($invoice->user->first_name . ' ' . $invoice->user->last_name) }}</p>
                @endif
            </div>
            <div class="meta-box" style="text-align: right;">
                <h3>Invoice Details</h3>
                <p><span class="label">Date:</span> {{ $invoice->created_at->format('M j, Y') }}</p>
                @if($invoice->due_at)
                    <p><span class="label">Due Date:</span> {{ $invoice->due_at->format('M j, Y') }}</p>
                @endif
                @if($invoice->currency_code)
                    <p><span class="label">Currency:</span> {{ $invoice->currency_code }}</p>
                @endif
                <p style="margin-top: 6px;">
                    <span class="label">Status:</span>
                    <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th class="text-center" style="width: 15%;">Qty</th>
                    <th style="width: 17%;">Unit Price</th>
                    <th style="width: 18%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td>{{ $invoice->currency ? $invoice->currency->symbol : '$' }}{{ number_format($item->price, 2) }}</td>
                        <td>{{ $invoice->currency ? $invoice->currency->symbol : '$' }}{{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <div class="totals-table">
                <div class="row">
                    <span>Subtotal</span>
                    <span>{{ $invoice->currency ? $invoice->currency->symbol : '$' }}{{ number_format($totals['subtotal'], 2) }}</span>
                </div>
                @if($totals['tax'] > 0)
                    <div class="row">
                        <span>{{ $totals['tax_name'] }} ({{ $totals['tax_rate'] }}%)</span>
                        <span>{{ $invoice->currency ? $invoice->currency->symbol : '$' }}{{ number_format($totals['tax'], 2) }}</span>
                    </div>
                @endif
                <div class="row total">
                    <span>Total</span>
                    <span>{{ $invoice->currency ? $invoice->currency->symbol : '$' }}{{ number_format($totals['total'], 2) }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>{{ \App\Models\Setting::get('company_name', config('app.name', 'My Company')) }}</p>
            <p>Thank you for your business.</p>
        </div>
    </div>
</body>
</html>
