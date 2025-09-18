@extends('template.template-dashboard')

@section('content_dashboard')
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
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .tab-button {
            transition: all 0.3s ease;
        }
        .tab-button.active {
            border-bottom: 2px solid #0d9488;
            color: #0d9488;
        }
    </style>

    <!-- Header -->
    <header>
        <div class="max-w-7xl mx-auto px-3 sm:px-5 lg:px-7 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <button onclick="window.history.back()" class="mr-4 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-arrow-left text-gray-600 dark:text-gray-300"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Configurações do Workspace</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $workspace->title }}</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <button class="text-teal-600 bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 dark:text-teal-400 dark:hover:bg-teal-900/50 px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-save mr-2"></i>Salvar Alterações
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Navegação por Abas -->
    <div class="max-w-7xl mx-auto px-3 sm:px-5 lg:px-7 border-b border-gray-200 dark:border-gray-700">
        <div class="flex space-x-8">
            <button class="tab-button py-4 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 active" data-tab="tab-security">
                <i class="fas fa-shield-alt mr-2"></i>Segurança & API
            </button>
            <button class="tab-button py-4 px-1 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300" data-tab="tab-access">
                <i class="fas fa-users mr-2"></i>Controle de Acesso @free @include("components.badges.pro-badge")@endfree
            </button>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <main class="max-w-7xl mx-auto px-3 sm:px-5 lg:px-7 py-8">
        <!-- Aba 1: Segurança & API -->
        <div id="tab-security" class="tab-content active">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Coluna principal -->
                <div class="lg:col-span-2 space-y-8">
                    

                    <!-- Card de Visualização da API -->
                    <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Visualização da API</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Escolha como a API será exibida para os usuários.</p>
                        
                        <div class="grid gap-4 md:grid-cols-2">
                            <!-- Opção: Visualização GUI -->
                            <div>
                                <input type="radio" id="api-gui-mode" name="api_view_mode" value="gui" class="hidden peer" checked />
                                <label for="api-gui-mode" class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-teal-500 peer-checked:border-teal-600 peer-checked:text-teal-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                    <div class="block">
                                        <div class="w-full text-lg font-semibold">Modo GUI</div>
                                        <div class="w-full text-sm">Interface amigável com opção de visualizar JSON</div>
                                    </div>
                                    <i class="fas fa-desktop text-xl"></i>
                                </label>
                            </div>
                            
                            <!-- Opção: Visualização JSON Puro -->
                            @if(auth()->user()->isAdmin() || auth()->user()->isPro())
                                <div>
                                    <input type="radio" id="api-json-mode" name="api_view_mode" value="json" class="hidden peer" />
                                    <label for="api-json-mode" class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-teal-500 peer-checked:border-teal-600 peer-checked:text-teal-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <div class="block">
                                            <div class="w-full text-lg font-semibold">JSON Puro</div>
                                            <div class="w-full text-sm">Apenas o JSON bruto para desenvolvedores</div>
                                        </div>
                                        <i class="fas fa-code text-xl"></i>
                                    </label>
                                </div>
                            @else
                                <a href="{{ route('landing.offers') }}" class="dark:bg-purple-900/20 rounded-lg">
                                    <label class="inline-flex items-center justify-between w-full p-4 text-gray-500 border border-gray-200 rounded-lg cursor-pointer dark:border-gray-700">
                                        <div class="block rounded-full items-center justify-center mr-3 bg-gradient-to-r">
                                            <div class="flex items-center">
                                                <span class="text-lg font-semibold text-white">JSON Bruto</span>
                                                @include("components.badges.pro-badge")
                                            </div>
                                            <div class="w-full text-sm text-purple-300 mt-1"> Apenas o JSON bruto para desenvolvedores </div>
                                        </div>
                                        <i class="fas fa-code text-xl"></i>
                                    </label>
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Card de Hash da API -->
                    <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Hash Workspace API</h2>
                            <button id="generate-hash-button" class="text-teal-600 hover:text-teal-800 dark:text-teal-400 dark:hover:text-teal-300 flex items-center text-sm">
                                <i class="fas fa-sync-alt mr-1"></i> Gerar novo hash
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Use este hash para acessar a API deste workspace.</p>
                        
                        <div class="flex">
                            <input type="text" id="api-hash" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" value="a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6" readonly />
                            <button id="copy-hash-button" class="relative text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-r-lg text-sm px-4 text-center inline-flex items-center dark:bg-teal-500 dark:hover:bg-teal-600 dark:focus:ring-teal-800">
                                <i class="fas fa-copy"></i>
                                <span class="copied-tooltip absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded">Copiado!</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Coluna lateral -->
                <div class="space-y-8">
                    <!-- Card de Ações Rápidas -->
                    <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Ações Rápidas</h2>
                        
                        <div class="space-y-3">
                            <button class="w-full flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span>Exportar Configurações</span>
                                <i class="fas fa-download"></i>
                            </button>
                            
                            <button class="w-full flex items-center justify-between p-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600">
                                <span>Duplicar Workspace</span>
                                <i class="fas fa-copy"></i>
                            </button>
                            
                            <button class="w-full flex items-center justify-between p-3 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30">
                                <span>Excluir Workspace</span>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aba 2: Controle de Acesso -->
        <div id="tab-access" class="tab-content">
            <div class="grid grid-cols-1 gap-8">
                @if (auth()->user()->isPro() || auth()->user()->isAdmin())
                    <!-- Card de Proteção por Senha -->
                    <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Tipo de Acesso</h2>
                        </div>
                        
                        
                        <!-- Seleção de tipo de acesso -->
                        <div class="mb-6">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                Escolha quem pode acessar este workspace e seus dados via API.
                            </p>
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Opção: Somente convidados -->
                                <div>
                                    <input type="radio" id="access-private" name="access_type" value="private" class="hidden peer" checked>
                                    <label for="access-private" class="flex flex-col p-3 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:peer-checked:bg-teal-900/20 dark:peer-checked:border-teal-500 dark:peer-checked:text-teal-300">
                                        <span class="font-medium">Privado</span>
                                        <span class="text-xs mt-1">Apenas colaboradores</span>
                                    </label>
                                </div>
                                
                                <!-- Opção: Público -->
                                <div>
                                    <input type="radio" id="access-public" name="access_type" value="public" class="hidden peer">
                                    <label for="access-public" class="flex flex-col p-3 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:peer-checked:bg-teal-900/20 dark:peer-checked:border-teal-500 dark:peer-checked:text-teal-300">
                                        <span class="font-medium">Público</span>
                                        <span class="text-xs mt-1">Qualquer pessoa pode acessar</span>
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
                                    <input type="checkbox" id="password-protection" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-teal-600"></div>
                                </label>
                            </div>
                            
                            <!-- Campo de senha (mostrar apenas quando o checkbox estiver ativado) -->
                            <div id="password-field" class="hidden mt-4">
                                <div class="flex gap-3">
                                    <div class="flex-1">
                                        <label for="workspace-password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                            Senha de Acesso
                                        </label>
                                        <input 
                                            type="password" 
                                            id="workspace-password" 
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" 
                                            placeholder="Digite uma senha segura" 
                                        />
                                    </div>
                                    <div class="flex items-end">
                                        <button 
                                            type="button" 
                                            id="save-password-btn"
                                            class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-4 py-2.5 h-[42px] dark:bg-teal-500 dark:hover:bg-teal-600 dark:focus:ring-teal-800"
                                        >
                                            Salvar
                                        </button>
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
                    <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
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
                    </div>
                </div>
            @else
                {{-- Upsell --}}
                <div class="grid grid-cols-1 gap-8">
                    @include('components.upsell.password-protection')
                    @include('components.upsell.collaborators')
                </div> 
            @endif
        </div>
    </main>

    <script>
