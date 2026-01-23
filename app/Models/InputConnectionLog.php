<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InputConnectionLog extends Model
{
    protected $table = 'input_connection_logs';

    protected $fillable = [
        'input_connection_id',
        'topic_id',
        'status',
        'response_data',
        'error_message',
        'executed_at',
    ];

    protected $casts = [
        'response_data' => 'array',
        'executed_at' => 'datetime',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(InputConnection::class, 'input_connection_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}