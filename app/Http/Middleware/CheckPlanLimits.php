<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanLimits
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return $next($request);
        }

        // Verificar se usuário está com pagamento pendente
        if ($user->hasPaymentIssues()) {
            if ($this->shouldShowPaymentWarning($request)) {
                session()->flash('warning', [
                    'title' => 'Pagamento Pendente',
                    'message' => 'Sua assinatura está com pagamento pendente. Atualize suas informações para evitar interrupção do serviço.',
                    'action' => [
                        'text' => 'Resolver Agora',
                        'url' => route('billing.portal')
                    ]
                ]);
            }
        }

        // Verificar limites do plano free
        if ($user->isFree()) {
            $this->checkFreePlanLimits($user, $request);
        }

        // Verificar se é admin (acesso ilimitado)
        if ($user->isAdmin()) {
            return $next($request);
        }

        return $next($request);
    }

    private function shouldShowPaymentWarning(Request $request): bool
    {
        $excludedRoutes = [
            'subscription.pricing',
            'billing.portal',
            'subscription.success',
            'logout'
        ];

        return !in_array($request->route()->getName(), $excludedRoutes);
    }

    private function checkFreePlanLimits($user, Request $request): void
    {
        try {
            $limits = $user->plan_limits;
            
            // Verificar limites de forma segura
            $this->checkWorkspaceLimits($limits, $request);
            $this->checkFieldLimits($user, $limits, $request);
            $this->checkTopicLimits($limits, $request);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao verificar limites do plano: ' . $e->getMessage());
            // Não quebrar a aplicação por erro de limites
        }
    }

    private function checkWorkspaceLimits($limits, Request $request): void
    {
        if (!isset($limits['remaining_workspaces'])) return;
        
        if ($limits['remaining_workspaces'] <= 0) {
            if ($request->route()->getName() === 'workspaces.create') {
                session()->flash('error', [
                    'title' => 'Limite Atingido',
                    'message' => 'Você atingiu o limite de workspaces do plano Free. Faça upgrade para criar mais workspaces.',
                    'action' => ['text' => 'Fazer Upgrade', 'url' => route('subscription.pricing')]
                ]);
            }
            
            if ($limits['remaining_workspaces'] === 0 && $this->isDashboardRoute($request)) {
                session()->flash('warning', [
                    'title' => 'Limite de Workspaces',
                    'message' => 'Você atingiu o limite máximo de workspaces. Faça upgrade para criar mais.',
                    'action' => ['text' => 'Ver Planos', 'url' => route('subscription.pricing')]
                ]);
            }
        }
    }

    private function checkFieldLimits($user, $limits, Request $request): void
    {
        if (!isset($limits['remaining_fields'])) return;
        
        $topicId = $request->route('topic') ?? $request->route('topic_id') ?? $request->input('topic_id');
        $workspaceId = $request->route('workspace') ?? $request->route('workspace_id');
        
        $currentFields = $user->getCurrentFieldsCount($workspaceId, $topicId);
        $fieldLimit = $user->getFieldsLimitPerTopic();
        
        \Log::info("🔍 Middleware Field Limits", [
            'topic_id' => $topicId,
            'workspace_id' => $workspaceId,
            'current_fields' => $currentFields,
            'field_limit' => $fieldLimit,
            'remaining' => $fieldLimit - $currentFields
        ]);

        if ($currentFields >= $fieldLimit && $this->isWorkspaceRoute($request)) {
            session()->flash('error', [
                'title' => 'Limite de Campos Atingido',
                'message' => "Você atingiu o limite de {$fieldLimit} campos por tópico. Faça upgrade para campos ilimitados.",
                'action' => ['text' => 'Fazer Upgrade', 'url' => route('subscription.pricing')]
            ]);
        } elseif (($fieldLimit - $currentFields) <= 5 && $this->isWorkspaceRoute($request)) {
            session()->flash('info', [
                'title' => 'Limite de Campos Próximo',
                'message' => "Você tem apenas " . ($fieldLimit - $currentFields) . " campos restantes neste tópico.",
                'action' => ['text' => 'Fazer Upgrade', 'url' => route('subscription.pricing')]
            ]);
        }
    }

    private function checkTopicLimits($limits, Request $request): void
    {
        if (!isset($limits['current_topics'], $limits['topics'])) return;
        
        if ($limits['current_topics'] >= $limits['topics'] && $this->isWorkspaceRoute($request)) {
            session()->flash('error', [
                'title' => 'Limite de Tópicos Atingido',
                'message' => "Você atingiu o limite de {$limits['topics']} tópicos. Faça upgrade para tópicos ilimitados.",
                'action' => ['text' => 'Fazer Upgrade', 'url' => route('subscription.pricing')]
            ]);
        }
    }

    private function isDashboardRoute(Request $request): bool
    {
        $dashboardRoutes = [
            'dashboard.home',
            'dashboard.overview',
            'workspaces.show',
            'workspaces.show'
        ];

        return in_array($request->route()->getName(), $dashboardRoutes);
    }

    private function isWorkspaceRoute(Request $request): bool
    {
        $workspaceRoutes = [
            'workspaces.create',
            'workspaces.store',
            'topics.create',
            'topics.store',
            'fields.create',
            'fields.store'
        ];

        return in_array($request->route()->getName(), $workspaceRoutes) ||
               str_contains($request->route()->getName(), 'workspace.') ||
               $request->routeIs('workspaces.*') ||
               $request->routeIs('topics.*') ||
               $request->routeIs('fields.*');
    }
}