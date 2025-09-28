@extends('template.template-site')

@section('content_site')
<div class="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white">API REST - {{ $workspace->title }}</h1>
                <p class="text-slate-400 mt-2">Gerencie e integre seus dados através de API</p>
            </div>
            <a href="{{ route('workspace.show', $workspace->id) }}" 
               class="flex items-center text-cyan-400 hover:text-cyan-300 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar ao Workspace
            </a>
        </div>

        <!-- API Status Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Status Card -->
            <div class="bg-slate-800 rounded-xl p-6 border border-cyan-500/20">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Status da API</h3>
                        @if ($workspace->api_enabled)    
                            <p class="text-green-400 flex items-center mt-1">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                                API Ativa
                            </p>
                        @else
                            <p class="text-red-400 flex items-center mt-1">
                                <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                                API Desativada
                            </p>
                        @endif
                    </div>
                    <div class="p-3 bg-cyan-500/10 rounded-lg">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Base URL Card -->
            <div class="bg-slate-800 rounded-xl p-6 border border-cyan-500/20">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">Base URL</h3>
                        <p class="text-slate-300 font-mono text-sm mt-1">{{ url('/api/v1') }}</p>
                    </div>
                    <button data-copy-target="#baseUrlText" class="p-2 text-cyan-400 hover:text-cyan-300 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </button>
                </div>
                <span id="baseUrlText" class="hidden">{{ url('/api/v1') }}</span>
            </div>

            <!-- API Key Card -->
            <div class="bg-slate-800 rounded-xl p-6 border border-cyan-500/20">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-white">API Key</h3>
                        <p class="text-slate-300 font-mono text-sm mt-1" id="apiKeyDisplay">
                            @if($workspace->workspace_hash_api)
                                ••••••••{{ substr($workspace->workspace_hash_api, -8) }}
                            @else
                                Não gerada
                            @endif
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        @if($workspace->workspace_hash_api)
                            <button onclick="regenerateApiKey()" class="p-2 text-yellow-400 hover:text-yellow-300" title="Regenerar API Key">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                            <button data-copy-target="#apiKeyText" class="p-2 text-cyan-400 hover:text-cyan-300" title="Copiar API Key">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        @else
                            <button onclick="generateApiKey()" class="p-2 text-green-400 hover:text-green-300" title="Gerar API Key">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
                <span id="apiKeyText" class="hidden">{{ $workspace->workspace_hash_api }}</span>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-8 border-b border-slate-700">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" 
                data-tabs-toggle="#default-styled-tab-content"
                data-tabs-active-classes="text-teal-600 hover:text-teal-600 dark:text-teal-500 dark:hover:text-teal-500 border-teal-600 dark:border-teal-500" 
                data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" 
                role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg text-teal-600 border-teal-600 dark:text-teal-500 dark:border-teal-500" 
                            type="button" 
                            role="tab" 
                            aria-controls="endpoints-tab" 
                            aria-selected="true"
                            data-tabs-target="#endpoints-tab">
                        📡 Endpoints
                    </button>
                </li>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" 
                            type="button" 
                            role="tab" 
                            aria-controls="documentation-tab" 
                            aria-selected="false" 
                            data-tabs-target="#documentation-tab">
                        📚 Documentação
                    </button>
                </li>
                <li role="presentation">
                    <button class="inline-block p-4 border-b-2 rounded-t-lg" 
                            type="button" 
                            role="tab" 
                            aria-controls="settings-tab" 
                            aria-selected="false" 
                            data-tabs-target="#settings-tab">
                        ⚙️ Configurações
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div id="default-styled-tab-content">
            <!-- Endpoints Tab -->
            <div class="p-4 rounded-lg bg-slate-800/50" id="endpoints-tab" role="tabpanel">
                <div class="mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Endpoints Disponíveis</h3>
                    
                    <!-- Workspace Endpoints -->
                    <div class="mb-8">
                        <h4 class="text-lg font-medium text-cyan-400 mb-4">📁 Workspace</h4>
                        <div class="space-y-4" id="workspaceEndpoints">
                            <!-- Gerado via JavaScript -->
                        </div>
                    </div>

                    <!-- Topics Endpoints -->
                    <div class="mb-8">
                        <h4 class="text-lg font-medium text-cyan-400 mb-4">🗂️ Tópicos</h4>
                        <div class="space-y-4" id="topicsEndpoints">
                            <!-- Gerado via JavaScript -->
                        </div>
                    </div>

                    <!-- Fields Endpoints -->
                    <div>
                        <h4 class="text-lg font-medium text-cyan-400 mb-4">🔤 Campos</h4>
                        <div class="space-y-4" id="fieldsEndpoints">
                            <!-- Gerado via JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentation Tab -->
            <div class="hidden p-4 rounded-lg bg-slate-800/50" id="documentation-tab" role="tabpanel">
                <h3 class="text-xl font-semibold text-white mb-6">📚 Documentação da API</h3>
                
                <div class="space-y-6">
                    <!-- Autenticação -->
                    <div class="bg-slate-900 rounded-lg p-6">
                        <h4 class="text-cyan-400 text-lg font-semibold mb-3">🔑 Autenticação</h4>
                        <p class="text-slate-300 mb-4">Todas as requisições devem incluir o header de autenticação:</p>
                        <div class="bg-slate-800 rounded p-3 mb-4">
                            <code class="text-cyan-300 font-mono text-sm">
                                Authorization: Bearer <span id="authKeyExample">{{ $workspace->workspace_hash_api ?: 'SUA_API_KEY' }}</span>
                            </code>
                            <button data-copy-target="#authKeyExample" class="ml-2 text-cyan-400 hover:text-cyan-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Estrutura de Resposta -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-slate-900 rounded-lg p-6">
                            <h4 class="text-cyan-400 text-lg font-semibold mb-3">📋 Estrutura de Resposta</h4>
                            <pre class="text-slate-300 text-sm font-mono">
                                <code>
{
    "success": true,
    "data": { ... },
    "message": "Operação realizada"
}
                                </code>
                            </pre>
                        </div>

                        <div class="bg-slate-900 rounded-lg p-6">
                            <h4 class="text-cyan-400 text-lg font-semibold mb-3">📊 Códigos de Status</h4>
                            <ul class="text-slate-300 space-y-2 text-sm">
                                <li class="flex items-center"><span class="text-green-400 font-mono mr-2">200</span> Sucesso</li>
                                <li class="flex items-center"><span class="text-blue-400 font-mono mr-2">201</span> Criado</li>
                                <li class="flex items-center"><span class="text-red-400 font-mono mr-2">400</span> Erro na requisição</li>
                                <li class="flex items-center"><span class="text-red-400 font-mono mr-2">401</span> Não autorizado</li>
                                <li class="flex items-center"><span class="text-red-400 font-mono mr-2">404</span> Não encontrado</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Exemplo de Uso -->
                    <div class="bg-slate-900 rounded-lg p-6">
                        <h4 class="text-cyan-400 text-lg font-semibold mb-3">🚀 Exemplo de Uso</h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Selecione a linguagem:</label>
                                <select id="selectDocExample" class="bg-slate-800 border border-slate-700 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-2.5">
                                    <option value="javascript" selected>JavaScript</option>
                                    <option value="php">PHP</option>
                                    <option value="python">Python</option>
                                </select>
                            </div>
                            <div class="bg-slate-800 rounded p-4">
                                <pre id="docCodeOutput" class="text-slate-300 text-sm font-mono whitespace-pre-wrap"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div class="hidden p-4 rounded-lg bg-slate-800/50" id="settings-tab" role="tabpanel">
                <h3 class="text-xl font-semibold text-white mb-6">⚙️ Configurações de Segurança</h3>
                
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
                                <form action="{{ route('workspace.api.toggle', $workspace) }}" method="POST" class="flex items-center">
                                    @csrf @method('PUT')
                                    <button type="submit" class="relative inline-flex items-center h-6 rounded-full w-11 
                                        @if($workspace->api_enabled) bg-teal-500 @else bg-gray-600 @endif transition-colors">
                                        <span class="inline-block w-4 h-4 transform bg-white rounded-full transition 
                                            @if($workspace->api_enabled) translate-x-6 @else translate-x-1 @endif" />
                                    </button>
                                    <span class="ml-3 text-sm font-medium text-white">
                                        {{ $workspace->api_enabled ? 'Ativada' : 'Desativada' }}
                                    </span>
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

                        <!-- Domínios Permitidos -->
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
                                               placeholder="exemplo.com ou sub.exemplo.com" 
                                               class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm"
                                               pattern="^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$"
                                               title="Digite um domínio válido"
                                               required>
                                        @error('domain')
                                            <span class="text-red-400 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <button type="submit" 
                                            class="bg-teal-500 hover:bg-teal-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        Adicionar
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
                                $maxDomains = $plan->max_domains ?? 10;
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
                                </div>

                                <!-- Requests -->
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-slate-400">Requests/dia</span>
                                        <span class="text-white font-medium">{{ $plan->api_requests_per_day ?? 'Ilimitado' }}</span>
                                    </div>
                                    <div class="w-full bg-slate-700 rounded-full h-2">
                                        <div class="bg-blue-400 h-2 rounded-full" style="width: 45%"></div>
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
                                    Adicione apenas domínios que você controla
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Revogue acesso de domínios não utilizados
                                </li>
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Use HTTPS em produção
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Configurações
const WORKSPACE_ID = {{ $workspace->id }};
const BASE_URL = '{{ url('/api/v1') }}';
const API_KEY = '{{ $workspace->workspace_hash_api }}';

