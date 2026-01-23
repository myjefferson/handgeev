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
        'name',
        'description',
        'is_active',
        'trigger_field_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

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
        return $this->hasMany(InputConnectionLog::class);
    }
}