<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'body',
        'category',
        'enabled',
        'order_column',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'views' => 'integer',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (Article $article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }
}
