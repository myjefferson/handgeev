<?php

namespace App\Services;

use App\Models\InputConnection;
use App\Models\InputConnectionLog;
use App\Models\Topic;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InputConnectionService
{
    /**
     * Executa uma conexão de entrada para um tópico específico
     */
    public function executeConnection(InputConnection $connection, Topic $topic): array
    {
        // Verificar se a conexão pode ser executada
        if (!$connection->canExecute()) {
            return [
                'success' => false,
                'message' => 'Conexão não está configurada corretamente'
            ];
        }

        // Criar log de execução
        $log = InputConnectionLog::create([
            'input_connection_id' => $connection->id,
            'topic_id' => $topic->id,
            'status' => InputConnectionLog::STATUS_PENDING,
            'executed_at' => now(),
        ]);

        try {
            // Obter dados da fonte externa
            $externalData = $this->fetchExternalData($connection, $topic);
            
            // Validar dados obtidos
            if (empty($externalData)) {
                throw new \Exception('Nenhum dado retornado da fonte externa');
            }

            // Aplicar mapeamentos
            $mappedData = $this->applyMappings($connection, $externalData);
            
            // Validar dados mapeados
            if (empty($mappedData)) {
                throw new \Exception('Nenhum dado mapeado após transformações');
            }

            // Atualizar tópico com dados mapeados
            $this->updateTopic($topic, $mappedData);

            // Registrar sucesso
            $log->markAsSuccess($externalData);

            return [
                'success' => true,
                'message' => 'Conexão executada com sucesso',
                'data' => $mappedData,
                'log_id' => $log->id,
            ];

        } catch (\Exception $e) {
            // Registrar erro
            $log->markAsError($e->getMessage());

            Log::error('Erro na execução da conexão', [
                'connection_id' => $connection->id,
                'topic_id' => $topic->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Erro na execução: ' . $e->getMessage(),
                'log_id' => $log->id,
            ];
        }
    }

    /**
     * Busca dados da fonte externa
     */
    private function fetchExternalData(InputConnection $connection, Topic $topic): array
    {
        $source = $connection->source;
        
        switch ($source->source_type) {
            case InputConnectionSource::TYPE_REST_API:
                return $this->fetchFromRestApi($source, $topic);
                
            case InputConnectionSource::TYPE_WEBHOOK:
                return $this->fetchFromWebhook($source, $topic);
                
            case InputConnectionSource::TYPE_CSV:
                return $this->fetchFromCsv($source, $topic);
                
            case InputConnectionSource::TYPE_EXCEL:
                return $this->fetchFromExcel($source, $topic);
                
            case InputConnectionSource::TYPE_FORM:
                return $this->fetchFromForm($source, $topic);
                
            default:
                throw new \Exception('Tipo de fonte não suportado: ' . $source->source_type);
        }
    }

    /**
     * Busca dados de uma API REST
     */
    private function fetchFromRestApi(InputConnectionSource $source, Topic $topic): array
    {
        $config = $source->getRestApiConfig();
        
        // Preparar URL substituindo placeholders
        $url = $this->replacePlaceholders($config['url'], $topic);
        
        // Preparar parâmetros
        $parameters = $this->replacePlaceholdersInArray($config['parameters'], $topic);
        
        // Configurar autenticação
        $httpClient = Http::timeout($config['timeout']);
        
        // Adicionar headers
        if (!empty($config['headers'])) {
            $httpClient->withHeaders($config['headers']);
        }
        
        // Configurar autenticação
        $this->configureAuthentication($httpClient, $config);
        
        // Fazer requisição
        $response = $httpClient->{$config['method']}($url, $parameters);
        
        if ($response->successful()) {
            return $response->json();
        }
        
        throw new \Exception('Falha na requisição: ' . $response->status() . ' - ' . $response->body());
    }

    /**
     * Configura autenticação para API
     */
    private function configureAuthentication(&$httpClient, array $config): void
    {
        switch ($config['authentication']) {
            case 'api_key':
                if ($config['auth_type'] === 'bearer') {
                    $httpClient->withToken($config['api_key']);
                } else {
                    $httpClient->withHeaders(['X-API-Key' => $config['api_key']]);
                }
                break;
                
            case 'basic':
                $httpClient->withBasicAuth($config['username'], $config['password']);
                break;
                
            case 'oauth2':
                // Implementar OAuth2 se necessário
                break;
        }
    }

    /**
     * Busca dados de Webhook
     */
    private function fetchFromWebhook(InputConnectionSource $source, Topic $topic): array
    {
        // Similar à API REST, mas geralmente POST com dados do tópico
        $config = $source->config;
        
        $url = $this->replacePlaceholders($config['url'], $topic);
        $method = $config['method'] ?? 'POST';
        $data = $this->prepareWebhookData($topic, $config);
        
        $response = Http::post($url, $data);
        
        if ($response->successful()) {
            return $response->json();
        }
        
        throw new \Exception('Falha no webhook: ' . $response->status());
    }

    /**
     * Busca dados de arquivo CSV
     */
    private function fetchFromCsv(InputConnectionSource $source, Topic $topic): array
    {
        $config = $source->config;
        $url = $this->replacePlaceholders($config['url'], $topic);
        
        // Download do arquivo CSV
        $response = Http::get($url);
        
        if (!$response->successful()) {
            throw new \Exception('Falha ao baixar CSV');
        }
        
        $csvContent = $response->body();
        
        // Processar CSV
        return $this->parseCsv($csvContent, $config);
    }

    /**
     * Aplica mapeamentos aos dados externos
     */
    private function applyMappings(InputConnection $connection, array $externalData): array
    {
        $mappedData = [];
        
        foreach ($connection->mappings as $mapping) {
            try {
                // Obter valor da fonte externa usando dot notation
                $value = data_get($externalData, $mapping->source_field);
                
                // Aplicar transformação
                $transformedValue = $mapping->applyTransformation($value);
                
                // Validar campo obrigatório
                if ($mapping->is_required && empty($transformedValue)) {
                    throw new \Exception("Campo obrigatório '{$mapping->source_field}' está vazio");
                }
                
                // Armazenar valor mapeado
                $mappedData[$mapping->targetField->id] = $transformedValue;
                
            } catch (\Exception $e) {
                Log::warning('Erro no mapeamento', [
                    'source_field' => $mapping->source_field,
                    'error' => $e->getMessage(),
                ]);
                
                // Se for obrigatório, interrompe a execução
                if ($mapping->is_required) {
                    throw $e;
                }
            }
        }
        
        return $mappedData;
    }

    /**
     * Atualiza tópico com dados mapeados
     */
    private function updateTopic(Topic $topic, array $mappedData): void
    {
        $record = $topic->records()->first();
        
        if (!$record) {
            $record = $topic->records()->create(['order' => 1]);
        }
        
        foreach ($mappedData as $fieldId => $value) {
            $record->setFieldValue($fieldId, $value);
        }
    }

    /**
     * Substitui placeholders em string
     */
    private function replacePlaceholders(string $text, Topic $topic): string
    {
        preg_match_all('/\{(\w+(?:\.\w+)*)\}/', $text, $matches);
        
        foreach ($matches[1] as $fieldPath) {
            $value = $this->getTopicFieldValue($topic, $fieldPath);
            $text = str_replace('{' . $fieldPath . '}', $value, $text);
        }
        
        return $text;
    }

    /**
     * Obtém valor de campo do tópico
     */
    private function getTopicFieldValue(Topic $topic, string $fieldPath): string
    {
        $parts = explode('.', $fieldPath);
        $fieldName = $parts[0];
        
        $record = $topic->records()->first();
        if (!$record) {
            return '';
        }
        
        foreach ($record->fieldValues as $fieldValue) {
            if ($fieldValue->structureField->key_name === $fieldName) {
                return $fieldValue->field_value ?? '';
            }
        }
        
        return '';
    }

    /**
     * Substitui placeholders em array
     */
    private function replacePlaceholdersInArray(array $array, Topic $topic): array
    {
        array_walk_recursive($array, function (&$value) use ($topic) {
            if (is_string($value)) {
                $value = $this->replacePlaceholders($value, $topic);
            }
        });
        
        return $array;
    }
}