// Inicializar quando o DOM carregar
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    renderEndpoints();
    updateDocCodeExample()
});

// Inicializar tabs do Flowbite
function initializeTabs() {
    // Flowbite já cuida das tabs automaticamente
    console.log('Tabs inicializadas');
}

// Renderizar endpoints
function renderEndpoints() {
    const endpoints = generateEndpoints();
    
    renderEndpointSection('workspaceEndpoints', endpoints.workspace);
    renderEndpointSection('topicsEndpoints', endpoints.topics);
    renderEndpointSection('fieldsEndpoints', endpoints.fields);
}

function renderEndpointSection(containerId, endpoints) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = endpoints.map(endpoint => `
        <div class="bg-slate-900 rounded-lg p-4 border border-slate-700 hover:border-cyan-500/50 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <span class="px-2 py-1 text-xs font-mono rounded ${getMethodColor(endpoint.method)}">
                        ${endpoint.method}
                    </span>
                    <code class="text-cyan-300 text-sm">${endpoint.path}</code>
                </div>
                <button onclick="copyToClipboard('${endpoint.full_url}')" 
                        class="text-slate-400 hover:text-cyan-300 transition-colors"
                        data-tooltip-target="tooltip-copy">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
            <p class="text-slate-400 text-sm mb-3">${endpoint.description}</p>
            ${endpoint.parameters && endpoint.parameters.length ? `
                <div class="mt-2">
                    <span class="text-slate-500 text-xs uppercase font-semibold">Parâmetros:</span>
                    <div class="flex flex-wrap gap-1 mt-1">
                        ${endpoint.parameters.map(param => `
                            <span class="px-2 py-1 bg-slate-800 text-slate-300 text-xs rounded">${param}</span>
                        `).join('')}
                    </div>
                </div>
            ` : ''}
        </div>
    `).join('');
}

