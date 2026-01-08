<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordFieldValue extends Model
{
    protected $table = 'record_field_values';

    protected $fillable = [
        'record_id',
        'structure_field_id',
        'field_value',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static $rules = [
        'record_id' => 'required|exists:topic_records,id',
        'structure_field_id' => 'required|exists:structure_fields,id',
        'field_value' => 'nullable|string',
    ];

    /**
     * Relacionamento: Um valor pertence a um registro
     */
    public function record(): BelongsTo
    {
        return $this->belongsTo(TopicRecord::class);
    }

    /**
     * Relacionamento: Um valor pertence a um campo de estrutura
     */
    public function structureField(): BelongsTo
    {
        return $this->belongsTo(StructureField::class);
    }

    /**
     * Acessor: Valor formatado baseado no tipo do campo
     */
    public function getFormattedValueAttribute()
    {
        if ($this->field_value === null) {
            return null;
        }

        // 🔐 Proteção absoluta
        if (!$this->structureField) {
            return $this->field_value;
        }

        $fieldType = $this->structureField->type ?? 'text';

        return match ($fieldType) {
            'boolean' => filter_var($this->field_value, FILTER_VALIDATE_BOOLEAN),
            'number'  => (int) $this->field_value,
            'decimal' => (float) $this->field_value,
            'json'    => json_decode($this->field_value, true) ?? $this->field_value,
            default   => $this->field_value,
        };
    }
}