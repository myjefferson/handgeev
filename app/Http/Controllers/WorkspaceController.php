<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Topic;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Services\HashService;

class WorkspaceController extends Controller
{
    /**
     * Exibe a lista de todos os workspaces do usuário autenticado.
     */
    public function indexWorkspaces()
    {
        // $workspaces = Workspace::where('user_id', Auth::id())->get();
        // if (!$workspaces) {
        //     abort(404, 'Workspace não encontrado ou você não tem permissão para acessá-lo');
        // }

        $user = auth()->user();
    
        $workspaces = $user->workspaces()
            ->withCount('topics')
            ->orderBy('created_at', 'desc')
            ->get();

        // Agora usando o relacionamento correto
        $collaborations = $user->collaborations()
            ->with(['workspace' => function($query) {
                $query->withCount('topics');
            }])
            ->orderBy('joined_at', 'desc')
            ->get();
        return view('pages.dashboard.workspaces.my-workspaces', compact('workspaces', 'collaborations'));
    }
    /**
     * Exibe a lista de todos os workspaces do usuário autenticado.
     */
    public function index($id)
    {
        // Carrega o workspace com os tópicos e fields aninhados
        $workspace = Workspace::with(['topics' => function($query) {
                $query->orderBy('order')->with(['fields' => function($query) {
                    $query->orderBy('order');
                }]);
            }])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        // Se não encontrou o workspace, retorna 404
        if (!$workspace) {
            abort(404, 'Workspace não encontrado ou você não tem permissão para acessá-lo');
        }

        // Obter informações de limite de campos
        $user = Auth::user();
        $canAddMoreFields = $user->canAddMoreFields($workspace->id);
        $fieldsLimit = $user->getFieldsLimit();
        $currentFieldsCount = $user->getCurrentFieldsCount($workspace->id);
        $remainingFields = $user->getRemainingFieldsCount($workspace->id);
        
        return view('pages.dashboard.workspaces.workspace', compact(
            'workspace',
            'canAddMoreFields',
            'fieldsLimit',
            'currentFieldsCount',
            'remainingFields'
        ));
    }

    /**
     * Armazena um novo workspace no banco de dados.
     */
    public function store(Request $request)
    {
        try {
            $workspaceData = $request->all();
            // Converte o checkbox para boolean
            $workspaceData['is_published'] = $request->has('is_published');
            $workspaceData['workspace_hash_api'] = HashService::generateUniqueHash();            
            
            $validatedData = Validator::make($workspaceData, Workspace::$rules)->validate();
            $workspace = auth()->user()->workspaces()->create($validatedData);
            // 4. Cria um tópico padrão para o novo workspace.
            $workspace->topics()->create([
                'order' => 1,
                'title' => 'First Topic',
            ]);
            return redirect()->route('workspace.show', ['id' => $workspace->id])->with('success', 'Workspace criado com sucesso!');
        } catch (ValidationException $e) {
            // return redirect()->back()->withErrors($e->errors())->withInput();
            return $e->errors();
        }
    }

    /**
     * Atualiza os dados de um workspace existente.
     */
    public function update(Request $request, string $id)
    {        
        try {
            $workspace = Workspace::with(['topics.fields'])->findOrFail($id);
            
            if($workspace->user_id !== Auth::id()) {
                return response()->json([
                    'error' => 'Você não tem permissão para alterar este workspace'
                ], 403);
            }
            $validatedData = Validator::make($request->all(), Workspace::$rules)->validate();
            $workspace = $workspace->update($validatedData);

            return redirect()->route('workspace.show', ['id' => $id])->with('success', 'Workspace atualizado com sucesso!');
        } catch (ValidationException $e) {
            // return $e->errors();
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Exclui um workspace.
     */
    public function destroy(string $id)
    {
        try {
            $workspace = Workspace::with(['topics.fields'])->findOrFail($id);

            if($workspace->user_id !== Auth::id()) {
                return response()->json([
                    'error' => 'Você não tem permissão para excluir este workspace'
                ], 403);
            }

            // CORREÇÃO: Contar os campos antes de deletar
            $totalFields = 0;
            foreach ($workspace->topics as $topic) {
                $totalFields += $topic->fields->count();
            }

            // Primeiro: deletar todos os campos de todos os tópicos
            // Usando whereHas para eficiência
            Field::whereHas('topic', function($query) use ($id) {
                $query->where('workspace_id', $id);
            })->delete();

            // Segundo: deletar todos os tópicos do workspace
            Topic::where('workspace_id', $id)->delete();

            // Terceiro: deletar o workspace
            $workspace->delete();

            return redirect(route('workspaces.index'))->with([
                'success' => true,
                'message' => 'Workspace excluído com sucesso',
                'deleted_topics' => $workspace->topics->count(),
                'deleted_fields' => $totalFields
            ]);
        } catch(ModelNotFoundException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch(\Exception $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    
    public function stats(Workspace $workspace)
    {
        $this->authorize('view', $workspace);

        $stats = [
            'topics_count' => $workspace->topics()->count(),
            'fields_count' => $workspace->topics()->withCount('fields')->get()->sum('fields_count'),
            'views_count' => $workspace->views_count,
            'collaborators_count' => $workspace->collaborators()->count(),
        ];

        return response()->json($stats);
    }
}
