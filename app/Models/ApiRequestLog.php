<?php
// app/Models/ApiRequestLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiRequestLog extends Model
{
    use HasFactory;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}