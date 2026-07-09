¿<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceTransaction extends Model
{
    protected $fillable = [
        'invoice_id',
        'gateway_id',
        'amount',
        'fee',
        'transaction_id',
        'status',
        'is_credit_transaction',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'is_credit_transaction' => 'boolean',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Extension::class, 'gateway_id');
    }
}
