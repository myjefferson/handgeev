<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class WorkspaceController extends Controller
{
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

        return view('pages.dashboard.workspaces.index', compact('workspace'));
    }

    /**
 * Mostra o formulário para criar um novo workspace.
     */
    // public function create()
    // {
    //     return view('workspaces.create');
    // }

    /**
     * Armazena um novo workspace no banco de dados.
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            // Converte o checkbox para boolean
            $data['is_published'] = $request->has('is_published');
            
            $validatedData = Validator::make($data, Workspace::$rules)->validate();
            $workspace = auth()->user()->workspaces()->create($validatedData);
            // 4. Cria um tópico padrão para o novo workspace.
            $workspace->topics()->create([
                'order' => 1,
                'title' => 'First Topic',
            ]);
            return redirect()->route('dashboard.home')->with('success', 'Workspace criado com sucesso!');
        } catch (ValidationException $e) {
            // return redirect()->back()->withErrors($e->errors())->withInput();
            return $e->errors();
        }
    }

    /**
     * Mostra um único workspace, se ele pertencer ao usuário.
     */
    // public function show(Workspace $workspace)
    // {
    //     if ($workspace->user_id !== auth()->user()->id) {
    //         abort(403);
    //     }

    //     return view('workspaces.show', compact('workspace'));
    // }

    /**
     * Mostra o formulário para editar um workspace existente.
     */
    public function edit(Workspace $workspace)
    {
        if ($workspace->user_id !== auth()->user()->id) {
            abort(403);
        }

        return view('workspaces.edit', compact('workspace'));
    }

    /**
     * Atualiza os dados de um workspace existente.
     */
    public function update(Request $request, Workspace $workspace)
    {
        if ($workspace->user_id !== auth()->user()->id) {
            abort(403);
        }

        try {
            $validated = $request->validate([
                'title' => 'string|max:100',
                'type_workspace_id' => 'integer|exists:type_workspaces,id',
                'is_published' => 'boolean',
            ]);

            $workspace->update($validated);

            return redirect()->route('workspaces.index')->with('success', 'Workspace atualizado com sucesso!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Exclui um workspace.
     */
    public function destroy(Workspace $workspace)
    {
        if ($workspace->user_id !== auth()->user()->id) {
            abort(403);
        }

        $workspace->delete();

        return redirect()->route('workspaces.index')->with('success', 'Workspace excluído com sucesso!');
    }
}
