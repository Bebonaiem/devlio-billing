<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'subject',
        'enabled',
        'body',
        'cc',
        'bcc',
        'mail_enabled',
        'in_app_enabled',
        'in_app_title',
        'in_app_body',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'mail_enabled' => 'boolean',
            'in_app_enabled' => 'boolean',
        ];
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }
}
