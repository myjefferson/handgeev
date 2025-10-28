<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ApiRequestLog extends Model
{
    use HasFactory;

    protected $table = 'api_request_logs';

    protected $fillable = [
        'user_id',
        'workspace_id',
        'ip_address',
        'method',
        'endpoint',
        'response_code',
        'response_time',
        'user_agent'
    ];

    protected $casts = [
        'response_time' => 'integer',
        'response_code' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com workspace
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Scope para filtrar por workspace
     */
    public function scopeForWorkspace($query, $workspaceId)
    {
        return $query->where('workspace_id', $workspaceId);
    }

    /**
     * Scope para filtrar por usuário
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtrar por período
     */
    public function scopeForPeriod($query, $startDate, $endDate = null)
    {
        $endDate = $endDate ?? now();
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopePeakHour($query, $startDate = null)
    {
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        return $query
            ->selectRaw('EXTRACT(HOUR FROM created_at) as hour, COUNT(*) as count')
            ->groupByRaw('EXTRACT(HOUR FROM created_at)')
            ->orderByDesc('count');
    }

    // /**
    //  * Scope para estatísticas por hora
    //  */
    // public function scopeHourlyStats($query, $startDate = null)
    // {
    //     if ($startDate) {
    //         $query->whereDate('created_at', '>=', $startDate);
    //     }

    //     return $query
    //         ->selectRaw('EXTRACT(HOUR FROM created_at) as hour, COUNT(*) as count')
    //         ->groupByRaw('EXTRACT(HOUR FROM created_at)')
    //         ->orderBy('hour');
    // }
}