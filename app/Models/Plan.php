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
        'can_export',
        'can_use_api',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_workspaces' => 'integer',
        'max_topics' => 'integer',
        'max_fields' => 'integer',
        'can_export' => 'boolean',
        'can_use_api' => 'boolean',
        'is_active' => 'boolean'
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
