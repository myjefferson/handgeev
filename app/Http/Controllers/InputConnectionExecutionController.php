<?php

namespace App\Http\Controllers;

use App\Models\InputConnection;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InputConnectionExecutionController extends Controller
{
    public function execute(InputConnection $connection, Topic $topic)
    {
        // Verificar se a conexão está ativa
        if (!$connection->is_active) {
            return response()->json(['message' => 'Connection is not active.'], 400);
        }

        // Verificar se o tópico pertence à estrutura da conexão
        if ($topic->structure_id != $connection->structure_id) {
            return response()->json(['message' => 'Topic does not belong to the connection structure.'], 400);
        }

        // Obter a fonte da conexão
        $source = $connection->source;
        if (!$source) {
            return response()->json(['message' => 'Connection source not found.'], 400);
        }

        // Inicializar log
        $log = $connection->logs()->create([
            'topic_id' => $topic->id,
            'status' => 'pending',
            'executed_at' => now(),
        ]);

        try {
            // Obter dados da fonte externa
            $externalData = $this->fetchFromSource($source, $topic);

            // Aplicar transformações e mapeamentos
            $mappedData = $this->applyMappings($connection->mappings, $externalData);

            // Atualizar o tópico com os dados mapeados
            $this->updateTopic($topic, $mappedData);

            // Atualizar log com sucesso
            $log->update([
                'status' => 'success',
                'response_data' => $externalData,
            ]);

            return response()->json(['message' => 'Connection executed successfully.', 'data' => $mappedData]);

        } catch (\Exception $e) {
            $log->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Error executing connection: ' . $e->getMessage()], 500);
        }
    }

    private function fetchFromSource($source, $topic)
    {
        switch ($source->type) {
            case 'rest_api':
                // Configuração: url, method, headers, parameters, etc.
                $config = $source->config;
                $url = $config['url'];
                $method = $config['method'] ?? 'GET';
                $headers = $config['headers'] ?? [];
                $parameters = $config['parameters'] ?? [];

                // Substituir placeholders nos parâmetros (ex: {topic.field})
                $parameters = $this->replacePlaceholders($parameters, $topic);

                $response = Http::withHeaders($headers)->{$method}($url, $parameters);
                if ($response->successful()) {
                    return $response->json();
                } else {
                    throw new \Exception('Failed to fetch data from REST API: ' . $response->status());
                }
                break;

            // Implementar outros tipos (webhook, csv, excel, form) conforme necessário
            default:
                throw new \Exception('Source type not implemented.');
        }
    }

    private function replacePlaceholders($parameters, $topic)
    {
        // Obter os valores dos campos do tópico
        $fieldValues = [];
        foreach ($topic->records as $record) {
            foreach ($record->fieldValues as $fieldValue) {
                $fieldValues[$fieldValue->structureField->name] = $fieldValue->field_value;
            }
        }

        // Substituir placeholders no formato {field_name}
        array_walk_recursive($parameters, function (&$value) use ($fieldValues) {
            if (is_string($value) && preg_match('/\{(\w+)\}/', $value, $matches)) {
                $fieldName = $matches[1];
                if (isset($fieldValues[$fieldName])) {
                    $value = str_replace('{' . $fieldName . '}', $fieldValues[$fieldName], $value);
                }
            }
        });

        return $parameters;
    }

    private function applyMappings($mappings, $externalData)
    {
        $mappedData = [];

        foreach ($mappings as $mapping) {
            $sourceField = $mapping->source_field;
            $value = data_get($externalData, $sourceField);

            // Aplicar transformação, se existir
            if ($mapping->transformation) {
                $value = $this->applyTransformation($value, $mapping->transformation);
            }

            $mappedData[$mapping->targetField->name] = $value;
        }

        return $mappedData;
    }

    private function applyTransformation($value, $transformation)
    {
        switch ($transformation) {
            case 'trim':
                return trim($value);
            case 'uppercase':
                return strtoupper($value);
            case 'lowercase':
                return strtolower($value);
            // Adicionar mais transformações conforme necessário
            default:
                return $value;
        }
    }

    private function updateTopic($topic, $mappedData)
    {
        // Atualizar os registros do tópico com os dados mapeados
        // Assumindo que o tópico tem um único registro (ou o primeiro registro)
        $record = $topic->records()->first();
        if (!$record) {
            $record = $topic->records()->create(['order' => 1]);
        }

        foreach ($mappedData as $fieldName => $value) {
            // Encontrar o campo da estrutura pelo nome
            $field = $topic->structure->fields()->where('name', $fieldName)->first();
            if ($field) {
                $record->setFieldValue($field->id, $value);
            }
        }
    }
}