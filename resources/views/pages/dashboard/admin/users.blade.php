@extends('template.template-dashboard')

@section('title', 'Usuários')
@section('description', 'Usuários')

@push('style')
    <style>
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #f1f5f9;
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }
        
        .search-box:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(45, 212, 191, 0.3);
        }
    </style>
@endpush

@section('content_dashboard')
    <div class="max-w-7xl mx-auto px-4 sm:px-4 lg:px-4 py-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Gerenciamento de Usuários</h1>
            <p class="text-slate-400 mt-2">Controle as permissões e regras de acesso dos usuários</p>
        </div>
        
        <!-- Barra de Pesquisa e Filtros -->
        <div class="bg-slate-800 rounded-xl p-5 border border-slate-700 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400"></i>
                    </div>
                    <input type="text" id="searchInput" placeholder="Pesquisar usuários por nome, email..." 
                           class="pl-10 pr-4 py-3 w-full bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 search-box">
                    
                </div>
                
                <div class="flex gap-3">
                    <select id="roleFilter" class="bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2">
                        <option value="">Todos os perfis</option>
                        <option value="admin">Administrador</option>
                        <option value="pro">Pro</option>
                        <option value="free">Free</option>
                    </select>
                    
                    <select id="statusFilter" class="bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2">
                        <option value="">Todos os status</option>
                        <option value="active">Ativo</option>
                        <option value="inactive">Inativo</option>
                        <option value="suspended">Suspenso</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Tabela de Usuários -->
        <div class="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-700">
                    <thead class="bg-slate-750">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Usuário
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Perfil/Plano
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Status
                            </th>
                            {{-- <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                Último Acesso
                            </th> --}}
                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-slate-800 divide-y divide-slate-700">
                        <!-- Exemplo de dados - estes seriam substituídos por dados reais do backend -->
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-750 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 bg-teal-500 rounded-full flex items-center justify-center">
                                            <span class="font-medium text-slate-900">{{ !empty($user->name) ? $user->name[0] : ''}}{{ !empty($user->surname) ? $user->surname[0] : '' }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-white">{{ $user->name }} {{ $user->surname }}</div>
                                            <div class="text-sm text-slate-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="role-badge 
                                        @if($user->plan_name == 'admin') bg-blue-100 text-blue-800
                                        @elseif($user->plan_name == 'pro') bg-purple-100 text-purple-800
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
                                {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                                    {{ $user->last_login ? $user->last_login->format('d/m/Y H:i') : 'Nunca acessou' }}
                                </td> --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-teal-400 hover:text-teal-300 mr-3 edit-user" 
                                        data-modal-target="editUserModal" 
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-surname="{{ $user->surname }}"
                                        data-email="{{ $user->email }}"
                                        data-role="{{ $user->plan_name }}"
                                        data-status="{{ $user->is_active ? 'active' : 'inactive' }}"
                                        type="button">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-red-400 hover:text-red-300 delete-user" data-id="{{ $user->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-slate-400">
                                    Nenhum usuário encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <div class="bg-slate-750 px-6 py-4 flex items-center justify-between border-t border-slate-700">
                <div class="flex-1 flex justify-between items-center">
                    <div>
                        <p class="text-sm text-slate-400">
                            Mostrando
                            <span class="font-medium">1</span>
                            a
                            <span class="font-medium">3</span>
                            de
                            <span class="font-medium">24</span>
                            resultados
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-slate-600 bg-slate-700 text-sm font-medium text-slate-300 hover:bg-slate-600">
                                <span class="sr-only">Anterior</span>
                                <i class="fas fa-chevron-left w-5 h-5"></i>
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-slate-600 bg-slate-800 text-sm font-medium text-white">
                                1
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-slate-600 bg-slate-700 text-sm font-medium text-slate-300 hover:bg-slate-600">
                                2
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-slate-600 bg-slate-700 text-sm font-medium text-slate-300 hover:bg-slate-600">
                                3
                            </a>
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-slate-600 bg-slate-700 text-sm font-medium text-slate-300 hover:bg-slate-600">
                                <span class="sr-only">Próximo</span>
                                <i class="fas fa-chevron-right w-5 h-5"></i>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@push('modals')
    <!-- Modal de Edição (exemplo) -->
    @include('components.modals.modal-editar-usuario-admin')
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Lógica do Modal de Edição ---
            const editModal = document.getElementById('editUserModal');
            const editForm = editModal.querySelector('form');
            const editButtons = document.querySelectorAll('.edit-user');
            const closeModalButtons = document.querySelectorAll('[data-modal-hide="editUserModal"]');
            const saveButton = editForm.querySelector('.save-edit');

            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    const userName = this.getAttribute('data-name');
                    const userSurname = this.getAttribute('data-surname');
                    const userEmail = this.getAttribute('data-email');
                    const userRole = this.getAttribute('data-role');
                    const userStatus = this.getAttribute('data-status');
                    
                    document.getElementById('edit_user_id').value = userId;
                    document.getElementById('edit_user_name').value = userName;
                    document.getElementById('edit_user_surname').value = userSurname;
                    document.getElementById('edit_user_email').value = userEmail;
                    document.getElementById('edit_user_role').value = userRole;
                    document.getElementById('edit_user_status').value = userStatus;
                    
                    editModal.classList.remove('hidden');
                });
            });

            // --- Lógica de Submissão AJAX para Edição ---
            saveButton.addEventListener('click', function() {
                const userId = document.getElementById('edit_user_id').value;
                const userRole = document.getElementById('edit_user_role').value;
                const userStatus = document.getElementById('edit_user_status').value;
                const url = "{{ route('admin.users.update', ['id' => ':id']) }}";
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                // Exibir um estado de "carregando"
                this.innerHTML = '<i class="fas fa-spinner animate-spin"></i> Salvando...';
                this.disabled = true;

                $.ajax({
                    url: url.replace(':id', userId),
                    type: 'PUT', // ou 'PATCH'
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    data: {
                        plan_name: userRole,
                        status: userStatus
                    },
                    success: function(data) {
                        console.log('Sucesso:', data);
                        alert('Usuário atualizado com sucesso!');
                        $('.edit-modal').addClass('hidden');
                        // Opcional: recarregar a página ou atualizar a linha da tabela
                        location.reload(); 
                    },
                    error: function(xhr, status, error) {
                        console.error('Erro:', error);
                        alert('Ocorreu um erro ao atualizar o usuário: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
                    },
                    done: function() {
                        this.innerHTML = 'Salvar';
                        this.disabled = false;
                    }
                });
            });

            // --- Lógica de Exclusão AJAX ---
            document.querySelectorAll('.delete-user').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-id');
                    const url = "{{ route('admin.users.delete', ['id' => ':id']) }}";
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
                        
                        this.innerHTML = '<i class="fas fa-spinner animate-spin"></i>';
                        this.disabled = true;

                        $.ajax({
                            url: url.replace(':id', userId),
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: function(data) {
                                console.log('Exclusão bem-sucedida:', data);
                                alert('Usuário excluído com sucesso!');
                                // Remover a linha da tabela sem recarregar a página
                                const row = this.closest('tr');
                                row.remove();
                            },
                            error: function(xhr, status, error) {
                                console.error('Erro na exclusão:', error);
                                alert('Ocorreu um erro ao excluir o usuário.');
                            },
                            done: function() {
                                this.innerHTML = '<i class="fas fa-trash"></i>';
                                this.disabled = false;
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush