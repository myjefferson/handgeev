<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InputConnectionMapping extends Model
{
    protected $table = 'input_connection_mappings';

    protected $fillable = [
        'input_connection_id',
        'source_field',
        'target_field_id',
        'transformation_type',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    // Tipos de transformação suportados
    const TRANSFORM_NONE = 'none';
    const TRANSFORM_TRIM = 'trim';
    const TRANSFORM_UPPERCASE = 'uppercase';
    const TRANSFORM_LOWERCASE = 'lowercase';
    const TRANSFORM_CAPITALIZE = 'capitalize';
    const TRANSFORM_NUMBER = 'to_number';
    const TRANSFORM_DATE = 'to_date';
    const TRANSFORM_JSON = 'to_json';
    const TRANSFORM_ARRAY = 'to_array';

    public static function getTransformations(): array
    {
        return [
            self::TRANSFORM_NONE => 'Nenhuma',
            self::TRANSFORM_TRIM => 'Remover espaços',
            self::TRANSFORM_UPPERCASE => 'Converter para MAIÚSCULAS',
            self::TRANSFORM_LOWERCASE => 'Converter para minúsculas',
            self::TRANSFORM_CAPITALIZE => 'Primeira letra maiúscula',
            self::TRANSFORM_NUMBER => 'Converter para número',
            self::TRANSFORM_DATE => 'Converter para data',
            self::TRANSFORM_JSON => 'Converter JSON para objeto',
            self::TRANSFORM_ARRAY => 'Converter para array',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(InputConnection::class, 'input_connection_id');
    }

    public function targetField(): BelongsTo
    {
        return $this->belongsTo(StructureField::class, 'target_field_id');
    }

    /**
     * Aplica transformação no valor
     */
    public function applyTransformation($value)
    {
        if (empty($this->transformation_type) || $this->transformation_type === self::TRANSFORM_NONE) {
            return $value;
        }

        switch ($this->transformation_type) {
            case self::TRANSFORM_TRIM:
                return is_string($value) ? trim($value) : $value;
            case self::TRANSFORM_UPPERCASE:
                return is_string($value) ? strtoupper($value) : $value;
            case self::TRANSFORM_LOWERCASE:
                return is_string($value) ? strtolower($value) : $value;
            case self::TRANSFORM_CAPITALIZE:
                return is_string($value) ? ucfirst($value) : $value;
            case self::TRANSFORM_NUMBER:
                return is_numeric($value) ? (float) $value : $value;
            case self::TRANSFORM_DATE:
                return $this->transformToDate($value);
            case self::TRANSFORM_JSON:
                return $this->transformJson($value);
            case self::TRANSFORM_ARRAY:
                return is_string($value) ? explode(',', $value) : (array) $value;
            default:
                return $value;
        }
    }

    private function transformToDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    private function transformJson($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
        }

        return $value;
    }
}