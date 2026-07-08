<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    protected $fillable = [
        'name',
        'permissions',
        'token',
        'user_id',
        'type',
        'ip_addresses',
        'last_used_at',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'json',
            'ip_addresses' => 'array',
            'last_used_at' => 'datetime',
            'enabled' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
