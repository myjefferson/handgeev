<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class WorkspaceSharedController extends Controller
{
    // Rota para a interface de visualização compartilhada
    public function showInterfaceApi($global_hash_api, $workspace_hash_api)
    {
        // Encontrar o usuário pelo global_hash_api
        $user = User::where('global_hash_api', $global_hash_api)->firstOrFail();
        
        // Use first() em vez de get() para retornar UM workspace
        $workspace = Workspace::with(['topics.fields' => function($query) {
                $query->orderBy('order', 'asc');
            }])
            ->where('user_id', $user->id)
            ->where('workspace_hash_api', $workspace_hash_api)
            ->first(); // Mude para first()
        
        if (!$workspace) {
            abort(404);
        }
        
        if ($workspace->type_view_workspace_id != 1) {
            abort(404);
        }

        return view('pages.dashboard.workspace-shared.interface-api', compact('workspace', 'user'));
    }

    // Seu WorkspaceController - ADICIONAR ESTE MÉTODO
    public function showApiRest($id)
    {
        $workspace = Workspace::with(['topics.fields'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $apiKey = $workspace->workspace_hash_api;

        return view('pages.dashboard.workspace-shared.rest-api', compact(
            'workspace',
            'apiKey',
        ));
    }

    // Rota para a API REST compartilhada
    public function sharedApi($global_hash_api, $workspace_hash_api)
    {
        // Encontrar o usuário pelo global_hash_api
        $user = User::where('global_hash_api', $global_hash_api)->firstOrFail();
        
        // Encontrar o workspace pelo workspace_hash e user_id
        $workspace = Workspace::where('workspace_hash', $workspace_hash_api)
                            ->where('user_id', $user->id)
                            ->firstOrFail();
        
        // Verificar se o workspace está configurado para API REST
        if ($workspace->type_view_workspace_id != 2) {
            abort(404);
        }
        
        // Retornar os dados em formato JSON
        return response()->json([
            'workspace' => $workspace->load('topics.fields')
        ]);
    }
}
