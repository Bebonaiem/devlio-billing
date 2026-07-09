<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceConfig extends Model
{
    protected $fillable = [
        'service_id',
        'config_option_id',
        'config_value_id',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function configOption(): BelongsTo
    {
        return $this->belongsTo(ConfigOption::class);
    }

    public function configValue(): BelongsTo
    {
        return $this->belongsTo(ConfigOption::class, 'config_value_id');
    }
}
