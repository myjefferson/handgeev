<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Workspace;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApiManagementController extends Controller
{
public function showMyApis()
{
    $user = Auth::user();
    
    // Buscar workspaces com APIs ativas
    $workspaces = Workspace::with([
        'typeWorkspace',
        'topics.structureFields' // <<< corrigido
    ])
    ->where('user_id', $user->id)
    ->get()
    ->map(function($workspace) {

        $workspace->api_type = $workspace->type_view_workspace_id == 1 
            ? 'Geev Studio' 
            : 'Geev API';

        $workspace->api_status = $workspace->is_published ? 'Ativa' : 'Inativa';
        
        // Contagem de requests
        $workspace->api_requests_count = ApiRequestLog::where('workspace_id', $workspace->id)->count();

        // Corrigido: buscar fields visíveis
        $workspace->visible_fields_count = $workspace->topics
            ->flatMap
            ->structureFields
            ->where('is_visible', true)
            ->count();

        return $workspace;
    });

    return Inertia::render('Dashboard/ApiManagement/MyApis', [
        'workspaces' => $workspaces
    ]);
}




    
    /**
     * Ativar/desativar API
    */
    public function toggleAccessApi(Workspace $workspace)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }

            // Verificar se o workspace pertence ao usuário
            if ($workspace->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não tem permissão para alterar este workspace.'
                ], 403);
            }

            \Log::info('Iniciando toggle API', [
                'workspace_id' => $workspace->id,
                'current_status' => $workspace->api_enabled,
                'user_id' => $user->id
            ]);

            // Usar query builder para evitar problemas do Eloquent
            $newStatus = !$workspace->api_enabled;
            
            $updated = DB::table('workspaces')
                ->where('id', $workspace->id)
                ->where('user_id', $user->id)
                ->update([
                    'api_enabled' => $newStatus,
                    'updated_at' => now()
                ]);

            \Log::info('Resultado do update', [
                'workspace_id' => $workspace->id,
                'new_status' => $newStatus,
                'rows_affected' => $updated
            ]);

            if ($updated === 0) {
                throw new \Exception('Nenhuma linha foi atualizada');
            }

            $status = $newStatus ? 'ativada' : 'desativada';
            
            return back()->with([
                'type'=> 'success',
                'message' => "API {$status} com sucesso!",
                'new_status' => $newStatus,
                'workspace_id' => $workspace->id
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Erro ao alterar status da API: ' . $e->getMessage(), [
                'workspace_id' => $workspace->id ?? 'unknown',
                'user_id' => $user->id ?? 'unknown'
            ]);

            return back()->with([
                'type'=> 'error',
                'message' => 'Erro interno ao alterar status da API.'
            ], 500);
        }
    }

    public function toggleHttpsRequirement(Workspace $workspace)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'type'=> 'error',
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        $this->authorize('update', $workspace);

        $workspace->update([
            'api_https_required' => !$workspace->api_https_required
        ]);

        return back()->with('success', 
            $workspace->api_https_required 
                ? 'HTTPS agora é obrigatório para este workspace' 
                : 'HTTP agora é permitido para este workspace'
        );
    }
}