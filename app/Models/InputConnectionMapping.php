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
        'transformation',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(InputConnection::class, 'input_connection_id');
    }

    public function targetField(): BelongsTo
    {
        return $this->belongsTo(StructureField::class, 'target_field_id');
    }
}