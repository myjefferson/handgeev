<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'app' => [
                'name' => env('APP_NAME'),
                'version' => env('APP_VERSION'),
            ],
            'auth' => function () use ($request) {
                if (!$request->user()) {
                    return ['user' => null];
                }

                $user = $request->user();
                $plan = $user->getPlan();
                
                return [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'status' => $user->status,
                        'email_verified_at' => $user->email_verified_at,
                        'language' => $user->language,
                        'timezone' => $user->timezone,

                        'csrf_token' => csrf_token(),
                        'global_key_api' => $user->global_key_api,
                        
                        // Dados do plano
                        'plan' => [
                            'name' => $plan->name ?? 'free',
                            'plan_expires_at' => $user->plan_expires_at,
                            'structures' => $plan->structures ?? 3,
                            'workspaces' => $plan->workspaces ?? 1,
                            'domains' => $plan->domains ?? 1,
                            'team_members' => $plan->max_team_members ?? 1,
                            'can_create_workspace' => $user->canCreateWorkspace() ?? false
                        ],
                    ],
                ];
            },
            
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
                'warning' => fn () => $request->session()->get('warning'),
            ],
        ]);
    }

    /**
     * Handle the incoming request - ADICIONADO
     */
    public function handle($request, $next)
    {
        $response = parent::handle($request, $next);

        // Se for uma resposta de erro HTTP, renderiza com Inertia
        if ($response->isClientError() || $response->isServerError()) {
            $status = $response->getStatusCode();
            
            // Status codes que queremos customizar
            if (in_array($status, [403, 404, 419, 500, 503])) {
                return $this->renderInertiaError($request, $status, $response);
            }
        }

        return $response;
    }

    /**
     * Renderiza erro com Inertia
     */
    protected function renderInertiaError($request, int $status, $response)
    {
        if (env('APP_ENV') === 'local') {
            return $response;
        }
        
        $messages = [
            403 => 'Acesso negado.',
            404 => 'Página não encontrada.',
            419 => 'Sua sessão expirou. Por favor, atualize a página.',
            500 => 'Erro interno do servidor.',
            503 => 'Serviço temporariamente indisponível.',
        ];

        return \Inertia\Inertia::render("Errors/{$status}", [
            'status' => $status,
            'message' => $messages[$status] ?? 'Ocorreu um erro.',
        ])
        ->toResponse($request)
        ->setStatusCode($status);
    }

    private function getApiRequestsToday(User $user): int
    {
        return 0;
    }

    private function getFreePlanLimits(): array
    {
        return [
            'workspaces' => 1,
            'topics_per_workspace' => 3,
            'fields_per_topic' => 10,
            'api_requests_per_minute' => 30,
            'api_requests_per_day' => 1000,
            'domains' => 1,
            'team_members' => 1,
            'storage' => 10,
        ];
    }

    private function getFreePlanFeatures(): array
    {
        return [
            'api_access' => true,
            'analytics' => false,
            'webhooks' => false,
            'custom_domains' => false,
            'team_collaboration' => false,
            'priority_support' => false,
            'advanced_permissions' => false,
        ];
    }
}