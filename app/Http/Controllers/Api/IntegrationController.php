<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use App\Models\InputConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class IntegrationController extends Controller
{
    /**
     * Busca CEP usando ViaCEP
     */
    public function viaCep(Request $request, Workspace $workspace)
    {
        $validator = Validator::make($request->all(), [
            'cep' => 'required|string|size:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $cep = preg_replace('/[^0-9]/', '', $request->cep);
        
        try {
            $response = Http::timeout(10)
                ->get("https://viacep.com.br/ws/{$cep}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['erro'])) {
                    return response()->json(['error' => 'CEP não encontrado'], 404);
                }
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'cep' => $data['cep'] ?? '',
                        'logradouro' => $data['logradouro'] ?? '',
                        'complemento' => $data['complemento'] ?? '',
                        'bairro' => $data['bairro'] ?? '',
                        'localidade' => $data['localidade'] ?? '',
                        'uf' => $data['uf'] ?? '',
                        'ibge' => $data['ibge'] ?? '',
                        'gia' => $data['gia'] ?? '',
                        'ddd' => $data['ddd'] ?? '',
                        'siafi' => $data['siafi'] ?? '',
                    ],
                ]);
            }
            
            return response()->json(['error' => 'Erro ao consultar ViaCEP'], 500);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Consulta placa de veículo
     */
    public function vehiclePlate(Request $request, Workspace $workspace)
    {
        $validator = Validator::make($request->all(), [
            'plate' => 'required|string|min:7|max:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $plate = strtoupper(preg_replace('/[^A-Z0-9]/', '', $request->plate));
        
        // Exemplo usando API pública (substituir por API real)
        try {
            // Esta é uma API de exemplo - implementar com API real
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.vehicle_api.key'),
                ])
                ->get('https://api.example.com/vehicles/' . $plate);
            
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                ]);
            }
            
            return response()->json(['error' => 'Placa não encontrada'], 404);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Integração com Google Places
     */
    public function googlePlaces(Request $request, Workspace $workspace)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:3',
            'type' => 'nullable|string|in:address,establishment',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $apiKey = config('services.google.api_key');
        
        if (!$apiKey) {
            return response()->json(['error' => 'API Key do Google não configurada'], 500);
        }

        try {
            $response = Http::timeout(10)
                ->get('https://maps.googleapis.com/maps/api/place/autocomplete/json', [
                    'input' => $request->query,
                    'types' => $request->type ?? 'address',
                    'key' => $apiKey,
                    'language' => 'pt-BR',
                    'components' => 'country:br',
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] !== 'OK') {
                    return response()->json(['error' => $data['status']], 400);
                }
                
                $places = array_map(function($place) {
                    return [
                        'description' => $place['description'],
                        'place_id' => $place['place_id'],
                    ];
                }, $data['predictions']);
                
                return response()->json([
                    'success' => true,
                    'data' => $places,
                ]);
            }
            
            return response()->json(['error' => 'Erro na consulta'], 500);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Google Geocoding
     */
    public function googleGeocoding(Request $request, Workspace $workspace)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $apiKey = config('services.google.api_key');
        
        if (!$apiKey) {
            return response()->json(['error' => 'API Key do Google não configurada'], 500);
        }

        try {
            $response = Http::timeout(10)
                ->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $request->address,
                    'key' => $apiKey,
                    'language' => 'pt-BR',
                    'region' => 'br',
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] !== 'OK' || empty($data['results'])) {
                    return response()->json(['error' => 'Endereço não encontrado'], 404);
                }
                
                $result = $data['results'][0];
                $components = [];
                
                foreach ($result['address_components'] as $component) {
                    $type = $component['types'][0];
                    $components[$type] = $component['long_name'];
                }
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'formatted_address' => $result['formatted_address'],
                        'latitude' => $result['geometry']['location']['lat'],
                        'longitude' => $result['geometry']['location']['lng'],
                        'components' => $components,
                    ],
                ]);
            }
            
            return response()->json(['error' => 'Erro na consulta'], 500);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Busca CNPJ na Receita WS
     */
    public function cnpjLookup(Request $request, Workspace $workspace)
    {
        $validator = Validator::make($request->all(), [
            'cnpj' => 'required|string|size:14',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $cnpj = preg_replace('/[^0-9]/', '', $request->cnpj);
        
        try {
            $response = Http::timeout(15)
                ->get("https://receitaws.com.br/v1/cnpj/{$cnpj}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['status']) && $data['status'] === 'ERROR') {
                    return response()->json(['error' => $data['message']], 400);
                }
                
                return response()->json([
                    'success' => true,
                    'data' => [
                        'cnpj' => $data['cnpj'] ?? '',
                        'nome' => $data['nome'] ?? '',
                        'fantasia' => $data['fantasia'] ?? '',
                        'tipo' => $data['tipo'] ?? '',
                        'abertura' => $data['abertura'] ?? '',
                        'situacao' => $data['situacao'] ?? '',
                        'logradouro' => $data['logradouro'] ?? '',
                        'numero' => $data['numero'] ?? '',
                        'complemento' => $data['complemento'] ?? '',
                        'bairro' => $data['bairro'] ?? '',
                        'municipio' => $data['municipio'] ?? '',
                        'uf' => $data['uf'] ?? '',
                        'cep' => $data['cep'] ?? '',
                        'email' => $data['email'] ?? '',
                        'telefone' => $data['telefone'] ?? '',
                        'atividade_principal' => $data['atividade_principal'][0]['text'] ?? '',
                    ],
                ]);
            }
            
            return response()->json(['error' => 'Erro ao consultar CNPJ'], 500);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Templates de conexões prontas
     */
    public function templates(Workspace $workspace)
    {
        $templates = [
            [
                'id' => 'viacep',
                'name' => 'ViaCEP - Consulta de CEP',
                'description' => 'Busca endereços pelo CEP brasileiro',
                'source_type' => 'rest_api',
                'config' => [
                    'url' => 'https://viacep.com.br/ws/{cep}/json/',
                    'method' => 'GET',
                    'headers' => [],
                    'parameters' => [],
                    'authentication' => 'none',
                    'timeout' => 10,
                ],
                'mappings' => [
                    [
                        'source_field' => 'cep',
                        'target_field_id' => null, // Será substituído
                        'transformation_type' => 'none',
                        'is_required' => true,
                    ],
                    [
                        'source_field' => 'logradouro',
                        'target_field_id' => null,
                        'transformation_type' => 'none',
                        'is_required' => false,
                    ],
                    [
                        'source_field' => 'complemento',
                        'target_field_id' => null,
                        'transformation_type' => 'none',
                        'is_required' => false,
                    ],
                    [
                        'source_field' => 'bairro',
                        'target_field_id' => null,
                        'transformation_type' => 'none',
                        'is_required' => false,
                    ],
                    [
                        'source_field' => 'localidade',
                        'target_field_id' => null,
                        'transformation_type' => 'none',
                        'is_required' => false,
                    ],
                    [
                        'source_field' => 'uf',
                        'target_field_id' => null,
                        'transformation_type' => 'uppercase',
                        'is_required' => false,
                    ],
                ],
            ],
            [
                'id' => 'google_geocoding',
                'name' => 'Google Geocoding',
                'description' => 'Converte endereços em coordenadas geográficas',
                'source_type' => 'rest_api',
                'config' => [
                    'url' => 'https://maps.googleapis.com/maps/api/geocode/json',
                    'method' => 'GET',
                    'headers' => [],
                    'parameters' => [
                        'address' => '{endereco_completo}',
                        'key' => '[SUA_API_KEY]',
                        'language' => 'pt-BR',
                    ],
                    'authentication' => 'none',
                    'timeout' => 10,
                ],
                'mappings' => [
                    [
                        'source_field' => 'results.0.formatted_address',
                        'target_field_id' => null,
                        'transformation_type' => 'none',
                        'is_required' => false,
                    ],
                    [
                        'source_field' => 'results.0.geometry.location.lat',
                        'target_field_id' => null,
                        'transformation_type' => 'to_number',
                        'is_required' => false,
                    ],
                    [
                        'source_field' => 'results.0.geometry.location.lng',
                        'target_field_id' => null,
                        'transformation_type' => 'to_number',
                        'is_required' => false,
                    ],
                ],
            ],
            // Adicionar mais templates conforme necessário
        ];

        return response()->json([
            'success' => true,
            'templates' => $templates,
        ]);
    }

    /**
     * Aplica template de conexão
     */
    public function applyTemplate(Request $request, Workspace $workspace)
    {
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|string',
            'structure_id' => 'required|exists:structures,id',
            'field_mappings' => 'required|array',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Obter template
        $template = collect($this->templates($workspace)['templates'])
            ->firstWhere('id', $request->template_id);

        if (!$template) {
            return response()->json(['error' => 'Template não encontrado'], 404);
        }

        // Criar conexão com template
        $connection = InputConnection::create([
            'workspace_id' => $workspace->id,
            'structure_id' => $request->structure_id,
            'name' => $request->name,
            'description' => $template['description'],
            'is_active' => true,
            'timeout_seconds' => 30,
            'prevent_loops' => true,
        ]);

        // Ajustar mapeamentos com campos reais
        $mappings = [];
        foreach ($template['mappings'] as $mapping) {
            $targetFieldId = $request->field_mappings[$mapping['source_field']] ?? null;
            
            if ($targetFieldId) {
                $mappings[] = [
                    'source_field' => $mapping['source_field'],
                    'target_field_id' => $targetFieldId,
                    'transformation_type' => $mapping['transformation_type'],
                    'is_required' => $mapping['is_required'],
                ];
            }
        }

        // Criar fonte
        $connection->source()->create([
            'source_type' => $template['source_type'],
            'config' => $template['config'],
        ]);

        // Criar mapeamentos
        foreach ($mappings as $mapping) {
            $connection->mappings()->create($mapping);
        }

        return response()->json([
            'success' => true,
            'message' => 'Template aplicado com sucesso',
            'connection_id' => $connection->id,
        ]);
    }
}