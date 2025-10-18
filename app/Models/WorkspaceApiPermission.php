<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceApiPermission extends Model
{
    protected $fillable = [
        'workspace_id',
        'endpoint',
        'allowed_methods'
    ];

    protected $casts = [
        'allowed_methods' => 'array'
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public static function getDefaultMethods($plan)
    {
        $defaults = [
            'free' => [
                'workspace' => ['GET'],
                'topics' => ['GET'],
                'fields' => ['GET']
            ],
            'start' => [
                'workspace' => ['GET', 'PUT'],
                'topics' => ['GET', 'POST'],
                'fields' => ['GET', 'POST']
            ],
            'pro' => [
                'workspace' => ['GET', 'PUT', 'PATCH'],
                'topics' => ['GET', 'POST', 'PUT', 'DELETE'],
                'fields' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']
            ],
            'premium' => [
                'workspace' => ['GET', 'PUT', 'PATCH'],
                'topics' => ['GET', 'POST', 'PUT', 'DELETE'],
                'fields' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']
            ],
            'admin' => [
                'workspace' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
                'topics' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
                'fields' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']
            ]
        ];

        return $defaults[strtolower($plan)] ?? $defaults['free'];
    }
}