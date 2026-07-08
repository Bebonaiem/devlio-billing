<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Server extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'service_id',
        'pterodactyl_server_id',
        'pterodactyl_server_identifier',
        'name',
        'status',
        'cpu',
        'memory',
        'disk',
        'node',
        'ip',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
