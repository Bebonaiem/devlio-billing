<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessageAttachment extends Model
{
    protected $fillable = [
        'ticket_message_id',
        'uuid',
        'file_path',
        'original_name',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class, 'ticket_message_id');
    }
}
