<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Workspace;

class Topic extends Model
{

    protected $table = 'topics';

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'workspace_id',
        'title',
        'order',
    ];

    public static $rules = [
        'workspace_id' => 'required|integer|exists:workspaces,id',
        'title' => 'required|string|max:200',
        'order' => 'required|integer',
    ];

    /**
     * Relacionamento: Um tópico pertence a um workspace
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Relacionamento: Um tópico tem muitos campos
     */
    public function fields(): HasMany
    {
        return $this->hasMany(Field::class)->orderBy('order');
    }
}