// Gerar estrutura de endpoints
function generateEndpoints() {
    return {
        workspace: [
            {
                method: 'GET',
                path: `/workspaces/${WORKSPACE_ID}`,
                full_url: `${BASE_URL}/workspaces/${WORKSPACE_ID}`,
                description: 'Obter informações do workspace',
                parameters: []
            },
            {
                method: 'GET',
                path: `/workspaces/${WORKSPACE_ID}/stats`,
                full_url: `${BASE_URL}/workspaces/${WORKSPACE_ID}/stats`,
                description: 'Obter estatísticas do workspace',
                parameters: []
            }
        ],
        topics: [
            {
                method: 'GET',
                path: `/workspaces/${WORKSPACE_ID}/topics`,
                full_url: `${BASE_URL}/workspaces/${WORKSPACE_ID}/topics`,
                description: 'Listar todos os tópicos',
                parameters: []
            },
            {
                method: 'POST',
                path: `/workspaces/${WORKSPACE_ID}/topics`,
                full_url: `${BASE_URL}/workspaces/${WORKSPACE_ID}/topics`,
                description: 'Criar novo tópico',
                parameters: ['name', 'order']
            }
        ],
        fields: [
            {
                method: 'GET',
                path: `/topics/{id}/fields`,
                full_url: `${BASE_URL}/topics/{id}/fields`,
                description: 'Listar campos de um tópico',
                parameters: ['topic_id']
            },
            {
                method: 'POST',
                path: `/topics/{id}/fields`,
                full_url: `${BASE_URL}/topics/{id}/fields`,
                description: 'Criar novo campo',
                parameters: ['topic_id', 'key_name', 'value']
            }
        ]
    };
}

