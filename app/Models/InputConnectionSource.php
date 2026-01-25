<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InputConnectionSource extends Model
{
    protected $table = 'input_connection_sources';

    protected $fillable = [
        'input_connection_id',
        'source_type',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    // Tipos de fonte suportados
    const TYPE_REST_API = 'rest_api';
    const TYPE_WEBHOOK = 'webhook';
    const TYPE_CSV = 'csv';
    const TYPE_EXCEL = 'excel';
    const TYPE_FORM = 'form';

    public static function getSourceTypes(): array
    {
        return [
            self::TYPE_REST_API => 'API REST',
            self::TYPE_WEBHOOK => 'Webhook',
            self::TYPE_CSV => 'Arquivo CSV',
            self::TYPE_EXCEL => 'Arquivo Excel',
            self::TYPE_FORM => 'Formulário Externo',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(InputConnection::class, 'input_connection_id');
    }

    /**
     * Verifica se é uma fonte REST API
     */
    public function isRestApi(): bool
    {
        return $this->source_type === self::TYPE_REST_API;
    }

    /**
     * Obtém a configuração específica para REST API
     */
    public function getRestApiConfig(): array
    {
        if (!$this->isRestApi()) {
            return [];
        }

        return [
            'url' => $this->config['url'] ?? '',
            'method' => $this->config['method'] ?? 'GET',
            'headers' => $this->config['headers'] ?? [],
            'parameters' => $this->config['parameters'] ?? [],
            'authentication' => $this->config['authentication'] ?? 'none',
            'auth_type' => $this->config['auth_type'] ?? 'bearer',
            'api_key' => $this->config['api_key'] ?? '',
            'username' => $this->config['username'] ?? '',
            'password' => $this->config['password'] ?? '',
            'timeout' => $this->config['timeout'] ?? 30,
        ];
    }
}