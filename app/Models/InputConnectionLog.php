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

    // Status possíveis
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_ERROR = 'error';
    const STATUS_TIMEOUT = 'timeout';

    public function connection(): BelongsTo
    {
        return $this->belongsTo(InputConnection::class, 'input_connection_id');
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Escopo para logs recentes
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('executed_at', '>=', now()->subDays($days));
    }

    /**
     * Escopo para logs de erro
     */
    public function scopeErrors($query)
    {
        return $query->where('status', self::STATUS_ERROR);
    }

    /**
     * Marca log como sucesso
     */
    public function markAsSuccess($responseData = null)
    {
        $this->update([
            'status' => self::STATUS_SUCCESS,
            'response_data' => $responseData,
            'executed_at' => now(),
        ]);
    }

    /**
     * Marca log como erro
     */
    public function markAsError($errorMessage)
    {
        $this->update([
            'status' => self::STATUS_ERROR,
            'error_message' => $errorMessage,
            'executed_at' => now(),
        ]);
    }
}