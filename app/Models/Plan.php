<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'max_workspaces',
        'max_topics',
        'max_fields',
        'max_domains',
        'can_export',
        'can_use_api',
        'is_active',
        'api_requests_per_minute',
        'api_requests_per_hour',
        'api_requests_per_day',
        'burst_requests'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_workspaces' => 'integer',
        'max_topics' => 'integer',
        'max_fields' => 'integer',
        'max_domains' => 'integer',
        'can_export' => 'boolean',
        'can_use_api' => 'boolean',
        'is_active' => 'boolean',
        'api_requests_per_minute' => 'integer',
        'api_requests_per_hour' => 'integer',
        'api_requests_per_day' => 'integer',
        'burst_requests' => 'integer'
    ];

    // Escopos úteis
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePaid($query)
    {
        return $query->where('price', '>', 0);
    }

    public function scopeFree($query)
    {
        return $query->where('price', 0);
    }

    // Métodos helper
    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function hasUnlimitedWorkspaces(): bool
    {
        return $this->max_workspaces === 0;
    }

    public function hasUnlimitedTopics(): bool
    {
        return $this->max_topics === 0;
    }

    public function hasUnlimitedFields(): bool
    {
        return $this->max_fields === 0;
    }
}
