@extends('template.template-dashboard')

@section('title', 'Configurações do Workspace')
@section('description', 'Configurações do Workspace')

@push('style')
    <style>
        .toggle-checkbox:checked {
            right: 0;
            border-color: #0d9488;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #0d9488;
        }
        .copied-tooltip {
            opacity: 0;
            transition: opacity 0.3s;
        }
        .show-tooltip {
            opacity: 1;
        }
        .permission-badge {
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
        }
        .collaborator-item {
            transition: all 0.3s ease;
        }

        .settings-card {
            transition: all 0.3s ease;
        }

        .settings-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        /* Animações para o modal */
        .modal-entering {
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-10px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Estilo para os cards de estatística do merge */
        .merge-stat-card {
            transition: all 0.3s ease;
        }

        .merge-stat-card:hover {
            transform: translateY(-2px);
        }

        /* Loading animation */
        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Stats notification animation */
        .stats-notification {
            animation: slideInUp 0.3s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content_dashboard')
    <!-- Header -->
    <div class="max-w-7xl mx-auto p-0 sm:p-0 md:p-6">
        <header class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('workspace.show', ['id' => $workspace->id]) }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-arrow-left text-gray-600 dark:text-gray-300"></i>
                    </a>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Configurações do Workspace</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $workspace->title }}</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navegação por Abas -->
        <div class="border-b border-gray-200 dark:border-gray-700 mb-8">
            <div class="flex space-x-8">
                <button class="tab-button relative py-4 px-1 text-sm font-medium text-gray-700 dark:text-gray-300" 
                        data-tab="tab-overview">
                    <i class="fas fa-chart-bar mr-2"></i>Visão Geral
                    <div class="tab-border absolute bottom-0 left-0 w-full h-0.5 bg-teal-500"></div>
                </button>
                <button class="tab-button relative py-4 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300" 
                        data-tab="tab-security">
                    <i class="fas fa-shield-alt mr-2"></i>Segurança & API
                </button>
                <button class="tab-button relative py-4 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300" 
                        data-tab="tab-access">
                    <i class="fas fa-users mr-2"></i>Controle de Acesso @free @include("components.badges.upgrade-badge")@endfree
                </button>
            </div>
        </div>

        {{-- Conteúdo Principal --}}
        
            @include('components.alerts.alert')
            {{-- Aba 1: Visão geral --}}
            @include('components.tabs.workspace-settings-overview-tab', $workspace)

            <!-- Aba 2: Segurança & API -->
            @include('components.tabs.workspace-settings-security-tab', $workspace)

            <!-- Aba 2: Controle de Acesso -->
            @include('components.tabs.workspace-settings-control-access-tab', [$workspace, $hasPasswordWorkspace])
    </div>
@endsection

@push('modals')
    @include('components.modals.modal-delete-workspace')
    @include('components.modals.modal-duplicate-workspace')
@endpush

<script type="module">
    import '/js/modules/tab/SettingsWorkspaceTabManager.js'
</script>

<script>
// Gerenciamento de Solicitações de Edição (usando workspace_collaborators)
function initializeEditRequests() {
    loadPendingEditRequests();
    loadEditRequestsHistory();
}

function loadPendingEditRequests() {
    $.ajax({
        url: `{{ route('workspace.edit-requests', ['id' => $workspace->id]) }}`,
        method: 'GET',
        success: function(response) {
            renderPendingEditRequests(response.data);
        },
        error: function(error) {
            console.error('Erro ao carregar solicitações:', error);
            $('#edit-requests-list').html(`
                <div class="text-center py-4 text-red-500">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Erro ao carregar solicitações.
                </div>
            `);
        }
    });
}

