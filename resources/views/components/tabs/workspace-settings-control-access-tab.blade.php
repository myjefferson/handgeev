<div id="tab-access" class="hidden tab-content">
    <div class="grid grid-cols-1 gap-8">
        @if (auth()->user()->isStart() || auth()->user()->isPro() || auth()->user()->isPremium() || auth()->user()->isAdmin())
            <!-- Card de Proteção por Senha -->
            <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">Tipo de Acesso</h2>
                    <button id="save-type-access-control" class="text-teal-600 bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 dark:text-teal-400 dark:hover:bg-teal-900/50 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-save mr-2"></i> Save
                    </button>
                </div>
                
                <!-- Seleção de tipo de acesso -->
                <div class="mb-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Escolha quem pode acessar este workspace e seus dados via API.
                    </p>
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Opção: Somente convidados -->
                        <div>
                            <input type="radio" id="access-private" name="access_type" value="private" class="hidden peer"  @if(!$workspace->is_published) checked @endif>
                            <label for="access-private" class="flex flex-col p-3 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:peer-checked:bg-teal-900/20 dark:peer-checked:border-teal-500 dark:peer-checked:text-teal-300">
                                <span class="font-medium">Privado</span>
                                <span class="text-xs mt-1">Somente para mim</span>
                            </label>
                        </div>
                        
                        <!-- Opção: Público -->
                        <div>
                            <input type="radio" id="access-public" name="access_type" value="public" class="hidden peer" @if($workspace->is_published) checked @endif>
                            <label for="access-public" class="flex flex-col p-3 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:peer-checked:bg-teal-900/20 dark:peer-checked:border-teal-500 dark:peer-checked:text-teal-300">
                                <span class="font-medium">Público</span>
                                <span class="text-xs mt-1">Qualquer pessoa pode visualizar</span>
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Proteção por senha (mostrar apenas quando Público estiver selecionado) -->
                <div id="password-protection-section" class="hidden border-t pt-4 border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-md font-medium text-gray-900 dark:text-white">Proteção por Senha</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Adicione uma senha para controlar o acesso público
                            </p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="checkbox-password-protection" class="sr-only peer" @if($hasPasswordWorkspace) checked @endif>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-teal-600"></div>
                        </label>
                    </div>
                    
                    <!-- Campo de senha (mostrar apenas quando o checkbox estiver ativado) -->
                    <div id="password-field" class="@if($hasPasswordWorkspace) hidden @endif mt-4">
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <label for="workspace-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Senha de Acesso
                                </label>
                                <input 
                                    autocomplete="new_password"
                                    type="password" 
                                    id="workspace-password-input" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" 
                                    placeholder="Digite uma senha segura"
                                    value="{{ $workspace->plain_password }}"
                                />
                            </div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            A senha deve ter no mínimo 8 caracteres. Quem tiver a senha poderá acessar os dados públicos.
                        </p>
                    </div>
                </div>
                
                <!-- Mensagem de status -->
                <div id="password-status" class="hidden mt-3 p-3 rounded-lg text-sm"></div>
            </div>
        

            <!-- Área real para usuários Pro -->
            {{-- <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Controle de Acesso</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Gerencie quem pode acessar e editar este workspace.</p>
                
                <!-- Formulário para adicionar colaborador -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Adicionar Colaborador
                    </label>
                    <div class="flex space-x-2">
                        <input type="email" id="collaborator-email" 
                            class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" 
                            placeholder="E-mail do colaborador" />
                        <select id="collaborator-role" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-32 p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="viewer">Visualizador</option>
                            <option value="editor">Editor</option>
                        </select>
                        <button id="add-collaborator" class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-4 text-center inline-flex items-center dark:bg-teal-500 dark:hover:bg-teal-600 dark:focus:ring-teal-800">
                            <i class="fas fa-plus mr-1"></i> Convidar
                        </button>
                    </div>
                </div>

                <!-- Lista de colaboradores -->
                <div class="mb-4">
                    <div class="flex justify-between mb-3">
                        <h3 class="text-md font-medium text-gray-900 dark:text-white mb-2">Colaboradores</h3>
                        <div class="text-center flex items-center space-x-3">
                            <div class="flex bg-teal-200 text-teal-800 items-center rounded-full px-3 py-1 space-x-1">
                                <div class="text-xs font-bold" id="total-collaborators">0</div>
                                <div class="text-xs">Total</div>
                            </div>
                            <div class="flex bg-green-200 text-green-800 items-center rounded-full px-3 py-1 space-x-1">
                                <div class="text-xs font-bold" id="active-collaborators">0</div>
                                <div class="text-xs">Ativos</div>
                            </div>
                            <div class="flex bg-orange-200 text-yellow-800 items-center rounded-full px-3 py-1 space-x-1">
                                <div class="text-xs font-bold" id="pending-collaborators">0</div>
                                <div class="text-xs">Pendentes</div>
                            </div>
                        </div>
                    </div>
                    <div id="collaborators-list" class="space-y-3 max-h-96 overflow-y-auto">
                        <!-- Os colaboradores serão carregados via JavaScript -->
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                            <p>Carregando colaboradores...</p>
                        </div>
                    </div>
                </div>
                <!-- Adicione esta seção após a lista de colaboradores -->
                @if(auth()->user()->isPro() || auth()->user()->isAdmin())
                    <!-- Card de Solicitações de Edição -->
                    <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mt-8">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Solicitações de Edição Pendentes</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Gerencie as solicitações de usuários que querem editar este workspace.</p>
                        
                        <div id="edit-requests-list" class="space-y-4">
                            <!-- As solicitações serão carregadas via JavaScript -->
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                <p>Carregando solicitações...</p>
                            </div>
                        </div>

                        <!-- Histórico de Solicitações -->
                        <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-md font-medium text-gray-900 dark:text-white mb-4">Histórico de Solicitações</h3>
                            <div id="edit-requests-history" class="space-y-3">
                                <!-- O histórico será carregado via JavaScript -->
                            </div>
                        </div>
                    </div>
                @endif
            </div> --}}

        </div>
    @else
        
        <div class="grid grid-cols-1 gap-8">
            @include('components.upsell.password-protection')
            {{-- @include('components.upsell.collaborators') --}}
        </div> 
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeAccessControl();
    });

    function initializeAccessControl() {
        const saveButton = document.getElementById('save-type-access-control');
        const accessTypeRadios = document.querySelectorAll('input[name="access_type"]');
        const passwordCheckbox = document.getElementById('checkbox-password-protection');
        const passwordField = document.getElementById('password-field');
        const passwordInput = document.getElementById('workspace-password-input');
        const passwordStatus = document.getElementById('password-status');
        
        // Mostrar/ocultar seção de senha baseado no tipo de acesso
        accessTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                togglePasswordSection();
            });
        });
        
        // Mostrar/ocultar campo de senha baseado no checkbox
        if (passwordCheckbox) {
            passwordCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    passwordField.classList.remove('hidden');
                } else {
                    passwordField.classList.add('hidden');
                    passwordInput.value = '';
                }
            });
        }
        
        // Event listener para o botão Save
        if (saveButton) {
            saveButton.addEventListener('click', saveAccessSettings);
        }
        
        // Inicializar estado da seção de senha
        togglePasswordSection();
    }

    function togglePasswordSection() {
        try {
            const publicRadio = document.getElementById('access-public');
            const passwordSection = document.getElementById('password-protection-section');
            
            // Verificação robusta
            if (!publicRadio) {
                console.warn('Elemento access-public não encontrado');
                return;
            }
            
            if (!passwordSection) {
                console.warn('Elemento password-protection-section não encontrado');
                return;
            }
            
            // Manipulação segura das classes
            if (publicRadio.checked) {
                passwordSection.classList.remove('hidden');
            } else {
                passwordSection.classList.add('hidden');
            }
            
        } catch (error) {
            console.error('Erro em togglePasswordSection:', error);
        }
    }

    async function saveAccessSettings() {
        const saveButton = document.getElementById('save-type-access-control');
        const originalText = saveButton.innerHTML;
        
        // Validar antes de enviar
        if (!validateAccessForm()) {
            return;
        }
        
        try {
            // Mostrar loading
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Salvando...';
            
            // Coletar dados do formulário
            const formData = {
                is_published: document.getElementById('access-public').checked,
                password_enabled: document.getElementById('checkbox-password-protection').checked,
                password: document.getElementById('workspace-password-input').value
            };
            
            // Fazer requisição
            const response = await fetch(`{{ route('workspace.update.access-settings', ['id' => $workspace->id]) }}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAccessSuccessNotification(data.message);
                updateUIAfterSave(data.data);
            } else {
                showAccessError(data.error || 'Erro ao salvar configurações');
            }
            
        } catch (error) {
            console.error('Erro:', error);
            showAccessError('Erro de conexão. Tente novamente.');
        } finally {
            // Restaurar botão
            saveButton.disabled = false;
            saveButton.innerHTML = originalText;
        }
    }

    function validateAccessForm() {
        const passwordEnabled = document.getElementById('checkbox-password-protection').checked;
        const passwordInput = document.getElementById('workspace-password-input');
        const passwordStatus = document.getElementById('password-status');
        
        // Limpar status anterior
        passwordStatus.classList.add('hidden');
        passwordStatus.innerHTML = '';
        
        // Validar senha se estiver habilitada
        if (passwordEnabled) {
            const password = passwordInput.value.trim();
            
            if (!password) {
                showAccessError('Por favor, digite uma senha quando a proteção por senha estiver ativada.', 'password-status');
                passwordInput.focus();
                return false;
            }
            
            if (password.length < 8) {
                showAccessError('A senha deve ter pelo menos 8 caracteres.', 'password-status');
                passwordInput.focus();
                return false;
            }
        }
        
        return true;
    }

    function showAccessSuccessNotification(message) {
        // Remover notificações existentes
        const existingNotifications = document.querySelectorAll('.access-notification');
        existingNotifications.forEach(notification => notification.remove());
        
        const notification = document.createElement('div');
        notification.className = 'access-notification fixed top-4 right-4 z-50 p-4 bg-green-500 text-white rounded-lg shadow-lg transform transition-transform duration-300';
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => notification.style.transform = 'translateX(0)', 100);
        
        // Remover após 5 segundos
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }

    function showAccessError(message, targetElementId = null) {
        if (targetElementId) {
            const targetElement = document.getElementById(targetElementId);
            if (targetElement) {
                targetElement.innerHTML = `
                    <div class="flex items-center p-3 text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <span>${message}</span>
                    </div>
                `;
                targetElement.classList.remove('hidden');
            }
        } else {
            // Notificação geral
            const existingNotifications = document.querySelectorAll('.access-notification');
            existingNotifications.forEach(notification => notification.remove());
            
            const notification = document.createElement('div');
            notification.className = 'access-notification fixed top-4 right-4 z-50 p-4 bg-red-500 text-white rounded-lg shadow-lg transform transition-transform duration-300';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => notification.style.transform = 'translateX(0)', 100);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, 5000);
        }
    }

    function updateUIAfterSave(data) {
        // Atualizar interface com os novos dados
        const passwordStatus = document.getElementById('password-status');
        
        if (data.has_password) {
            passwordStatus.innerHTML = `
                <div class="flex items-center p-3 text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-green-900/20 dark:text-green-400 dark:border-green-800">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Proteção por senha ativada com sucesso!</span>
                </div>
            `;
        } else {
            passwordStatus.innerHTML = `
                <div class="flex items-center p-3 text-blue-800 border border-blue-300 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <span>Workspace ${data.access_type === 'public' ? 'público' : 'privado'} configurado com sucesso.</span>
                </div>
            `;
        }
        
        passwordStatus.classList.remove('hidden');
        
        // Esconder campo de senha se não estiver habilitado
        if (!data.has_password) {
            document.getElementById('password-field').classList.add('hidden');
            document.getElementById('workspace-password-input').value = '';
        }
        
        // Atualizar outros elementos da página se necessário
        updateAccessIndicators(data);
    }

    function updateAccessIndicators(data) {
        // Atualizar indicadores visuais em outros lugares da página
        const accessIndicators = document.querySelectorAll('.access-type-indicator');
        accessIndicators.forEach(indicator => {
            if (data.access_type === 'public') {
                indicator.innerHTML = '<i class="fas fa-globe text-green-500 mr-1"></i> Público';
                indicator.className = 'access-type-indicator inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
            } else {
                indicator.innerHTML = '<i class="fas fa-lock text-gray-500 mr-1"></i> Privado';
                indicator.className = 'access-type-indicator inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            }
        });
        
        // Atualizar status de proteção por senha
        const protectionIndicators = document.querySelectorAll('.password-protection-indicator');
        protectionIndicators.forEach(indicator => {
            if (data.has_password) {
                indicator.innerHTML = '<i class="fas fa-shield-alt text-blue-500 mr-1"></i> Protegido por senha';
                indicator.className = 'password-protection-indicator inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300';
            } else {
                indicator.innerHTML = '<i class="fas fa-unlock text-gray-500 mr-1"></i> Sem senha';
                indicator.className = 'password-protection-indicator inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
            }
        });
    }

    // Inicializar quando a aba for ativada (para casos de SPA)
    document.addEventListener('DOMContentLoaded', function() {
        // Observar mudanças de abas se estiver usando sistema de tabs
        const tabButtons = document.querySelectorAll('[data-tab-target]');
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetTab = this.getAttribute('data-tab-target');
                if (targetTab === 'tab-access') {
                    // Re-inicializar quando a aba de acesso for ativada
                    setTimeout(initializeAccessControl, 100);
                }
            });
        });
    });
</script>