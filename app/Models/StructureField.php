<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StructureField extends Model
{
    use HasFactory;

    protected $table = 'structure_fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'structure_id',
        'name',
        'type',
        'default_value',
        'is_required',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static $rules = [
        'structure_id' => 'required|integer|exists:structures,id',
        'name' => 'required|string|max:100',
        'type' => 'required|string|in:text,number,decimal,boolean,date,datetime,email,url,json',
        'default_value' => 'nullable|string',
        'is_required' => 'boolean',
        'order' => 'required|integer',
    ];

    /**
     * Tipos de campo disponíveis
     */
    public static function getAvailableTypes(): array
    {
        return [
            'text' => 'Texto',
            'number' => 'Número',
            'decimal' => 'Decimal',
            'boolean' => 'Booleano',
            'date' => 'Data',
            'datetime' => 'Data e Hora',
            'email' => 'E-mail',
            'url' => 'URL',
            'json' => 'JSON',
        ];
    }

    /**
     * Relacionamento: Um campo pertence a uma estrutura
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    /**
     * Get the human-readable type name
     */
    public function getTypeNameAttribute(): string
    {
        return self::getAvailableTypes()[$this->type] ?? $this->type;
    }

    /**
     * Verifica se o campo tem um valor padrão definido
     */
    public function hasDefaultValue(): bool
    {
        return !empty($this->default_value);
    }

    /**
     * Obtém o valor padrão formatado de acordo com o tipo
     */
    public function getFormattedDefaultValue()
    {
        if (empty($this->default_value)) {
            return null;
        }

        return match ($this->type) {
            'boolean' => filter_var($this->default_value, FILTER_VALIDATE_BOOLEAN),
            'number', 'decimal' => (float) $this->default_value,
            'json' => json_decode($this->default_value, true),
            default => $this->default_value,
        };
    }
}