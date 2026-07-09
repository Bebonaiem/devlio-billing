¿<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'prefix',
        'suffix',
        'format',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class, 'currency_code', 'code');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'currency_code', 'code');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'currency_code', 'code');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'currency_code', 'code');
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class, 'currency_code', 'code');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'currency_code', 'code');
    }
}
