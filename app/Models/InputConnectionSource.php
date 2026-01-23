<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InputConnectionSource extends Model
{
    protected $table = 'input_connection_sources';

    protected $fillable = [
        'input_connection_id',
        'type',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(InputConnection::class, 'input_connection_id');
    }
}