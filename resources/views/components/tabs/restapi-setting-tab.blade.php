<div class="hidden p-6 rounded-lg bg-slate-800/50 border border-slate-700" id="settings-tab" role="tabpanel">
    <h3 class="text-xl font-semibold text-white mb-6">⚙️ Configurações de Segurança</h3>
    @if($workspace->api_domain_restriction && $workspace->allowedDomains->where('is_active', true)->count() === 0)
        <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4 mb-4">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <p class="text-red-400 font-medium">Atenção: API bloqueada</p>
                    <p class="text-red-300 text-sm mt-1">
                        A restrição por domínio está ativa, mas nenhum domínio foi configurado. 
                        <strong>A API está bloqueando todas as requisições.</strong> 
                        Adicione domínios abaixo para permitir o acesso.
                    </p>
                </div>
            </div>
        </div>
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna Principal -->
        <div class="lg:col-span-2">
            <!-- Status da API -->
            <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-lg font-semibold text-white">Status do Acesso API</h4>
                        <p class="text-slate-400 text-sm">Controle o acesso à API deste workspace</p>
                    </div>
                    <form action="{{ route('workspace.api.access.toggle', $workspace) }}" method="POST" class="flex items-center">
                        @csrf @method('PUT')
                        <span class="mr-3 text-sm font-medium text-white">
                            {{ $workspace->api_enabled ? 'Ativada' : 'Desativada' }}
                        </span>
                        <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 
                            @if($workspace->api_enabled) bg-teal-500 @else bg-gray-600 @endif transition-colors">
                            <span class="inline-block w-4 h-4 transform bg-white rounded-full transition 
                                @if($workspace->api_enabled) translate-x-6 @else translate-x-1 @endif" />
                        </button>
                    </form>
                </div>
                
                @if(!$workspace->api_enabled)
                <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-3">
                    <p class="text-yellow-400 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        A API está desativada. Nenhum acesso externo será permitido.
                    </p>
                </div>
                @endif
            </div>

            <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-lg font-semibold text-white">Autenticação JWT Obrigatória</h4>
                        <p class="text-slate-400 text-sm">Forçar uso de tokens JWT via rota de autenticação</p>
                    </div>
                    <form action="{{ route('workspace.api.jwt-requirement.toggle', $workspace->id) }}" method="POST" class="flex items-center">
                        @csrf @method('PUT')
                        <span class="mr-3 text-sm font-medium text-white">
                            {{ $workspace->api_jwt_required ? 'JWT Obrigatório' : 'Tokens Livres' }}
                        </span>
                        <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 
                            @if($workspace->api_jwt_required) bg-teal-500 @else bg-gray-600 @endif transition-colors">
                            <span class="inline-block w-4 h-4 transform bg-white rounded-full transition 
                                @if($workspace->api_jwt_required) translate-x-6 @else translate-x-1 @endif" />
                        </button>
                    </form>
                </div>

                @if(!$workspace->api_jwt_required)
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3">
                    <p class="text-blue-400 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Tokens fixos do workspace são aceitos. Ative para exigir autenticação JWT.
                    </p>
                </div>
                @else
                <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-3 mb-4">
                    <p class="text-green-400 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Autenticação JWT obrigatória. Use a rota <code class="bg-slate-700 px-1 rounded">/api/auth/login/token</code> para obter tokens.
                    </p>
                </div>
                
                <!-- Informações da Rota de Autenticação -->
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4">
                    <h5 class="text-amber-400 font-medium mb-2">🔐 Rota de Autenticação</h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-start">
                            <span class="text-amber-300 font-mono text-xs bg-amber-500/20 px-2 py-1 rounded mr-2">POST</span>
                            <div>
                                <code class="text-amber-200">{{ url('/api/auth/login/token') }}</code>
                                <p class="text-amber-300 mt-1">Obtenha um token JWT válido usando suas credenciais</p>
                            </div>
                        </div>
                        
                        <div class="mt-3 p-2 bg-slate-700 rounded text-xs">
                            <p class="text-amber-300 mb-1"><strong>Body da requisição:</strong></p>
                            <pre class="text-amber-200"><code>{
    "email": "seu-email@exemplo.com",
    "password": "sua-senha"
}</code></pre>
                        </div>
                        
                        <div class="mt-2 p-2 bg-slate-700 rounded text-xs">
                            <p class="text-amber-300 mb-1"><strong>Resposta:</strong></p>
                            <pre class="text-amber-200"><code>{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_at": "2024-01-01T00:00:00Z"
}</code></pre>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Controle de Domínios -->
            <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-lg font-semibold text-white">Controle de Acesso por Domínio</h4>
                        <p class="text-slate-400 text-sm">Restringir acesso apenas a domínios específicos</p>
                    </div>
                    <form action="{{ route('workspace.api.domain-restriction.toggle', $workspace) }}" method="POST" class="flex items-center">
                        @csrf @method('PUT')
                        <span class="mr-3 text-sm font-medium text-white">
                            {{ $workspace->api_domain_restriction ? 'Apenas domínios permitidos' : 'Acesso livre' }}
                        </span>
                        <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 
                            @if($workspace->api_domain_restriction) bg-teal-500 @else bg-gray-600 @endif transition-colors">
                            <span class="inline-block w-4 h-4 transform bg-white rounded-full transition 
                                @if($workspace->api_domain_restriction) translate-x-6 @else translate-x-1 @endif" />
                        </button>
                    </form>
                </div>

                @if(!$workspace->api_domain_restriction)
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3">
                    <p class="text-blue-400 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        A API aceita requisições de qualquer domínio. Ative a restrição para maior segurança.
                    </p>
                </div>
                @else
                <div class="bg-green-500/10 border border-green-500/20 rounded-lg p-3 mb-4">
                    <p class="text-green-400 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        A API só aceita requisições dos domínios listados abaixo.
                    </p>
                </div>
                @endif
            </div>

            <!-- Domínios Permitidos (só mostra quando a restrição está ativa) -->
            @if($workspace->api_domain_restriction)
            <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="text-lg font-semibold text-white">Domínios Permitidos</h4>
                        <p class="text-slate-400 text-sm">
                            {{ $workspace->allowedDomains->where('is_active', true)->count() }} domínios ativos
                        </p>
                    </div>
                </div>

                <!-- Formulário para Adicionar Domínio -->
                <form action="{{ route('workspace.api.domains.add', $workspace) }}" method="POST" class="mb-6">
                    @csrf
                    <div class="flex space-x-3">
                        <div class="flex-1">
                            <input type="text" name="domain" 
                                placeholder="exemplo.com ou *.exemplo.com" 
                                class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm"
                                pattern="^(\*\.)?([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}(:\d+)?$|^localhost(:\d+)?$|^(\*\.)?([a-z0-9]+(-[a-z0-9]+)*\.)?localhost(:\d+)?$"
                                title="Digite um domínio válido (ex: site.com, *.site.com)"
                                required>
                            <p class="text-slate-500 text-xs mt-1">
                                Use *.exemplo.com para permitir todos os subdomínios
                            </p>
                            @error('domain')
                                <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" 
                                class="bg-teal-500 hover:bg-teal-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                            Adicionar Domínio
                        </button>
                    </div>
                </form>

                <!-- Lista de Domínios Ativos -->
                <div class="space-y-3">
                    <h5 class="text-sm font-medium text-slate-300">Domínios Ativos</h5>
                    
                    @forelse($workspace->allowedDomains->where('is_active', true) as $domain)
                    <div class="flex items-center justify-between p-3 bg-slate-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-white text-sm font-mono">{{ $domain->domain }}</span>
                            <span class="text-slate-500 text-xs">{{ $domain->created_at->format('d/m/Y') }}</span>
                            @if(strpos($domain->domain, '*') === 0)
                            <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">Wildcard</span>
                            @endif
                        </div>
                        <form action="{{ route('workspace.api.domains.remove', $workspace) }}" method="POST">
                            @csrf @method('DELETE')
                            <input type="hidden" name="domain_id" value="{{ $domain->id }}">
                            <button type="submit" 
                                    class="text-red-400 hover:text-red-300 transition-colors"
                                    title="Remover domínio">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="text-center p-4 bg-slate-700 rounded-lg">
                        <p class="text-slate-400 text-sm">Nenhum domínio configurado</p>
                        <p class="text-slate-500 text-xs mt-1">
                            Adicione pelo menos um domínio para permitir acesso à API
                        </p>
                    </div>
                    @endforelse
                </div>

                <!-- Domínios Inativos -->
                @if($workspace->allowedDomains->where('is_active', false)->count() > 0)
                <div class="mt-6 pt-6 border-t border-slate-700">
                    <h5 class="text-sm font-medium text-slate-300 mb-3">Domínios Inativos</h5>
                    <div class="space-y-2">
                        @foreach($workspace->allowedDomains->where('is_active', false) as $domain)
                        <div class="flex items-center justify-between p-2 bg-slate-700 rounded opacity-60">
                            <div class="flex items-center space-x-2">
                                <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span class="text-slate-300 text-xs font-mono">{{ $domain->domain }}</span>
                            </div>
                            <form action="{{ route('workspace.api.domains.activate', $workspace) }}" method="POST">
                                @csrf @method('PUT')
                                <input type="hidden" name="domain_id" value="{{ $domain->id }}">
                                <button type="submit" 
                                        class="text-teal-400 hover:text-teal-300 text-xs transition-colors">
                                    Reativar
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- Coluna Lateral -->
        <div class="lg:col-span-1">
            <!-- Informações do Plano -->
            <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
                <h4 class="text-lg font-semibold text-white mb-4">📊 Limites do Plano</h4>
                
                @php
                    $user = $workspace->user;
                    $plan = $user->getPlan();
                    $activeDomainsCount = $workspace->allowedDomains->where('is_active', true)->count();
                    $maxDomains = $plan->max_domains ?? 1;
                @endphp
                
                <div class="space-y-4">
                    <!-- Domínios -->
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-400">Domínios permitidos</span>
                            <span class="text-white font-medium">{{ $activeDomainsCount }} / {{ $maxDomains }}</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-teal-400 h-2 rounded-full" 
                                    style="width: {{ min(100, ($activeDomainsCount / $maxDomains) * 100) }}%"></div>
                        </div>
                        @if($workspace->api_domain_restriction && $activeDomainsCount === 0)
                        <p class="text-red-400 text-xs mt-1">⚠️ Adicione domínios para permitir acesso</p>
                        @endif
                    </div>

                    <!-- Status do Controle -->
                    <div class="pt-3 border-t border-slate-700">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-sm">Controle de domínios</span>
                            <span class="px-2 py-1 
                                @if($workspace->api_domain_restriction) 
                                    bg-green-500/20 text-green-400 
                                @else 
                                    bg-blue-500/20 text-blue-400 
                                @endif rounded text-xs font-medium">
                                {{ $workspace->api_domain_restriction ? 'Restrito' : 'Livre' }}
                            </span>
                        </div>
                    </div>

                    <!-- Requests por Minuto -->
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-400">Requests/minuto</span>
                            <span class="text-white font-medium">{{ $plan->api_requests_per_minute ?? 30 }}</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-blue-400 h-2 rounded-full" style="width: {{ min(100, (($plan->api_requests_per_minute ?? 30) / 250) * 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Requests por Dia -->
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-400">Requests/dia</span>
                            <span class="text-white font-medium">{{ $plan->api_requests_per_day ?? 2000 }}</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-purple-400 h-2 rounded-full" style="width: {{ min(100, (($plan->api_requests_per_day ?? 2000) / 250000) * 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Plano Atual -->
                    <div class="pt-3 border-t border-slate-700">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-sm">Plano atual</span>
                            <span class="px-2 py-1 bg-teal-500/20 text-teal-400 rounded text-xs font-medium">
                                {{ $user->getRoleNames()->first() }}
                            </span>
                        </div>
                    </div>

                    @if($user->isFree() && $activeDomainsCount >= $maxDomains)
                    <div class="mt-4 p-3 bg-amber-500/10 border border-amber-500/20 rounded-lg">
                        <p class="text-amber-400 text-xs">
                            Limite de domínios atingido. 
                            <a href="{{ route('plans') }}" class="underline hover:text-amber-300">
                                Faça upgrade para adicionar mais domínios.
                            </a>
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Dicas de Segurança -->
            <div class="bg-slate-800 rounded-xl p-6 border border-slate-700">
                <h4 class="text-lg font-semibold text-white mb-3">🔒 Dicas de Segurança</h4>
                <ul class="text-slate-300 text-sm space-y-2">
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>
                            <strong>JWT Obrigatório:</strong> Mais seguro, tokens com expiração
                        </span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>
                            <strong>Modo livre:</strong> Ideal para desenvolvimento e testes
                        </span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>
                            <strong>Modo restrito:</strong> Obrigatório para produção
                        </span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Use *.seudominio.com para permitir todos os subdomínios
                    </li>
                    <li class="flex items-start">
                        <svg class="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Revogue acesso de domínios não utilizados
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>