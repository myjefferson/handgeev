<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id',
        'key_name', 
        'value',
        'type',
        'is_visible',
        'order'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * Formata o nome da chave para o padrão snake_case
     */
    public static function formatKeyName($keyName)
    {
        // Validação básica do input
        if (!is_string($keyName) || empty(trim($keyName))) {
            throw new \InvalidArgumentException('Key name must be a non-empty string');
        }
        
        // Remove null bytes e caracteres de controle (prevenção contra response splitting)
        $cleaned = preg_replace('/[\x00-\x1F\x7F]/u', '', $keyName);
        
        // Remove caracteres especiais perigosos, mantém apenas letras, números e espaços
        $cleaned = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $cleaned);
        
        // Limita o tamanho (prevenção contra DoS)
        $cleaned = mb_substr($cleaned, 0, 255, 'UTF-8');
        
        // Converte para minúsculas
        $lowercase = mb_strtolower($cleaned, 'UTF-8');
        
        // Substitui espaços por underscores
        $with_underscores = preg_replace('/\s+/', '_', $lowercase);
        
        // Remove underscores e hífens duplicados
        $single_separators = preg_replace('/[_\-]+/', '_', $with_underscores);
        
        // Remove underscores no início e no fim
        $trimmed = trim($single_separators, '_-');
        
        // Garante que não está vazio após o processamento
        if (empty($trimmed)) {
            throw new \InvalidArgumentException('Invalid key name format');
        }
        
        return $trimmed;
    }

    public function setKeyNameAttribute($value)
    {
        $this->attributes['key_name'] = self::formatKeyName($value);
    }

    // Regras de validação DINÂMICAS baseadas no plano do usuário
    public static function getValidationRules()
    {
        $user = Auth::user();
        $allowedTypes = ['text']; // Padrão: apenas text para free
        
        if ($user && !$user->isFree()) {
            $allowedTypes = ['text', 'boolean', 'number'];
        }

        return [
            'topic_id' => 'required|exists:topics,id',
            'key_name' => 'required|string|max:255',
            'value' => 'nullable|string',
            'type' => 'required|in:' . implode(',', $allowedTypes),
            'is_visible' => 'sometimes|boolean',
        ];
    }

    /**
     * Obter tipos de campo permitidos baseados no plano do usuário
     */
    public static function getAllowedTypes($user = null, $workspace = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        if (!$user) {
            return ['text']; // Fallback padrão
        }

        $plan = $user->getPlan();
        
        // Tipos base por plano
        $baseTypes = [
            'free' => ['text'],
            'start' => ['text', 'number', 'boolean'],
            'pro' => ['text', 'number', 'boolean', 'email', 'url'],
            'premium' => ['text', 'number', 'boolean', 'email', 'url', 'date', 'json'],
            'admin' => ['text', 'number', 'boolean', 'email', 'url', 'date', 'json']
        ];

        $planName = strtolower($plan->name ?? 'free');
        return $baseTypes[$planName] ?? $baseTypes['free'];
    }

    /**
     * Obter tipos com labels amigáveis
     */
    public static function getAllowedTypesWithLabels($user = null)
    {
        $types = self::getAllowedTypes($user);
        
        $labels = [
            'text' => 'Texto',
            'number' => 'Número',
            'boolean' => 'Verdadeiro/Falso',
            'email' => 'Email',
            'url' => 'URL',
            'date' => 'Data',
            'json' => 'JSON'
        ];

        $result = [];
        foreach ($types as $type) {
            $result[$type] = $labels[$type] ?? ucfirst($type);
        }

        return $result;
    }

    /**
     * Verificar se um tipo é permitido para o usuário
     */
    public static function isTypeAllowed($type, $user = null)
    {
        $allowedTypes = self::getAllowedTypes($user);
        return in_array($type, $allowedTypes);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // Método para validar valor baseado no tipo
    public function validateValue($value)
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
            case 'number':
                return is_numeric($value);
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            case 'date':
                return strtotime($value) !== false;
            case 'json':
                if (is_string($value)) {
                    json_decode($value);
                    return json_last_error() === JSON_ERROR_NONE;
                }
                return false;
            case 'text':
            default:
                return is_string($value);
        }
    }

    // Método para formatar valor baseado no tipo
    public function formatValue($value)
    {
        switch ($this->type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
            case 'number':
                return strval($value);
            case 'email':
            case 'url':
            case 'date':
            case 'text':
            default:
                return strval($value);
        }
    }
}