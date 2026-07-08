<?php

return [
    'invoice_prefix' => env('INVOICE_PREFIX', 'INV-'),
    'tax_rate' => env('TAX_RATE', 0),
    'grace_days' => env('GRACE_DAYS', 3),
    'terminate_days' => env('TERMINATE_DAYS', 14),
    'affiliate_rate' => env('AFFILIATE_RATE', 10),
    'currency' => env('BILLING_CURRENCY', 'USD'),
];