function renderPendingEditRequests(requests) {
    const container = $('#edit-requests-list');
    
    if (requests.length === 0) {
        container.html(`
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl mb-4 opacity-50"></i>
                <p>Nenhuma solicitação pendente.</p>
            </div>
        `);
        return;
    }

    container.empty();
    
    requests.forEach(request => {
        const requestItem = `
            <div class="edit-request-item bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-semibold">
                                ${getInitials(request.user_name)}
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">${request.user_name}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">${request.user_email}</p>
                                <p class="text-xs text-gray-400">Será adicionado como: <span class="font-medium">${request.role}</span></p>
                            </div>
                            <span class="badge bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Solicitação de Edição</span>
                        </div>
                        ${request.message ? `
                            <div class="bg-white dark:bg-gray-700 rounded p-3 mt-2">
                                <p class="text-sm text-gray-600 dark:text-gray-300">${request.message}</p>
                            </div>
                        ` : ''}
                        <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <i class="fas fa-clock"></i>
                            <span>Solicitado em: ${formatDate(request.requested_at)}</span>
                        </div>
                    </div>
                    <div class="flex space-x-2 ml-4">
                        <button 
                            onclick="approveEditRequest(${request.id})" 
                            class="approve-request-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                        >
                            <i class="fas fa-check mr-1"></i>Aprovar
                        </button>
                        <button 
                            onclick="showRejectModal(${request.id})" 
                            class="reject-request-btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                        >
                            <i class="fas fa-times mr-1"></i>Rejeitar
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.append(requestItem);
    });
}   

function loadEditRequestsHistory() {
    $.ajax({
        url: `{{ route('workspace.edit-requests.history', ['id' => $workspace->id]) }}`,
        method: 'GET',
        success: function(response) {
            renderEditRequestsHistory(response.data);
        },
        error: function(error) {
            console.error('Erro ao carregar histórico:', error);
        }
    });
}

function renderEditRequestsHistory(requests) {
    const container = $('#edit-requests-history');
    
    if (requests.length === 0) {
        container.html(`
            <div class="text-center py-4 text-gray-500">
                <p>Nenhuma solicitação no histórico.</p>
            </div>
        `);
        return;
    }

    container.empty();
    
    requests.forEach(request => {
        const statusBadge = request.status === 'approved' ? 
            '<span class="badge bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Aprovado</span>' :
            '<span class="badge bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Rejeitado</span>';
        
        const approvedBy = request.approved_by ? `por ${request.approved_by.name}` : '';
        
        const historyItem = `
            <div class="history-item bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                            ${getInitials(request.requested_by_name)}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">${request.requested_by_name}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                ${statusBadge} • ${formatDate(request.updated_at)} ${approvedBy}
                            </p>
                        </div>
                    </div>
                </div>
                ${request.rejected_reason ? `
                    <div class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                        <strong>Motivo:</strong> ${request.rejected_reason}
                    </div>
                ` : ''}
            </div>
        `;
        container.append(historyItem);
    });
}

function approveEditRequest(requestId) {
    if (!confirm('Tem certeza que deseja aprovar esta solicitação? O usuário será adicionado como colaborador editor.')) {
        return;
    }

    $.ajax({
        url: `{{ route('edit-requests.approve', '') }}/${requestId}`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function(response) {
            showAlert(response.message, 'success');
            loadPendingEditRequests();
            loadEditRequestsHistory();
            loadCollaborators(); // Recarregar lista de colaboradores
        },
        error: function(error) {
            const message = error.responseJSON?.message || 'Erro ao aprovar solicitação';
            showAlert(message, 'error');
        }
    });
}

function showRejectModal(requestId) {
    const reason = prompt('Digite o motivo da rejeição (opcional):');
    if (reason !== null) {
        rejectEditRequest(requestId, reason);
    }
}

function rejectEditRequest(requestId, reason) {
    $.ajax({
        url: `{{ route('edit-requests.reject', '') }}/${requestId}`,
        method: 'POST',
        data: {
            reason: reason,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            showAlert(response.message, 'info');
            loadPendingEditRequests();
            loadEditRequestsHistory();
        },
        error: function(error) {
            const message = error.responseJSON?.message || 'Erro ao rejeitar solicitação';
            showAlert(message, 'error');
        }
    });
}

// Funções auxiliares
function getInitials(name) {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}


// Configuração do modal de duplicação
document.addEventListener('DOMContentLoaded', function() {
    // Variáveis globais
    let currentWorkspaceId = null;
    
    // Event listeners para os botões de duplicação
    document.querySelectorAll('.duplicate-workspace-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentWorkspaceId = this.dataset.workspaceId;
            const workspaceTitle = this.dataset.workspaceTitle;
            const topicsCount = this.dataset.topicsCount;
            const fieldsCount = this.dataset.fieldsCount;
            
            // Preencher informações do modal
            document.getElementById('new_title').value = workspaceTitle + ' - Cópia';
            document.getElementById('topicsCount').textContent = `• ${topicsCount} tópicos`;
            document.getElementById('fieldsCount').textContent = `• ${fieldsCount} campos`;
            
            // Limpar mensagens de erro
            document.getElementById('errorMessage').classList.add('hidden');
        });
    });
    
    // Submissão do formulário via AJAX
    document.getElementById('duplicateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('duplicateSubmitBtn');
        const errorDiv = document.getElementById('errorMessage');
        const originalText = submitBtn.innerHTML;
        
        // Mostrar loading
        submitBtn.innerHTML = '🔄 Duplicando...';
        submitBtn.disabled = true;
        errorDiv.classList.add('hidden');
        
        // Fazer requisição AJAX
        fetch(`{{route('workspace.duplicate', ['id' => $workspace->id])}}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                new_title: document.getElementById('new_title').value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Sucesso - redirecionar para o novo workspace
                window.location.href = data.data.redirect_url;
            } else {
                // Erro - mostrar mensagem
                errorDiv.textContent = data.message || 'Erro ao duplicar workspace';
                errorDiv.classList.remove('hidden');
                
                // Focar no campo de erro se for de título
                if (data.error === 'title_exists') {
                    document.getElementById('new_title').focus();
                }
            }
        })
        .catch(error => {
            errorDiv.textContent = 'Erro de conexão. Tente novamente.';
            errorDiv.classList.remove('hidden');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Fechar modal ao pressionar ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('duplicateModal');
            if (!modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        }
    });
    
    // Auto-focus no campo de texto quando modal abrir
    const modal = document.getElementById('duplicateModal');
    modal.addEventListener('shown', function() {
        document.getElementById('new_title').focus();
        document.getElementById('new_title').select();
    });

    // Inicializar gerenciamento de solicitações
    @if(auth()->user()->isPro() || auth()->user()->isAdmin())
        initializeEditRequests();
    @endif
});
</script>

    <script type="module">
        import '/js/modules/workspace/settings-interations.js';
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //COLLABORATORS
            @if(auth()->user()->isStart() || auth()->user()->isPro() || auth()->user()->isPremium() || auth()->user()->isAdmin())
                initializeAccessControl();
            @endif


            // Funcionalidade das abas
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.getAttribute('data-tab');
                    
                    // Remove a classe active de todos os botões e conteúdos
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('hidden'));
                    
                    // Adiciona a classe active ao botão e conteúdo clicado
                    button.classList.add('active');
                    document.getElementById(tabId).classList.add('hidden');
                });
            });
            
            
                        
            // Validar email
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            // Gerar cor aleatória para o avatar
            function getRandomColor() {
                const colors = [
                    'bg-teal-500', 'bg-blue-500', 'bg-purple-500', 
                    'bg-pink-500', 'bg-red-500', 'bg-orange-500', 
                    'bg-yellow-500', 'bg-green-500', 'bg-indigo-500'
                ];
                return colors[Math.floor(Math.random() * colors.length)];
            }

            
            function initializeAccessControl() {
                // Carregar colaboradores inicialmente
                loadCollaborators();

                // Evento para adicionar colaborador
                $('#add-collaborator').click(function() {
                    inviteCollaborator();
                });

                // Evento para remover colaborador (delegação)
                $(document).on('click', '.remove-collaborator-btn', function() {
                    const collaboratorId = $(this).data('id');
                    removeCollaborator(collaboratorId);
                });

                // Evento para editar permissões
                $(document).on('click', '.edit-permissions-btn', function() {
                    const collaboratorId = $(this).data('id');
                    showEditModal(collaboratorId);
                });

                // Enter no campo de email
                $('#collaborator-email').keypress(function(e) {
                    if (e.which === 13) {
                        inviteCollaborator();
                    }
                });
            }

            function showAlert(message, type = 'info') {
                // Implemente seu sistema de alertas aqui
                const alert = $(`
                    <div class="alert alert-${type} mb-4 p-3 rounded-lg">
                        ${message}
                    </div>
                `);
                
                $('#alerts-container').append(alert);
                setTimeout(() => alert.remove(), 5000);
            }

            const workspaceId = {{ $workspace->id }};
    
            

            async function loadCollaborators() {
                $.ajax({
                    url: `{{ route('workspace.collaborators.list', ['workspaceId' => $workspace->id]) }}`,
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response){
                        renderCollaborators(response.data);
                        updateStatistics(response.data);

                    },
                    error: function(error){
                        showAlert('Erro ao carregar colaboradores.', 'error');
                        console.error('Error:', error);
                    },
                    complete: function(){

                    } 
                });
            }

            function renderCollaborators(collaborators) {
                const container = $('#collaborators-list');
                container.empty();

                if (collaborators.length === 0) {
                    container.html(`
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-users text-4xl mb-4 opacity-50"></i>
                            <p>Nenhum colaborador ainda.</p>
                            <p class="text-sm mt-2">Use o formulário acima para convidar alguém.</p>
                        </div>
                    `);
                    return;
                }

                collaborators.forEach(collab => {
                    const avatar = collab.avatar || generateAvatar(collab.email);
                    const statusBadge = collab.status === 'accepted' ? 
                        '<span class="badge bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Aceito</span>' :
                        '<span class="badge bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pendente</span>';

                    const roleBadge = getRoleBadge(collab.role);

                    const item = `
                        <div class="collaborator-item bg-gray-50 dark:bg-gray-700 rounded-lg p-4 hover:bg-slate-600" data-id="${collab.id}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <img src="${avatar}" alt="${collab.email}" class="w-10 h-10 rounded-full">
                                    <div>
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="font-medium text-gray-900 dark:text-white">${collab.email}</span>
                                            ${statusBadge}
                                        </div>
                                        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                            ${roleBadge}
                                            <span>•</span>
                                            <span>Convite por: ${collab.invited_by}</span>
                                            <span>•</span>
                                            <span>${formatDate(collab.invited_at)}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex space-x-4">
                                    <select id="collaborator-role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-32 py-1 px-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                        <option value="viewer">Visualizador</option>
                                        <option value="editor">Editor</option>
                                    </select>
                                    <button class="remove-collaborator-btn text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300" data-id="${collab.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(item);
                });
            }

                function inviteCollaborator() {
                    const email = $('#collaborator-email').val().trim();
                    const role = $('#collaborator-role').val();

                    if (!isValidEmail(email)) {
                        showAlert('Por favor, insira um email válido.', 'error');
                        return;
                    }

                    $('#add-collaborator').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enviando...');

                    $.ajax({
                        url: `{{ route('workspace.collaborator.invite', ['workspaceId' => $workspace->id] ) }}`,
                        method: 'POST',
                        data: {
                            email: email,
                            role: role,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data){
                            showAlert('Convite enviado com sucesso!', 'success');
                            $('#collaborator-email').val('');
                            loadCollaborators();

                        },
                        error: function() {
                            if (!response.data.user_exists) {
                                showAlert('Um email de convite foi enviado para o usuário.', 'info');
                            }
                            const message = error.responseJSON?.message || 'Erro ao enviar convite';
                            showAlert(message, 'error');
                        },
                        complete: function() {
                            // Restaura o botão
                            $('#add-collaborator').prop('disabled', false).html('<i class="fas fa-plus mr-1"></i> Convidar');
                        }
                    });
                }

                async function removeCollaborator(collaboratorId) {
                    if (!confirm('Tem certeza que deseja remover este colaborador?')) {
                        return;
                    }

                    $.ajax({
                        url: `/workspace/collaborators/{{$workspace->id}}/${collaboratorId}`,
                        method: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(){
                            showAlert('Colaborador removido com sucesso.', 'success');
                            loadCollaborators();
                        },
                        error: function(){
                            const message = error.responseJSON?.message || 'Erro ao remover colaborador';
                            showAlert(message, 'error');
                        }, 
                        complete: function(){

                        }
                    });
                }

                // Funções auxiliares
                function isValidEmail(email) {
                    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return re.test(email);
                }

                function generateAvatar(email) {
                    const hash = email.toLowerCase();
                    return `https://www.gravatar.com/avatar/${hash}?d=identicon&s=80`;
                }

                function getRoleBadge(role) {
                    const badges = {
                        owner: '<span class="badge bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Proprietário</span>',
                        admin: '<span class="badge bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Administrador</span>',
                        editor: '<span class="badge bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Editor</span>',
                        viewer: '<span class="badge bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Visualizador</span>'
                    };
                    return badges[role] || badges.viewer;
                }

                function formatDate(dateString) {
                    return new Date(dateString).toLocaleDateString('pt-BR');
                }

                function updateStatistics(collaborators) {
                    const total = collaborators.length;
                    const active = collaborators.filter(c => c.status === 'active').length;
                    const pending = collaborators.filter(c => c.status === 'pending').length;

                    $('#total-collaborators').text(total);
                    $('#active-collaborators').text(active);
                    $('#pending-collaborators').text(pending);
                }


                
                
            });
        </script>