const selectDocExample = $('#selectDocExample');
function updateDocCodeExample() {
    const docCodeOutput = $('#docCodeOutput');    
    
    // Construir strings manualmente
    const fullUrl = BASE_URL;
    
    const examples = {
        javascript: '// JavaScript (Fetch API)\n' +
                   'const response = await fetch(\'' + fullUrl + '\', {\n' +
                   '    method: \'GET\',\n' +
                   '    headers: {\n' +
                   '        \'Authorization\': \'Bearer ' + API_KEY + '\',\n' +
                   '        \'Content-Type\': \'application/json\'\n' +
                   '    }\n' +
                   '});\n' +
                   'const data = await response.json();\n' +
                   'console.log(data);',

        php: '<?php\n' +
             '// PHP (cURL)\n' +
             '$ch = curl_init();\n' +
             'curl_setopt($ch, CURLOPT_URL, \'' + fullUrl + '\');\n' +
             'curl_setopt($ch, CURLOPT_HTTPHEADER, [\n' +
             '    \'Authorization: Bearer ' + API_KEY + '\',\n' +
             '    \'Content-Type: application/json\'\n' +
             ']);\n' +
             'curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);\n' +
             '$response = curl_exec($ch);\n' +
             'curl_close($ch);\n' +
             'echo $response;',

        python: '# Python (requests)\n' +
                'import requests\n\n' +
                'url = \'' + fullUrl + '\'\n' +
                'headers = {\n' +
                '    \'Authorization\': \'Bearer ' + API_KEY + '\',\n' +
                '    \'Content-Type\': \'application/json\'\n' +
                '}\n\n' +
                'response = requests.get(url, headers=headers)\n' +
                'print(response.json())',

        curl: '# cURL\n' +
              'curl -X GET \\\\\n' +
              '  \'' + fullUrl + '\' \\\\\n' +
              '  -H \'Authorization: Bearer ' + API_KEY + '\' \\\\\n' +
              '  -H \'Content-Type: application/json\''
    };

    docCodeOutput.text(examples[selectDocExample.val()] || examples.javascript);
}

selectDocExample.on('change', function(){ updateDocCodeExample() })


// Funções auxiliares
function getMethodColor(method) {
    const colors = {
        'GET': 'bg-green-500/20 text-green-400',
        'POST': 'bg-blue-500/20 text-blue-400',
        'PUT': 'bg-yellow-500/20 text-yellow-400',
        'PATCH': 'bg-orange-500/20 text-orange-400',
        'DELETE': 'bg-red-500/20 text-red-400'
    };
    return colors[method] || 'bg-slate-500/20 text-slate-400';
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copiado para a área de transferência!', 'success');
    });
}

function showNotification(message, type = 'info') {
    // Usar toast do Flowbite
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-blue-500 text-white'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.remove(), 3000);
}

// Gerenciamento de API Key
async function generateApiKey() {
    if (!confirm('Deseja gerar uma nova API Key?')) return;
    
    try {
        const response = await fetch(`/workspace/${WORKSPACE_ID}/generate-api-key`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload(); // Recarregar para mostrar nova key
        } else {
            showNotification('Erro ao gerar API Key', 'error');
        }
    } catch (error) {
        showNotification('Erro de conexão', 'error');
    }
}

async function regenerateApiKey() {
    if (!confirm('Tem certeza? Isso invalidará a chave atual.')) return;
    await generateApiKey();
}
</script>

@endsection