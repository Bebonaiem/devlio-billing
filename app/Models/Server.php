<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = [
        'order_id', 'user_id', 'pterodactyl_server_id', 'pterodactyl_server_identifier',
        'name', 'status', 'cpu', 'memory', 'disk', 'node', 'ip',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
