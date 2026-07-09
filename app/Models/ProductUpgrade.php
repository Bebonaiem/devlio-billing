¿<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUpgrade extends Model
{
    protected $fillable = [
        'product_id',
        'upgrade_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function upgrade(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'upgrade_id');
    }
}
