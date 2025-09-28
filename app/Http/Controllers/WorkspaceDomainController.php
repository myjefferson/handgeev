<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\WorkspaceAllowedDomain;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkspaceDomainController extends Controller
{
    /**
     * Adicionar domínio permitido
     */
    public function addDomain(Request $request, Workspace $workspace)
    {
        // Verificar limite do plano
        $maxDomains = $workspace->user->getPlan()->max_domains ?? 10;
        $currentDomains = $workspace->allowedDomains()->active()->count();
        
        if ($currentDomains >= $maxDomains) {
            return back()->with('error', "Limite de {$maxDomains} domínios atingido para seu plano.");
        }

        $validated = $request->validate([
            'domain' => [
                'required',
                'regex:/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i',
                Rule::unique('workspace_allowed_domains')
                    ->where('workspace_id', $workspace->id)
                    ->where(function($query) {
                        $query->where('is_active', true);
                    })
            ]
        ], [
            'domain.regex' => 'Formato de domínio inválido.',
            'domain.unique' => 'Este domínio já está na lista de permitidos.'
        ]);

        try {
            $workspace->addAllowedDomain($validated['domain']);
            return back()->with('success', 'Domínio adicionado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao adicionar domínio: ' . $e->getMessage());
        }
    }

    /**
     * Remover/desativar domínio
     */
    public function removeDomain(Request $request, Workspace $workspace)
    {
        $validated = $request->validate([
            'domain_id' => 'required|exists:workspace_allowed_domains,id'
        ]);

        try {
            $domain = WorkspaceAllowedDomain::where('workspace_id', $workspace->id)
                ->where('id', $validated['domain_id'])
                ->firstOrFail();

            $domain->update(['is_active' => false]);
            
            return back()->with('success', 'Domínio removido com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao remover domínio.');
        }
    }

    /**
     * Reativar domínio
     */
    public function activateDomain(Request $request, Workspace $workspace)
    {
        $validated = $request->validate([
            'domain_id' => 'required|exists:workspace_allowed_domains,id'
        ]);

        try {
            $domain = WorkspaceAllowedDomain::where('workspace_id', $workspace->id)
                ->where('id', $validated['domain_id'])
                ->firstOrFail();

            $domain->update(['is_active' => true]);
            
            return back()->with('success', 'Domínio reativado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao reativar domínio.');
        }
    }

    /**
     * Ativar/desativar API
     */
    public function toggleApi(Workspace $workspace)
    {
        try {
            $workspace->update(['api_enabled' => !$workspace->api_enabled]);
            
            $status = $workspace->api_enabled ? 'ativada' : 'desativada';
            return back()->with('success', "API {$status} com sucesso!");
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao alterar status da API.');
        }
    }
}