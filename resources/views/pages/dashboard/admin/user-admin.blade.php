@extends('template.template-dashboard')

@section('title', 'Perfil do Usuário')
@section('description', 'Detalhes e gestão do usuário')

@push('style')
<style>
    .profile-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-bottom: 1px solid #334155;
    }
    
    .stat-card {
        background: #1e293b;
        border: 1px solid #334155;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
    }
    
    .activity-item {
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .activity-item:hover {
        border-left-color: #3b82f6;
        background: #1e293b;
    }
    
    .badge-status {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .plan-progress {
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .workspace-item {
        border: 1px solid #334155;
        transition: all 0.3s ease;
    }
    
    .workspace-item:hover {
        border-color: #3b82f6;
    }
</style>
@endpush

@section('content_dashboard')
<div class="max-w-7xl mx-auto p-0 sm:p-0 md:p-6">
    <!-- Header do Perfil -->
    <a href="{{ url()->previous() }}" class="block w-max text-sm text-gray-300 hover:text-teal-400 transition-colors mb-8">
        <i class="fas fa-arrow-left mr-1"></i> Voltar
    </a>
    <div class="profile-header rounded-xl p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                <div class="h-20 w-20 bg-teal-500 rounded-full flex items-center justify-center text-2xl font-bold text-slate-900">
                    {{ substr($user->name, 0, 1) }}{{ substr($user->surname, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $user->name }} {{ $user->surname }}</h1>
                    <p class="text-slate-400">{{ $user->email }}</p>
                    <div class="flex items-center space-x-2 mt-2">
                        <span class="badge-status 
                            @if($user->status === 'active') bg-green-500/20 text-green-400 border border-green-500/30
                            @elseif($user->status === 'suspended') bg-yellow-500/20 text-yellow-400 border border-yellow-500/30
                            @elseif($user->status === 'inactive') bg-orange-500/20 text-orange-400 border border-orange-500/30
                            @else bg-slate-500/20 text-slate-400 border border-slate-500/30 @endif">
                            {{ ucfirst($user->status) }}
                        </span>
                        <span class="badge-status bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            {{ ucfirst($user->plan_name ?? 'free') }}
                        </span>
                        @if($user->email_verified_at)
                            <span class="badge-status bg-green-500/20 text-green-400 border border-green-500/30">
                                Email Verificado
                            </span>
                        @else
                            <span class="badge-status bg-red-500/20 text-red-400 border border-red-500/30">
                                Email Não Verificado
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex space-x-3">
                <button onclick="resetPassword({{ $user->id }})" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-key mr-2"></i>Resetar Senha
                </button>
                @if($user->status === 'active')
                    <button onclick="toggleUserStatus({{ $user->id }}, 'suspend')" 
                        class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-pause mr-2"></i>Suspender
                    </button>
                @else
                    <button onclick="toggleUserStatus({{ $user->id }}, 'activate')" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-play mr-2"></i>Ativar
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Navegação por Tabs -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 mb-8">
        <div class="border-b border-slate-700">
            <nav class="flex flex-col sm:flex-col space-x-8 px-6" aria-label="Tabs">
                <button onclick="switchTab('overview')" 
                    class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition-colors border-blue-500 text-white">
                    <i class="fas fa-chart-bar mr-2"></i>Visão Geral
                </button>
                <button onclick="switchTab('activity')" 
                    class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-slate-400 hover:text-slate-300">
                    <i class="fas fa-history mr-2"></i>Atividades
                </button>
                <button onclick="switchTab('workspaces')" 
                    class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-slate-400 hover:text-slate-300">
                    <i class="fas fa-folder mr-2"></i>Workspaces
                </button>
                <button onclick="switchTab('settings')" 
                    class="tab-button py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-slate-400 hover:text-slate-300">
                    <i class="fas fa-cog mr-2"></i>Configurações
                </button>
            </nav>
        </div>

        <!-- Conteúdo das Tabs -->
        <div class="p-6">
            <!-- Tab: Visão Geral -->
            <div id="overview" class="tab-content active">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="stat-card rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 text-sm">Workspaces</p>
                                <p class="text-2xl font-bold text-white">{{ $stats['workspaces_count'] }}</p>
                            </div>
                            <div class="p-3 bg-blue-500/20 rounded-lg">
                                <i class="fas fa-folder text-blue-400"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm text-slate-400 mb-1">
                                <span>Uso</span>
                                <span>{{ $currentUsage['workspaces'] }}/{{ $planLimits['max_workspaces'] == 0 ? '∞' : $planLimits['max_workspaces'] }}</span>
                            </div>
                            <div class="plan-progress bg-slate-700">
                                <div class="bg-blue-500 h-full rounded-full" 
                                     style="width: {{ $planLimits['max_workspaces'] == 0 ? 100 : min(100, ($currentUsage['workspaces'] / $planLimits['max_workspaces']) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 text-sm">Tópicos</p>
                                <p class="text-2xl font-bold text-white">{{ $stats['topics_count'] }}</p>
                            </div>
                            <div class="p-3 bg-green-500/20 rounded-lg">
                                <i class="fas fa-file-alt text-green-400"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm text-slate-400 mb-1">
                                <span>Uso</span>
                                <span>{{ $currentUsage['topics'] }}/{{ $planLimits['max_topics'] == 0 ? '∞' : $planLimits['max_topics'] }}</span>
                            </div>
                            <div class="plan-progress bg-slate-700">
                                <div class="bg-green-500 h-full rounded-full" 
                                     style="width: {{ $planLimits['max_topics'] == 0 ? 100 : min(100, ($currentUsage['topics'] / $planLimits['max_topics']) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 text-sm">Campos</p>
                                <p class="text-2xl font-bold text-white">{{ $stats['fields_count'] }}</p>
                            </div>
                            <div class="p-3 bg-purple-500/20 rounded-lg">
                                <i class="fas fa-tags text-purple-400"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="flex justify-between text-sm text-slate-400 mb-1">
                                <span>Uso</span>
                                <span>{{ $currentUsage['fields'] }}/{{ $planLimits['max_fields'] == 0 ? '∞' : $planLimits['max_fields'] }}</span>
                            </div>
                            <div class="plan-progress bg-slate-700">
                                <div class="bg-purple-500 h-full rounded-full" 
                                     style="width: {{ $planLimits['max_fields'] == 0 ? 100 : min(100, ($currentUsage['fields'] / $planLimits['max_fields']) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-slate-400 text-sm">Colaborações</p>
                                <p class="text-2xl font-bold text-white">{{ $stats['collaborations_count'] }}</p>
                            </div>
                            <div class="p-3 bg-orange-500/20 rounded-lg">
                                <i class="fas fa-users text-orange-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações do Plano -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="stat-card rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Informações do Plano</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Plano Atual:</span>
                                <span class="text-white font-medium">{{ ucfirst($user->plan_name) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Exportação:</span>
                                <span class="text-white font-medium">
                                    @if($planLimits['can_export'])
                                        <i class="fas fa-check text-green-400"></i> Permitido
                                    @else
                                        <i class="fas fa-times text-red-400"></i> Não Permitido
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">API:</span>
                                <span class="text-white font-medium">
                                    @if($planLimits['can_use_api'])
                                        <i class="fas fa-check text-green-400"></i> Permitido
                                    @else
                                        <i class="fas fa-times text-red-400"></i> Não Permitido
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Membro desde:</span>
                                <span class="text-white font-medium">{{ $user->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Último login:</span>
                                <span class="text-white font-medium">
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('d/m/Y H:i') }}
                                    @else
                                        Nunca
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Workspaces Recentes -->
                    <div class="stat-card rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Workspaces Recentes</h3>
                        <div class="space-y-3">
                            @forelse($recentWorkspaces as $workspace)
                                <div class="workspace-item rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-white">{{ $workspace->name }}</h4>
                                            <p class="text-slate-400 text-sm mt-1">
                                                {{ $workspace->topics_count }} tópicos • 
                                                {{ $workspace->fields_count }} campos
                                            </p>
                                        </div>
                                        <span class="text-xs text-slate-400">
                                            {{ $workspace->updated_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-slate-400 text-center py-4">Nenhum workspace encontrado</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Atividades -->
            <div id="activity" class="tab-content">
                <h3 class="text-lg font-semibold text-white mb-4">Histórico de Atividades</h3>
                <div class="space-y-3">
                    @forelse($activities as $activity)
                        <div class="activity-item rounded-lg p-4 bg-slate-800/50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium text-white">
                                            {{ $activity->method }} {{ $activity->endpoint }}
                                        </span>
                                        <span class="text-xs px-2 py-1 rounded-full 
                                            @if($activity->response_code >= 200 && $activity->response_code < 300) bg-green-500/20 text-green-400
                                            @elseif($activity->response_code >= 400) bg-red-500/20 text-red-400
                                            @else bg-yellow-500/20 text-yellow-400 @endif">
                                            {{ $activity->response_code }}
                                        </span>
                                    </div>
                                    <p class="text-slate-400 text-sm mt-1">
                                        Tempo de resposta: {{ $activity->response_time }}ms
                                    </p>
                                </div>
                                <span class="text-xs text-slate-400">
                                    {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-center py-8">Nenhuma atividade registrada</p>
                    @endforelse
                </div>
            </div>

            <!-- Tab: Workspaces -->
            <div id="workspaces" class="tab-content">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-white">Workspaces do Usuário</h3>
                    <button onclick="inactivateAllWorkspaces({{ $user->id }})" 
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-ban mr-2"></i>Inativar Todos
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($user->workspaces as $workspace)
                        <div class="workspace-item rounded-lg p-6">
                            <div class="flex justify-between items-start mb-4">
                                <h4 class="font-medium text-white">{{ $workspace->name }}</h4>
                                <span class="text-xs px-2 py-1 rounded-full 
                                    @if($workspace->is_active) bg-green-500/20 text-green-400
                                    @else bg-red-500/20 text-red-400 @endif">
                                    {{ $workspace->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                            <div class="space-y-2 text-sm text-slate-400">
                                <div class="flex justify-between">
                                    <span>Tópicos:</span>
                                    <span class="text-white">{{ $workspace->topics_count ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Campos:</span>
                                    <span class="text-white">{{ $workspace->fields_count ?? 0 }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Criado em:</span>
                                    <span class="text-white">{{ $workspace->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="mt-4 flex space-x-2">
                                <button onclick="toggleWorkspaceStatus({{ $workspace->id }}, {{ $workspace->is_active ? 'false' : 'true' }})" 
                                    class="flex-1 px-3 py-2 text-sm 
                                    @if($workspace->is_active) bg-yellow-600 hover:bg-yellow-700
                                    @else bg-green-600 hover:bg-green-700 @endif text-white rounded transition-colors">
                                    {{ $workspace->is_active ? 'Inativar' : 'Ativar' }}
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full">
                            <p class="text-slate-400 text-center py-8">Nenhum workspace encontrado</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tab: Configurações -->
            <div id="settings" class="tab-content">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Informações Básicas -->
                    <div class="stat-card rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-white mb-4">Informações Básicas</h3>
                        <form id="basicInfoForm">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-400 mb-2">Nome</label>
                                    <input type="text" name="name" value="{{ $user->name }}" 
                                        class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-400 mb-2">Sobrenome</label>
                                    <input type="text" name="surname" value="{{ $user->surname }}" 
                                        class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-400 mb-2">Email</label>
                                    <input type="email" name="email" value="{{ $user->email }}" 
                                        class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-400 mb-2">Telefone</label>
                                    <input type="text" name="phone" value="{{ $user->phone ?? '' }}" 
                                        class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white">
                                </div>
                                <button type="button" onclick="updateBasicInfo({{ $user->id }})" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors">
                                    Atualizar Informações
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Gestão de Plano e Status -->
                    <div class="space-y-6">
                        <!-- Alterar Plano -->
                        <div class="stat-card rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-white mb-4">Alterar Plano</h3>
                            <div class="space-y-3">
                                <select id="planSelect" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white">
                                    <option value="free" {{ $user->plan_name == 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="start" {{ $user->plan_name == 'start' ? 'selected' : '' }}>Start</option>
                                    <option value="pro" {{ $user->plan_name == 'pro' ? 'selected' : '' }}>Pro</option>
                                    <option value="premium" {{ $user->plan_name == 'premium' ? 'selected' : '' }}>Premium</option>
                                    <option value="admin" {{ $user->plan_name == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                                <button onclick="updateUserPlan({{ $user->id }})" 
                                    class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg transition-colors">
                                    Alterar Plano
                                </button>
                            </div>
                        </div>

                        <!-- Alterar Status -->
                        <div class="stat-card rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-white mb-4">Alterar Status</h3>
                            <div class="space-y-3">
                                <select id="statusSelect" class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white">
                                    <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inativo</option>
                                    <option value="suspended" {{ $user->status == 'suspended' ? 'selected' : '' }}>Suspenso</option>
                                    <option value="past_due" {{ $user->status == 'past_due' ? 'selected' : '' }}>Pagamento Pendente</option>
                                    <option value="unpaid" {{ $user->status == 'unpaid' ? 'selected' : '' }}>Não Pago</option>
                                    <option value="incomplete" {{ $user->status == 'incomplete' ? 'selected' : '' }}>Incompleto</option>
                                    <option value="trial" {{ $user->status == 'trial' ? 'selected' : '' }}>Trial</option>
                                </select>
                                <button onclick="updateUserStatus({{ $user->id }})" 
                                    class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg transition-colors">
                                    Alterar Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Modal para mostrar nova senha -->
<div id="passwordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-white mb-4">Senha Resetada</h3>
        <p class="text-slate-400 mb-4">A nova senha do usuário é:</p>
        <div class="bg-slate-700 rounded-lg p-4 mb-4">
            <code id="newPassword" class="text-white font-mono text-lg"></code>
        </div>
        <p class="text-sm text-slate-400 mb-4">Esta senha será mostrada apenas uma vez. Salve-a em um local seguro.</p>
        <button onclick="closePasswordModal()" 
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors">
            Fechar
        </button>
    </div>
</div>
@endpush

@push('scripts_end')
<script>
    // Sistema de Tabs
    function switchTab(tabName) {
        // Esconder todas as tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        
        // Mostrar tab selecionada
        document.getElementById(tabName).classList.add('active');
        
        // Atualizar navegação
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-white');
            button.classList.add('border-transparent', 'text-slate-400');
        });
        
        event.target.classList.add('border-blue-500', 'text-white');
        event.target.classList.remove('border-transparent', 'text-slate-400');
    }

    // Reset de Senha
    function resetPassword(userId) {
        if (!confirm('Tem certeza que deseja resetar a senha deste usuário?')) return;

        const url = "{{ route('admin.users.reset-password', ['id' => ':id']) }}".replace(':id', userId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('newPassword').textContent = data.new_password;
                document.getElementById('passwordModal').classList.remove('hidden');
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao resetar senha');
        });
    }

    function closePasswordModal() {
        document.getElementById('passwordModal').classList.add('hidden');
    }

    // Toggle Status do Usuário
    function toggleUserStatus(userId, action) {
        const url = "{{ route('admin.users.toggle-status', ['id' => ':id']) }}".replace(':id', userId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ action: action })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao alterar status');
        });
    }

    // Atualizar Informações Básicas
    function updateBasicInfo(userId) {
        const form = document.getElementById('basicInfoForm');
        const formData = new FormData(form);
        const url = "{{ route('admin.users.update', ['id' => ':id']) }}".replace(':id', userId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Informações atualizadas com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao atualizar informações');
        });
    }

    // Atualizar Plano do Usuário
    function updateUserPlan(userId) {
        const plan = document.getElementById('planSelect').value;
        const url = "{{ route('admin.users.update', ['id' => ':id']) }}".replace(':id', userId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ plan_name: plan })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Plano atualizado com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao atualizar plano');
        });
    }

    // Atualizar Status do Usuário
    function updateUserStatus(userId) {
        const status = document.getElementById('statusSelect').value;
        const url = "{{ route('admin.users.update', ['id' => ':id']) }}".replace(':id', userId);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Status atualizado com sucesso!');
                location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erro ao atualizar status');
        });
    }

    // Inativar Todos os Workspaces
    function inactivateAllWorkspaces(userId) {
        if (!confirm('Tem certeza que deseja inativar todos os workspaces deste usuário?')) return;
        
        // Implementar lógica para inativar todos os workspaces
        alert('Funcionalidade de inativar todos os workspaces será implementada aqui');
    }

    // Toggle Status do Workspace
    function toggleWorkspaceStatus(workspaceId, activate) {
        // Implementar lógica para ativar/inativar workspace individual
        alert('Funcionalidade de toggle workspace será implementada aqui');
    }
</script>
@endpush