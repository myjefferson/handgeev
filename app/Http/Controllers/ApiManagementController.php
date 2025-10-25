<?php

namespace App\Http\Controllers;

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
        $workspaces = Workspace::with(['typeWorkspace', 'topics.fields'])
        ->where('user_id', $user->id)
        ->where('api_enabled', true)
        ->get()
        ->map(function($workspace) {
            $workspace->api_type = $workspace->type_view_workspace_id == 1 ? 'Interface API' : 'REST API';
            $workspace->api_status = $workspace->is_published ? 'Ativa' : 'Inativa';
            
            $workspace->api_requests_count = ApiRequestLog::where('workspace_id', $workspace->id)->count();
            return $workspace;
        });

        return view('pages.dashboard.api-management.my-apis', compact('workspaces'));
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
            
            return response()->json([
                'success' => true,
                'message' => "API {$status} com sucesso!",
                'new_status' => $newStatus,
                'workspace_id' => $workspace->id
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Erro ao alterar status da API: ' . $e->getMessage(), [
                'workspace_id' => $workspace->id ?? 'unknown',
                'user_id' => $user->id ?? 'unknown'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao alterar status da API.'
            ], 500);
        }
    }
}