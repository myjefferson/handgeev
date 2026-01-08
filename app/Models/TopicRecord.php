<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TopicRecord extends Model
{
    use HasFactory;

    protected $table = 'topic_records';

    protected $fillable = [
        'topic_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: Um registro pertence a um tópico
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Relacionamento: Um registro tem muitos valores de campo
     */
    public function fieldValues(): HasMany
    {
        return $this->hasMany(RecordFieldValue::class, 'record_id');
    }

    /**
     * Define o valor de um campo específico
     */
    public function setFieldValue($fieldId, $value)
    {
        $fieldValue = $this->fieldValues()
            ->where('structure_field_id', $fieldId)
            ->first();

        if ($fieldValue) {
            $fieldValue->update(['field_value' => $value]);
        } else {
            $this->fieldValues()->create([
                'structure_field_id' => $fieldId,
                'field_value' => $value,
            ]);
        }
    }

    /**
     * Obtém o valor de um campo específico
     */
    public function getFieldValue($fieldId)
    {
        $fieldValue = $this->fieldValues()
            ->where('structure_field_id', $fieldId)
            ->first();

        return $fieldValue ? $fieldValue->field_value : null;
    }

    public function field_values()
    {
        return $this->hasMany(RecordFieldValue::class, 'record_id', 'id');
    }
}