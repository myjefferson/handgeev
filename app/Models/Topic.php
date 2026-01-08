<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    protected $table = 'topics';

    protected $fillable = [
        'workspace_id',
        'structure_id',
        'title',
        'description',
        'order'
    ];

    protected $casts = [
        'order' => 'integer'
    ];

    public static $rules = [
        'title' => 'required|string|max:255',
        'structure_id' => 'nullable|exists:structures,id',
        'workspace_id' => 'required|exists:workspaces,id',
    ];

    /**
     * Relacionamento: Um tópico pertence a um workspace
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function structure()
    {
        return $this->belongsTo(Structure::class, 'structure_id');
    }

    public function fields()
    {
        return $this->structure ? $this->structure->fields() : collect();
    }

    public function structureFields()
    {
        return $this->hasManyThrough(
            StructureField::class,  // modelo final
            Structure::class,       // modelo intermediário
            'id',                   // id da estrutura
            'structure_id',         // FK em structure_fields
            'structure_id',         // FK em topics
            'id'                    // id da estrutura
        );
    }

    public function topicRecords()
    {
        return $this->hasMany(TopicRecord::class);
    }

    /**
     * Relacionamento: Registros estruturados dentro do tópico
     */
    public function records(): HasMany
    {
        return $this->hasMany(TopicRecord::class)->orderBy('order', 'asc');
    }
}
