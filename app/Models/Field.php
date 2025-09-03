<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Field extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'topic_id',
        'key_name',
        'value',
        'is_visible',
        'order'
    ];

    public static $rules = [
        'topic_id' => 'required|integer|exists:topics,id',
        'key_name' => 'nullable|string|max:200',
        'value' => 'nullable|string',
        'is_visible' => 'boolean'
    ];

    /**
     * Relacionamento: Um campo pertence a um tópico
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Relacionamento: Um campo pertence a um workspace (através do tópico)
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class, 'topic.workspace_id');
    }
}
