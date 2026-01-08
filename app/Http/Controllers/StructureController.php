<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Structure;
use App\Models\StructureField;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;

class StructureController extends Controller
{
    public function index()
    {
        $structures = Structure::withCount(['fields', 'topics'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Dashboard/Structures/Structures', [
            'structures' => $structures
        ]);
    }

    public function show(Structure $structure)
    {        
        $structure->load(['fields' => function($query) {
            $query->orderBy('order');
        }, 'user']);
        
        return Inertia::render('Dashboard/Structures/Details', [
            'structure' => $structure,
            'topics' => $structure->topics()->with('workspace')->get()
        ]);
    }

    public function create()
    {
        // Verificar se o usuário pode criar mais workspaces
        if (!auth()->user()->canCreateStructure()) {
            return redirect()->back()->with('info', 'Você atingiu o limite de estruturas. Faça upgrade e aproveite mais.');
        }

        return Inertia::render('Dashboard/Structures/FormStructure', [
            'structure' => null,
            'mode' => 'create'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
            'fields' => 'required|array|min:1',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string|in:text,number,decimal,boolean,date,datetime,email,url,json',
            'fields.*.default_value' => 'nullable|string|max:500',
            'fields.*.is_required' => 'boolean',
            'fields.*.order' => 'required|integer|min:0'
        ]);

        // Verificar se o usuário pode criar mais workspaces
        if (!auth()->user()->canCreateStructure()) {
            return redirect()->back()->with('warning', 'Você atingiu o limite de estruturas. Faça upgrade e aproveite mais.');
        }

        try {
            $structure = Structure::create([
                'user_id' => auth()->id(),
                'name' => $validated['name'],
                'description' => $validated['description'],
                'is_public' => $validated['is_public'] ?? false,
            ]);

            foreach ($validated['fields'] as $fieldData) {
                $structure->fields()->create($fieldData);
            }

            return redirect()->route('structures.show')
                ->with('success', 'Estrutura criada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao criar estrutura: ' . $e->getMessage());
        }
    }

    public function edit(Structure $structure)
    {
        $structure->load(['fields' => function($query) {
            $query->orderBy('order');
        }]);

        return Inertia::render('Dashboard/Structures/FormStructure', [
            'structure' => $structure,
            'mode' => 'edit'
        ]);
    }

    public function update(Request $request, Structure $structure)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',

            'fields' => 'required|array|min:1',

