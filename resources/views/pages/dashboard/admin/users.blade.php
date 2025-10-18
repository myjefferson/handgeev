@extends('template.template-dashboard')

@section('title', 'Gestão de Usuários')
@section('description', 'Controle completo de usuários do sistema')

@push('style')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border: 1px solid #475569;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #2dd4bf;
        display: block;
    }
    
    .stat-label {
        color: #94a3b8;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .filter-badge {
        background: #475569;
        color: #e2e8f0;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .filter-badge .remove {
        cursor: pointer;
        margin-left: 0.25rem;
    }
    
    .user-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .action-btn {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        border: 1px solid;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
    }
    
    .btn-suspend {
        background: #fef3c7;
        border-color: #f59e0b;
        color: #92400e;
    }
    
    .btn-activate {
        background: #d1fae5;
        border-color: #10b981;
        color: #065f46;
    }
    
    .btn-reset {
        background: #dbeafe;
        border-color: #3b82f6;
        color: #1e40af;
    }
    
    .activity-log {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .activity-item {
        padding: 0.75rem;
        border-bottom: 1px solid #374151;
        font-size: 0.875rem;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-time {
        color: #9ca3af;
        font-size: 0.75rem;
    }
</style>
@endpush

@section('content_dashboard')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header e Estatísticas -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Gestão de Usuários</h1>
                <p class="text-slate-400 mt-2">Controle completo de usuários e permissões do sistema</p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-teal-400">{{ $users->total() }}</div>
                <div class="text-slate-400 text-sm">Total de Usuários</div>
            </div>
        </div>
        
        <!-- Estatísticas Rápidas -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number">{{ $users->where('status', 'active')->count() }}</span>
                <span class="stat-label">Ativos</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">{{ $users->where('status', 'suspended')->count() }}</span>
                <span class="stat-label">Suspensos</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">{{ $users->where('plan_name', 'pro')->count() }}</span>
                <span class="stat-label">Plano Pro</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">{{ $users->where('plan_name', 'premium')->count() }}</span>
                <span class="stat-label">Plano Premium</span>
            </div>
        </div>
    </div>
    
    <!-- Filtros Ativos -->
    @if(request()->hasAny(['search', 'plan', 'status']))
    <div class="bg-slate-800 rounded-lg p-4 mb-6 border border-slate-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-slate-400">Filtros ativos:</span>
                @if(request('search'))
                <span class="filter-badge">
                    Pesquisa: "{{ request('search') }}"
                    <span class="remove" onclick="removeFilter('search')">×</span>
                </span>
                @endif
                @if(request('plan'))
                <span class="filter-badge">
                    Plano: {{ ucfirst(request('plan')) }}
                    <span class="remove" onclick="removeFilter('plan')">×</span>
                </span>
                @endif
                @if(request('status'))
                <span class="filter-badge">
                    Status: {{ ucfirst(request('status')) }}
                    <span class="remove" onclick="removeFilter('status')">×</span>
                </span>
                @endif
            </div>
            <button onclick="clearAllFilters()" class="text-slate-400 hover:text-slate-300 text-sm">
                Limpar todos
            </button>
        </div>
    </div>
    @endif
    
    <!-- Barra de Pesquisa e Filtros -->
    <div class="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-8">
        <form id="filterForm" method="GET" action="{{ route('admin.users') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Pesquisa -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Pesquisar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Nome, email, sobrenome..." 
                               class="pl-10 pr-4 py-3 w-full bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent">
                    </div>
                </div>
                
                <!-- Filtro de Plano -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Plano</label>
                    <select name="plan" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-3 focus:outline-none focus:ring-2 focus:ring-teal-400">
                        <option value="">Todos os planos</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan }}" {{ request('plan') == $plan ? 'selected' : '' }}>
                                {{ ucfirst($plan) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtro de Status -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Status</label>
                    <select name="status" class="w-full bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-3 focus:outline-none focus:ring-2 focus:ring-teal-400">
                        <option value="">Todos os status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="flex justify-between items-center mt-4">
                <div class="text-slate-400 text-sm">
                    Mostrando {{ $users->firstItem() }} - {{ $users->lastItem() }} de {{ $users->total() }} resultados
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="clearAllFilters()" 
                            class="px-4 py-2 bg-slate-700 border border-slate-600 text-slate-300 rounded-lg hover:bg-slate-600 transition-colors">
                        Limpar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-500 transition-colors">
                        Aplicar Filtros
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Tabela de Usuários -->
    <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-700">
                <thead class="bg-slate-750">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider cursor-pointer" onclick="sortTable('name')">
                            Usuário
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider cursor-pointer" onclick="sortTable('plan_name')">
                            Plano
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider cursor-pointer" onclick="sortTable('status')">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Estatísticas
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-slate-800 divide-y divide-slate-700">
                    @forelse ($users as $user)
                    <tr class="hover:bg-slate-750 transition-colors" id="user-row-{{ $user->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 bg-teal-500 rounded-full flex items-center justify-center">
                                    <span class="font-medium text-slate-900">
                                        {{ substr($user->name, 0, 1) }}{{ substr($user->surname, 0, 1) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-white">{{ $user->name }} {{ $user->surname }}</div>
                                    <div class="text-sm text-slate-400">{{ $user->email }}</div>
                                    <div class="text-xs text-slate-500">
                                        Cadastro: {{ $user->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="role-badge 
                                @if($user->plan_name == 'admin') bg-blue-100 text-blue-800
                                @elseif($user->plan_name == 'premium') bg-purple-100 text-purple-800
                                @elseif($user->plan_name == 'pro') bg-indigo-100 text-indigo-800
                                @elseif($user->plan_name == 'start') bg-green-100 text-green-800
                                @else bg-slate-100 text-slate-800 @endif">
                                {{ $user->plan_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($user->status === "active")
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Ativo
                            </span>
                            @elseif($user->status === "suspended")
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Suspenso
                            </span>
                            @elseif($user->status === "inactive")
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                Inativo
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-layer-group text-xs text-teal-400"></i>
                                    <span>{{ $user->workspaces_count ?? 0 }} workspaces</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-tags text-xs text-blue-400"></i>
                                    <span>{{ $user->topics_count ?? 0 }} tópicos</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="user-actions">
                                <button class="text-teal-400 hover:text-teal-300 edit-user" 
                                    data-user-id="{{ $user->id }}"
                                    data-user-data="{{ json_encode($user) }}"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                @if($user->status === 'active')
                                <button class="action-btn btn-suspend toggle-status" 
                                    data-user-id="{{ $user->id }}"
                                    data-action="suspend"
                                    title="Suspender">
                                    <i class="fas fa-pause"></i>
                                </button>
                                @else
                                <button class="action-btn btn-activate toggle-status" 
                                    data-user-id="{{ $user->id }}"
                                    data-action="activate"
                                    title="Ativar">
                                    <i class="fas fa-play"></i>
                                </button>
                                @endif
                                
                                <button class="action-btn btn-reset reset-password" 
                                    data-user-id="{{ $user->id }}"
                                    title="Redefinir Senha">
                                    <i class="fas fa-key"></i>
                                </button>
                                
                                <button class="text-red-400 hover:text-red-300 delete-user" 
                                    data-user-id="{{ $user->id }}"
                                    title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                                
                                <button class="text-purple-400 hover:text-purple-300 view-activities" 
                                    data-user-id="{{ $user->id }}"
                                    title="Ver Atividades">
                                    <i class="fas fa-history"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                            <i class="fas fa-users text-2xl mb-2"></i>
                            <p>Nenhum usuário encontrado</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        @if($users->hasPages())
        <div class="bg-slate-750 px-6 py-4 flex items-center justify-between border-t border-slate-700">
            <div class="flex-1 flex justify-between items-center">
                <div>
                    <p class="text-sm text-slate-400">
                        Mostrando
                        <span class="font-medium">{{ $users->firstItem() }}</span>
                        a
                        <span class="font-medium">{{ $users->lastItem() }}</span>
                        de
                        <span class="font-medium">{{ $users->total() }}</span>
                        resultados
                    </p>
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal de Edição -->
@include('components.modals.modal-admin-editar-usuario')

<!-- Modal de Atividades -->
{{-- @include('components.modals.modal-user-activities') --}}

<!-- Modal de Estatísticas -->
{{-- @include('components.modals.modal-user-stats') --}}
@endpush

@push('scripts')
<script>
// Funções de Filtro
function removeFilter(filterName) {
    const url = new URL(window.location.href);
    url.searchParams.delete(filterName);
    window.location.href = url.toString();
}

function clearAllFilters() {
    window.location.href = "{{ route('admin.users') }}";
}

function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort');
    const currentDirection = url.searchParams.get('direction');
    
    let newDirection = 'asc';
    if (currentSort === column && currentDirection === 'asc') {
        newDirection = 'desc';
    }
    
    url.searchParams.set('sort', column);
    url.searchParams.set('direction', newDirection);
    window.location.href = url.toString();
}

// Lógica JavaScript para ações
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Toggle Status (Suspender/Ativar)
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const action = this.getAttribute('data-action');
            const actionText = action === 'suspend' ? 'suspender' : 'ativar';
            
            if (confirm(`Tem certeza que deseja ${actionText} este usuário?`)) {
                fetch(`/admin/users/${userId}/toggle-status`, {
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
                        location.reload();
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao alterar status do usuário');
                });
            }
        });
    });
    
    // Reset de Senha
    document.querySelectorAll('.reset-password').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            
            if (confirm('Tem certeza que deseja redefinir a senha deste usuário? Uma nova senha será gerada.')) {
                fetch(`/admin/users/${userId}/reset-password`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Senha redefinida com sucesso! Nova senha: ${data.new_password}`);
                    } else {
                        alert('Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    alert('Erro ao redefinir senha');
                });
            }
        });
    });
    
    // Ver Atividades
    document.querySelectorAll('.view-activities').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            // Implementar modal de atividades
            console.log('Abrir atividades do usuário:', userId);
        });
    });
    
    // Auto-submit do form quando mudar filtros
    document.querySelectorAll('select[name="plan"], select[name="status"]').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
});
</script>
@endpush