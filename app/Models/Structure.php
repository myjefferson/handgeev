<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Structure extends Model
{
    use HasFactory;

    protected $table = 'structures';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static $rules = [
        'user_id' => 'required|integer|exists:users,id',
        'name' => 'required|string|max:150',
        'description' => 'nullable|string',
        'is_public' => 'boolean',
    ];

    /**
     * Relacionamento: Uma estrutura pertence a um usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento: Uma estrutura tem muitos campos
     */
    public function fields(): HasMany
    {
        return $this->hasMany(StructureField::class)->orderBy('order');
    }

    /**
     * Relacionamento: Uma estrutura pode ser usada em muitos tópicos
     */
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
    }

    /**
     * Scope para estruturas públicas
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope para estruturas do usuário
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Verifica se a estrutura pode ser editada pelo usuário
     */
    public function canBeEditedBy(User $user): bool
    {
        return $this->user_id === $user->id || $user->is_admin;
    }

    /**
     * Verifica se a estrutura pode ser usada pelo usuário
     */
    public function canBeUsedBy(User $user): bool
    {
        return $this->is_public || $this->user_id === $user->id;
    }
}