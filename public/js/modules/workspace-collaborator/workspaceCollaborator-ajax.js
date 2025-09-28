class Collaborator {
    constructor(workspaceId) {
        this.workspaceId = workspaceId;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadCollaborators();
    }

    bindEvents() {
        // Adicionar colaborador
        $('#add-collaborator-btn').on('click', () => this.inviteCollaborator());
        
        // Remover colaborador
        $(document).on('click', '.remove-collaborator-btn', (e) => {
            const collaboratorId = $(e.currentTarget).data('id');
            this.removeCollaborator(collaboratorId);
        });

        // Editar permissões
        $(document).on('click', '.edit-permissions-btn', (e) => {
            const collaboratorId = $(e.currentTarget).data('id');
            this.showEditModal(collaboratorId);
        });

        // Pesquisa em tempo real
        $('#collaborator-search').on('input', (e) => {
            this.filterCollaborators(e.target.value);
        });
    }

    async inviteCollaborator() {
        const email = $('#collaborator-email').val().trim();
        const role = $('#collaborator-role').val();

        if (!this.isValidEmail(email)) {
            this.showAlert('Por favor, insira um email válido.', 'error');
            return;
        }

        try {
            const response = await $.ajax({
                url: `/api/workspace/${this.workspaceId}/collaborators/invite`,
                method: 'POST',
                data: {
                    email: email,
                    role: role,
                    _token: CSRF_TOKEN
                }
            });

            if (response.success) {
                this.showAlert('Convite enviado com sucesso!', 'success');
                $('#collaborator-email').val('');
                this.loadCollaborators();
                
                if (!response.data.user_exists) {
                    this.showInfo('Um email de convite foi enviado para o usuário.');
                }
            }
        } catch (error) {
            this.handleError(error);
        }
    }

    async loadCollaborators() {
        try {
            const response = await $.ajax({
                url: `/api/workspace/${this.workspaceId}/collaborators`,
                method: 'GET'
            });

            if (response.success) {
                this.renderCollaborators(response.data);
            }
        } catch (error) {
            this.handleError(error);
        }
    }

    renderCollaborators(collaborators) {
        const container = $('#collaborators-list');
        container.empty();

        if (collaborators.length === 0) {
            container.html(`
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-users text-4xl mb-4 opacity-50"></i>
                    <p>Nenhum colaborador ainda.</p>
                </div>
            `);
            return;
        }

        collaborators.forEach(collab => {
            const avatar = collab.avatar || this.generateAvatar(collab.email);
            const statusBadge = collab.status === 'active' ? 
                '<span class="badge bg-green-100 text-green-800">Ativo</span>' :
                '<span class="badge bg-yellow-100 text-yellow-800">Pendente</span>';

            const item = `
                <div class="collaborator-item" data-id="${collab.id}" data-email="${collab.email}">
                    <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border">
                        <div class="flex items-center space-x-4">
                            <img src="${avatar}" alt="${collab.email}" class="w-10 h-10 rounded-full">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-medium">${collab.email}</span>
                                    ${statusBadge}
                                </div>
                                <div class="text-sm text-gray-500">
                                    ${this.getRoleBadge(collab.role)} • 
                                    Convite por: ${collab.invited_by}
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button class="edit-permissions-btn text-blue-600 hover:text-blue-800" data-id="${collab.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="remove-collaborator-btn text-red-600 hover:text-red-800" data-id="${collab.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.append(item);
        });
    }

    async removeCollaborator(collaboratorId) {
        if (!confirm('Tem certeza que deseja remover este colaborador?')) {
            return;
        }

        try {
            const response = await $.ajax({
                url: `/api/workspace/${this.workspaceId}/collaborators/${collaboratorId}`,
                method: 'DELETE',
                data: { _token: CSRF_TOKEN }
            });

            if (response.success) {
                this.showAlert('Colaborador removido com sucesso.', 'success');
                this.loadCollaborators();
            }
        } catch (error) {
            this.handleError(error);
        }
    }

    showEditModal(collaboratorId) {
        // Implementar modal de edição
        const collaborator = this.findCollaborator(collaboratorId);
        
        $('#edit-role-modal [name="role"]').val(collaborator.role);
        $('#edit-role-modal').data('collaborator-id', collaboratorId);
        $('#edit-role-modal').show();
    }

    async updateCollaboratorRole() {
        const collaboratorId = $('#edit-role-modal').data('collaborator-id');
        const newRole = $('#edit-role-modal [name="role"]').val();

        try {
            const response = await $.ajax({
                url: `/api/workspace/${this.workspaceId}/collaborators/${collaboratorId}/role`,
                method: 'PUT',
                data: {
                    role: newRole,
                    _token: CSRF_TOKEN
                }
            });

            if (response.success) {
                this.showAlert('Permissão atualizada com sucesso.', 'success');
                $('#edit-role-modal').hide();
                this.loadCollaborators();
            }
        } catch (error) {
            this.handleError(error);
        }
    }

    // Helper methods
    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    generateAvatar(email) {
        const hash = md5(email.toLowerCase());
        return `https://www.gravatar.com/avatar/${hash}?d=identicon&s=80`;
    }

    getRoleBadge(role) {
        const badges = {
            owner: '<span class="badge bg-purple-100 text-purple-800">Proprietário</span>',
            admin: '<span class="badge bg-red-100 text-red-800">Administrador</span>',
            editor: '<span class="badge bg-blue-100 text-blue-800">Editor</span>',
            viewer: '<span class="badge bg-green-100 text-green-800">Visualizador</span>'
        };
        return badges[role] || badges.viewer;
    }

    showAlert(message, type = 'info') {
        // Implementar sistema de alertas
        const alert = $(`<div class="alert alert-${type}">${message}</div>`);
        $('#alerts-container').append(alert);
        setTimeout(() => alert.remove(), 5000);
    }

    handleError(error) {
        const message = error.responseJSON?.message || 'Erro desconhecido';
        this.showAlert(message, 'error');
    }
}

// Inicializar quando o documento estiver pronto
$(document).ready(function() {
    window.workspaceAccessManager = new WorkspaceAccessManager(WORKSPACE_ID);
});