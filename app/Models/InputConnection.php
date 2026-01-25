<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InputConnection extends Model
{
    protected $table = 'input_connections';

    protected $fillable = [
        'workspace_id',
        'structure_id',
        'topic_id',
        'name',
        'description',
        'is_active',
        'trigger_field_id',
        'timeout_seconds',
        'prevent_loops',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'prevent_loops' => 'boolean',
        'config' => 'array',
    ];

    protected $with = ['structure', 'source', 'mappings'];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    public function triggerField(): BelongsTo
    {
        return $this->belongsTo(StructureField::class, 'trigger_field_id');
    }

    public function source(): HasOne
    {
        return $this->hasOne(InputConnectionSource::class);
    }

    public function mappings(): HasMany
    {
        return $this->hasMany(InputConnectionMapping::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(InputConnectionLog::class)->latest('executed_at');
    }

    /**
     * Escopo para conexões ativas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Verifica se a conexão pode ser executada
     */
    public function canExecute(): bool
    {
        return $this->is_active && $this->source && $this->mappings->count() > 0;
    }
}