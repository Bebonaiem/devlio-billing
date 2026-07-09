<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceSnapshot extends Model
{
    protected $fillable = [
        'invoice_id',
        'name',
        'properties',
        'tax_name',
        'tax_rate',
        'tax_country',
        'bill_to',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'json',
            'tax_rate' => 'decimal:2',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