// Versão simplificada - coloque no final do arquivo
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script carregado!');
    
    const accessPrivate = document.getElementById('access-private');
    const accessPublic = document.getElementById('access-public');
    const passwordSection = document.getElementById('password-protection-section');
    
    if (!accessPrivate || !accessPublic || !passwordSection) {
        console.error('Elementos não encontrados!');
        return;
    }
    
    // Função simples para mostrar/ocultar
    function togglePasswordSection(show) {
        if (show) {
            passwordSection.classList.remove('hidden');
        } else {
            passwordSection.classList.add('hidden');
        }
    }
    
    // Event listeners
    accessPrivate.addEventListener('change', function() {
        console.log('Privado selecionado');
        togglePasswordSection(false);
    });
    
    accessPublic.addEventListener('change', function() {
        console.log('Público selecionado');
        togglePasswordSection(true);
    });
    
    // Inicializar estado
    togglePasswordSection(accessPublic.checked);
});
</script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Funcionalidade das abas
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const tabId = button.getAttribute('data-tab');
                    
                    // Remove a classe active de todos os botões e conteúdos
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Adiciona a classe active ao botão e conteúdo clicado
                    button.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Funcionalidade de copiar hash
            const copyButton = document.getElementById('copy-hash-button');
            const apiHash = document.getElementById('api-hash');
            const tooltip = document.querySelector('.copied-tooltip');
            
            copyButton.addEventListener('click', function() {
                apiHash.select();
                document.execCommand('copy');
                
                // Mostrar tooltip de feedback
                tooltip.classList.add('show-tooltip');
                
                setTimeout(function() {
                    tooltip.classList.remove('show-tooltip');
                }, 2000);
            });
            
            // Gerar novo hash
            const generateButton = document.getElementById('generate-hash-button');
            const originalText = generateButton.innerHTML;
            generateButton.addEventListener('click', function() {
                $.ajax({
                    url: "{{ route('workspace.update.generateNewHashApi', ['id' => $workspace->id]) }}",
                    method: "PUT",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function (response) {
                        if (response.success) {
                            // Atualiza os códigos na interface
                            apiHash.value = response.data.workspace_hash_api
                            generateButton.innerHTML = '<i class="fas fa-check mr-1"></i> Gerado!';
                        } else {
                            alert('Erro ao gerar novo código!');
                        }
                    },
                    error: function (xhr) {
                        alert('Ocorreu um erro: ' + xhr.responseText);
                    },
                    complete: function() {
                        // Restaura o botão
                        button.prop('disabled', false).html(originalText);
                    }
                });        
                
                setTimeout(function() {
                    generateButton.innerHTML = originalText;
                }, 2000);
            });
            
            // Adicionar colaborador
            const addCollaboratorBtn = document.getElementById('add-collaborator');
            const collaboratorEmail = document.getElementById('collaborator-email');
            const collaboratorsList = document.getElementById('collaborators-list');
            
            addCollaboratorBtn.addEventListener('click', function() {
                if (collaboratorEmail.value && isValidEmail(collaboratorEmail.value)) {
                    // Criar elemento de colaborador
                    const initial = collaboratorEmail.value.charAt(0).toUpperCase();
                    const randomColor = getRandomColor();
                    
                    const collaboratorItem = document.createElement('div');
                    collaboratorItem.className = 'collaborator-item flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-lg';
                    collaboratorItem.innerHTML = `
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full ${randomColor} flex items-center justify-center text-white text-sm font-bold mr-2">${initial}</div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">${collaboratorEmail.value}</p>
                                <div class="flex space-x-1 mt-1">
                                    <span class="permission-badge bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900 dark:text-blue-200">Visualização</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button type="button" class="edit-permissions text-teal-600 hover:text-teal-800 dark:text-teal-400">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="remove-collaborator text-red-600 hover:text-red-800 dark:text-red-400">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                    
                    // Adicionar à lista
                    collaboratorsList.appendChild(collaboratorItem);
                    
                    // Limpar campo de email
                    collaboratorEmail.value = '';
                    
                    // Adicionar eventos aos botões
                    const removeBtn = collaboratorItem.querySelector('.remove-collaborator');
                    removeBtn.addEventListener('click', function() {
                        collaboratorsList.removeChild(collaboratorItem);
                    });
                    
                    const editBtn = collaboratorItem.querySelector('.edit-permissions');
                    editBtn.addEventListener('click', function() {
                        // Alternar entre permissões de visualização e edição
                        const permissionsDiv = collaboratorItem.querySelector('.flex.space-x-1');
                        const hasEditPermission = permissionsDiv.querySelector('.bg-green-100');
                        
                        if (hasEditPermission) {
                            permissionsDiv.innerHTML = '<span class="permission-badge bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900 dark:text-blue-200">Visualização</span>';
                        } else {
                            permissionsDiv.innerHTML = `
                                <span class="permission-badge bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900 dark:text-blue-200">Visualização</span>
                                <span class="permission-badge bg-green-100 text-green-800 rounded-full dark:bg-green-900 dark:text-green-200">Edição</span>
                            `;
                        }
                    });
                } else {
                    alert('Por favor, insira um e-mail válido.');
                }
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




            //COLLABORATORS
            @if(auth()->user()->isPro() || auth()->user()->isAdmin())
                initializeAccessControl();
            @endif

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
                    url: `{{ route('workspace.collaborators', ['workspaceId' => $workspace->id]) }}`,
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
                            button.prop('disabled', false).html(originalText);
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

@endsection