            'fields.*.id' => 'nullable|integer|exists:structure_fields,id',
            'fields.*.name' => 'required|string|max:255',
            'fields.*.type' => 'required|string|in:text,number,decimal,boolean,date,datetime,email,url,json',
            'fields.*.default_value' => 'nullable|string|max:500',
            'fields.*.is_required' => 'boolean',
            'fields.*.order' => 'required|integer|min:0'
        ]);

        try {
            \DB::transaction(function () use ($structure, $validated) {

                // Atualiza estrutura
                $structure->update([
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                    'is_public' => $validated['is_public'] ?? false,
                ]);

                // IDs enviados pelo front
                $incomingIds = collect($validated['fields'])
                    ->pluck('id')
                    ->filter()
                    ->values();

                // Remove APENAS campos que não vieram mais
                $structure->fields()
                    ->whereNotIn('id', $incomingIds)
                    ->delete();

                foreach ($validated['fields'] as $fieldData) {

                    if (!empty($fieldData['id'])) {
                        // 🔄 UPDATE
                        $structure->fields()
                            ->where('id', $fieldData['id'])
                            ->update([
                                'name' => $fieldData['name'],
                                'type' => $fieldData['type'],
                                'default_value' => $fieldData['default_value'] ?? null,
                                'is_required' => $fieldData['is_required'] ?? false,
                                'order' => $fieldData['order'],
                            ]);
                    } else {
                        // ➕ CREATE
                        $structure->fields()->create([
                            'name' => $fieldData['name'],
                            'type' => $fieldData['type'],
                            'default_value' => $fieldData['default_value'] ?? null,
                            'is_required' => $fieldData['is_required'] ?? false,
                            'order' => $fieldData['order'],
                        ]);
                    }
                }
            });

            return back()->with('success', 'Estrutura atualizada com sucesso!');

        } catch (\Throwable $e) {
            return back()->with('error', 'Erro ao atualizar estrutura: ' . $e->getMessage());
        }
    }

    public function destroy(Structure $structure)
    {
        try {
            // Verifica se a estrutura está sendo usada em algum tópico
            if ($structure->topics()->exists()) {
                return back()->with('error', 'Não é possível excluir uma estrutura que está sendo usada em tópicos.');
            }

            $structure->delete();

            return back()->with('success', 'Estrutura excluída com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir estrutura: ' . $e->getMessage());
        }
    }

    /**
     * Obtém estruturas disponíveis para o usuário
     */
    public function getAvailableStructures(Request $request)
    {
        try {
            $user = Auth::user();

            $structures = Structure::where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                ->orWhere('is_public', true);
            })
            ->with(['fields' => fn($q) => $q->orderBy('order')])
            ->withCount('fields')
            ->orderBy('name')
            ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'structures' => $structures
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar estruturas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Structure $structure)
    {
        try {
            // Verifica se o usuário pode exportar a estrutura
            $user = Auth::user();
            
            if (!$structure->canBeUsedBy($user)) {
                Log::warning('Tentativa de exportação não autorizada', [
                    'user_id' => $user->id,
                    'structure_id' => $structure->id,
                    'structure_owner' => $structure->user_id
                ]);
                
                return response()->json([
                    'error' => 'Você não tem permissão para exportar esta estrutura.'
                ], 403);
            }

            // Carrega os campos ordenados
            $structure->load(['fields' => function ($query) {
                $query->orderBy('order');
            }, 'user']);

            $exportData = [
                'export_version' => '1.0',
                'exported_at' => now()->toISOString(),
                'app_name' => config('app.name'),
                'structure' => [
                    'name' => $structure->name,
                    'description' => $structure->description,
                    'is_public' => $structure->is_public,
                    'fields' => $structure->fields->map(function ($field) {
                        return [
                            'name' => $field->name,
                            'type' => $field->type,
                            'default_value' => $field->default_value,
                            'is_required' => (bool) $field->is_required,
                            'order' => (int) $field->order,
                        ];
                    })->toArray(),
                ],
                'metadata' => [
                    'original_id' => $structure->id,
                    'original_creator' => $structure->user->name ?? 'Unknown',
                    'fields_count' => $structure->fields->count(),
                    'topics_count' => $structure->topics_count ?? 0,
                ]
            ];

            $fileName = Str::slug($structure->name) . '-structure-' . now()->format('Y-m-d-H-i') . '.json';

            return response()->json($exportData, 200, [
                'Content-Type' => 'application/json; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Access-Control-Expose-Headers' => 'Content-Disposition',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao exportar estrutura', [
                'structure_id' => $structure->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Erro ao exportar estrutura: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Importa uma estrutura a partir de um arquivo JSON
     */
    public function import(Request $request)
    {
        // Validação do plano do usuário
        $user = Auth::user();
        $currentStructuresCount = Structure::where('user_id', $user->id)->count();
        
        if ($user->plan && $user->plan->structures <= $currentStructuresCount) {
            return redirect()->back()->withErrors([
                'message' => 'Você atingiu o limite de estruturas do seu plano.'
            ]);
        }

        // Validação do arquivo
        $validator = Validator::make($request->all(), [
            'import_file' => 'required|file|mimes:json|max:2048',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Lê e valida o conteúdo do arquivo
            $fileContent = file_get_contents($request->file('import_file')->getRealPath());
            $importData = json_decode($fileContent, true);

            // Valida a estrutura do arquivo de importação
            if (!isset($importData['structure']) || !isset($importData['structure']['fields'])) {
                throw new \Exception('Arquivo de importação inválido: estrutura não encontrada.');
            }

            // Cria a nova estrutura
            $structure = Structure::create([
                'user_id' => $user->id,
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'is_public' => $request->input('is_public', false),
            ]);

            // Importa os campos
            $fieldsData = $importData['structure']['fields'];
            foreach ($fieldsData as $fieldData) {
                StructureField::create([
                    'structure_id' => $structure->id,
                    'name' => $fieldData['name'],
                    'type' => $fieldData['type'],
                    'default_value' => $fieldData['default_value'] ?? null,
                    'is_required' => $fieldData['is_required'] ?? false,
                    'order' => $fieldData['order'] ?? 0,
                ]);
            }

            return redirect()->route('structures.show', $structure->id)
                ->with('success', 'Estrutura importada com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'import_file' => 'Erro ao importar arquivo: ' . $e->getMessage()
            ])->withInput();
        }
    }
}