<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\WorkspaceSharedController;
use App\Models\Workspace;
use App\Models\User;

class WorkspacePasswordMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $globalKey = $request->route('global_key_api');
        $workspaceKey = $request->route('workspace_key_api');
        
        \Log::debug('WorkspaceApiPassword Middleware - INÍCIO', [
            'path' => $request->path(),
            'global_key' => $globalKey,
            'workspace_key' => $workspaceKey
        ]);
        
        // Buscar workspace
        $workspace = $this->findWorkspace($globalKey, $workspaceKey);
        
        if (!$workspace) {
            abort(404, 'Workspace não encontrado');
        }
        
        if ($request->routeIs('workspace.shared.password') || 
            $request->routeIs('workspace.shared.verify-password')) {
            \Log::debug('Na rota de password - permitindo acesso');
            $request->attributes->set('workspace', $workspace);
            return $next($request);
        }
        
        // Verificar se precisa de senha
        if ($workspace->password && !WorkspaceSharedController::checkAccess($workspace)) {
            \Log::debug('Precisa de senha - redirecionando para formulário');
            
            return redirect()->route('workspace.shared.password', [
                'global_key_api' => $globalKey,
                'workspace_key_api' => $workspaceKey
            ]);
        }
        
        \Log::debug('Acesso permitido - continuando para rota');
        $request->attributes->set('workspace', $workspace);
        return $next($request);
    }

    private function findWorkspace($globalKey, $workspaceKey)
    {
        $user = User::where('global_key_api', $globalKey)->first();
        
        if (!$user) {
            return null;
        }
        
        return Workspace::where('workspace_key_api', $workspaceKey)
                      ->where('user_id', $user->id)
                      ->first();
    }

    private function hasAccess($workspace, $globalKey)
    {
        if ($workspace->is_published) {
            return true;
        }

        $user = User::where('global_key_api', $globalKey)->first();
        return Auth::check() && Auth::user()->id === $user->id;
    }
}