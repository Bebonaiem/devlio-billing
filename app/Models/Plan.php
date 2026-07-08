<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'product_id', 'name', 'description', 'cpu', 'memory', 'disk', 'swap',
        'databases', 'backups', 'allocations', 'nest_id', 'egg_id',
        'billing_cycle', 'price', 'setup_fee', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cpu' => 'integer',
            'memory' => 'integer',
            'disk' => 'integer',
            'swap' => 'integer',
            'databases' => 'integer',
            'backups' => 'integer',
            'allocations' => 'integer',
            'price' => 'decimal:2',
            'setup_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getBillingLabelAttribute(): string
    {
        return match ($this->billing_cycle) {
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'semi_annually' => 'Semi-Annually',
            'annually' => 'Annually',
            default => ucfirst($this->billing_cycle),
        };
    }
}
