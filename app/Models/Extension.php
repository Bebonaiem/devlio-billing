<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Extension extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'enabled',
        'extension',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }
}
