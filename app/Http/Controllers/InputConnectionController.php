<?php

namespace App\Http\Controllers;

use App\Models\InputConnection;
use App\Models\Workspace;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InputConnectionController extends Controller
{
    public function index(Workspace $workspace)
    {
        // Verificar se o usuário tem acesso ao workspace
        if (!$workspace->users->contains(Auth::id())) {
            abort(403);
        }

        $connections = InputConnection::where('workspace_id', $workspace->id)
            ->with(['structure', 'triggerField', 'source', 'mappings.targetField'])
            ->get();

        return Inertia::render('Dashboard/InputConnections/Index', [
            'workspace' => $workspace,
            'connections' => $connections
        ]);
    }

    public function create(Workspace $workspace)
    {
        // Verificar se o usuário é o dono do workspace
        if ($workspace->user_id !== Auth::id()) {
            abort(403);
        }

        // Carregar estruturas do usuário atual ou públicas
        $structures = Structure::where('user_id', Auth::id())
            ->orWhere('is_public', true)
            ->with('fields')
            ->orderBy('name')
            ->get();

        // Nota: Se houver um relacionamento entre Structure e Workspace (ex: structures.workspace_id), 
        // então também podemos filtrar por: where('workspace_id', $workspace->id)
        // Mas pelo modelo Structure fornecido, não há workspace_id.

        return Inertia::render('Dashboard/ApiManagement/GeevStudio/InputConnectionsCreate', [
            'workspace' => $workspace,
            'structures' => $structures
        ]);
    }

    public function store(Request $request, Workspace $workspace)
    {
        // Validação
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'structure_id' => 'required|exists:structures,id',
            'trigger_field_id' => 'nullable|exists:structure_fields,id',
            'is_active' => 'boolean',
            'timeout_seconds' => 'integer|min:1|max:300',
            'prevent_loops' => 'boolean',
            'source' => 'required|array',
            'source.type' => 'required|in:rest_api,webhook,csv,excel,form',
            'source.config' => 'required|array',
            'mappings' => 'required|array',
            'mappings.*.source_field' => 'required|string',
            'mappings.*.target_field_id' => 'required|exists:structure_fields,id',
            'mappings.*.transformation' => 'nullable|string',
            'mappings.*.is_required' => 'boolean',
        ]);

        // Verificar se a estrutura pertence ao workspace
        $structure = Structure::find($validated['structure_id']);
        if (!$structure || $structure->workspace_id !== $workspace->id) {
            return back()->withErrors(['structure_id' => 'Estrutura não pertence a este workspace.']);
        }

        $connection = InputConnection::create([
            'workspace_id' => $workspace->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'structure_id' => $validated['structure_id'],
            'trigger_field_id' => $validated['trigger_field_id'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'timeout_seconds' => $validated['timeout_seconds'] ?? 30,
            'prevent_loops' => $validated['prevent_loops'] ?? true,
        ]);

        // Criar fonte - ajuste para usar o campo correto da tabela
        $connection->source()->create([
            'source_type' => $validated['source']['type'], // Mudar para 'source_type'
            'config' => $validated['source']['config'],
        ]);

        // Criar mapeamentos
        foreach ($validated['mappings'] as $mapping) {
            $connection->mappings()->create([
                'source_field' => $mapping['source_field'],
                'target_field_id' => $mapping['target_field_id'],
                'transformation_type' => $mapping['transformation'] ?? null,
                'is_required' => $mapping['is_required'] ?? false,
            ]);
        }

        return redirect()->route('workspaces.input-connections.index', $workspace->id)
            ->with('success', 'Conexão criada com sucesso!');
    }

    public function show(Workspace $workspace, InputConnection $connection)
    {
        // Verificar se a conexão pertence ao workspace
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        return Inertia::render('Dashboard/InputConnections/Show', [
            'workspace' => $workspace,
            'connection' => $connection->load(['structure', 'triggerField', 'source', 'mappings.targetField'])
        ]);
    }

    public function edit(Workspace $workspace, InputConnection $connection)
    {
        // Verificar se a conexão pertence ao workspace
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        // Carregar estruturas disponíveis
        $structures = Structure::whereHas('workspace', function($query) use ($workspace) {
            $query->where('workspace_id', $workspace->id);
        })->with('fields')->get();

        $connection->load(['source', 'mappings.targetField']);

        return Inertia::render('Dashboard/InputConnections/Edit', [
            'workspace' => $workspace,
            'structures' => $structures,
            'connection' => $connection
        ]);
    }

    public function update(Request $request, Workspace $workspace, InputConnection $connection)
    {
        // Verificar se a conexão pertence ao workspace
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'structure_id' => 'sometimes|required|exists:structures,id',
            'trigger_field_id' => 'nullable|exists:structure_fields,id',
            'is_active' => 'boolean',
            'timeout_seconds' => 'integer|min:1|max:300',
            'prevent_loops' => 'boolean',
            'source' => 'sometimes|required|array',
            'source.type' => 'sometimes|required|in:rest_api,webhook,csv,excel,form',
            'source.config' => 'sometimes|required|array',
            'mappings' => 'sometimes|required|array',
            'mappings.*.source_field' => 'required|string',
            'mappings.*.target_field_id' => 'required|exists:structure_fields,id',
            'mappings.*.transformation' => 'nullable|string',
            'mappings.*.is_required' => 'boolean',
        ]);

        $connection->update([
            'name' => $validated['name'] ?? $connection->name,
            'description' => $validated['description'] ?? $connection->description,
            'structure_id' => $validated['structure_id'] ?? $connection->structure_id,
            'trigger_field_id' => $validated['trigger_field_id'] ?? $connection->trigger_field_id,
            'is_active' => $validated['is_active'] ?? $connection->is_active,
            'timeout_seconds' => $validated['timeout_seconds'] ?? $connection->timeout_seconds,
            'prevent_loops' => $validated['prevent_loops'] ?? $connection->prevent_loops,
        ]);

        if (isset($validated['source'])) {
            $connection->source()->updateOrCreate(
                ['input_connection_id' => $connection->id],
                [
                    'source_type' => $validated['source']['type'],
                    'config' => $validated['source']['config'],
                ]
            );
        }

        if (isset($validated['mappings'])) {
            // Remover mapeamentos antigos e criar novos
            $connection->mappings()->delete();
            foreach ($validated['mappings'] as $mapping) {
                $connection->mappings()->create([
                    'source_field' => $mapping['source_field'],
                    'target_field_id' => $mapping['target_field_id'],
                    'transformation_type' => $mapping['transformation'] ?? null,
                    'is_required' => $mapping['is_required'] ?? false,
                ]);
            }
        }

        return redirect()->route('workspaces.input-connections.index', $workspace->id)
            ->with('success', 'Conexão atualizada com sucesso!');
    }

    public function destroy(Workspace $workspace, InputConnection $connection)
    {
        // Verificar se a conexão pertence ao workspace
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        $connection->delete();

        return back()->with('success', 'Conexão excluída com sucesso!');
    }

    // Método para exibir logs
    public function logs(Workspace $workspace, InputConnection $connection)
    {
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        $logs = $connection->logs()
            ->with('topic')
            ->orderBy('executed_at', 'desc')
            ->paginate(20);

        return Inertia::render('Dashboard/InputConnections/Logs', [
            'workspace' => $workspace,
            'connection' => $connection,
            'logs' => $logs
        ]);
    }

    // Método para executar a conexão manualmente
    public function execute(Workspace $workspace, InputConnection $connection)
    {
        if ($connection->workspace_id != $workspace->id) {
            abort(404);
        }

        // Aqui você pode chamar o job de execução
        // ExecuteInputConnectionJob::dispatch($connection);
        
        return back()->with('success', 'Conexão executada com sucesso!');
    }
}