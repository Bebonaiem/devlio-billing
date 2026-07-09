¿<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingAgreement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ulid',
        'user_id',
        'gateway_id',
        'name',
        'external_reference',
        'type',
        'expiry',
    ];

    protected function casts(): array
    {
        return [
            'expiry' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(Extension::class, 'gateway_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
