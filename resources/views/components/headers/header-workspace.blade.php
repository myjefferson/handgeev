
<div class="flex justify-between items-center">
    <h3 class="title-header text-2xl font-semibold">{{ $title }}</h3>
    <div class="flex gap-3 items-center">      
        <div class="flex items-center space-x-2">
            <!-- Dropdown Principal de Exportação -->
            <div class="relative" id="export-dropdown">
                <!-- Botão Principal do Dropdown -->
                <button id="export-dropdown-button" 
                        data-dropdown-toggle="export-dropdown-menu"
                        class="flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 focus:ring-4 focus:ring-teal-300 transition-colors teal-glow-hover"
                        type="button">
                        <i class="fas fa-file-export mr-2"></i>
                        Exportar
                        <i class="fas fa-chevron-down ml-2 text-sm"></i>
                </button>

                <!-- Menu Dropdown -->
                @include('components.dropdown.export-workspace-dropdown', ['workspace' => $workspace])
            </div>

            @if($workspace->type_view_workspace_id == 1)
                <button 
                    data-modal-target="modalShareApiInterface" 
                    data-modal-toggle="modalShareApiInterface" 
                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 focus:ring-4 focus:ring-teal-300 transition-colors teal-glow-hover">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2" width="1.4em" height="1.4em" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/>
                    </svg>
                    <span>Interface API</span>
                </button>
            @endif
            @if($workspace->type_view_workspace_id == 2)
                <a href="{{ route('workspace.api-rest', $workspace->id) }}" 
                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 focus:ring-4 focus:ring-teal-300 transition-colors teal-glow-hover">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2" width="1.4em" height="1.4em" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/>
                    </svg>
                    <span>REST API</span>
                </a>
            @endif

            <a href="{{ route('workspace.setting', ['id' => $workspace->id]) }}"
                class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-cog"></i>
            </a>
        </div>

        @include('components.modals.modal-json-preview')

        <!-- Toast Notification -->
        <div id="export-toast" class="fixed top-4 right-4 z-50 hidden">
            <div class="flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
                <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg">
                    <i id="toast-icon" class="fas fa-check text-green-500"></i>
                </div>
                <div class="ms-3 text-sm font-normal" id="toast-message">Operação realizada com sucesso!</div>
                <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" onclick="hideToast()">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
    // Inicialização do Flowbite Dropdown
    document.addEventListener('DOMContentLoaded', function() {

        // Copiar JSON para clipboard
        const copyJsonButtons = document.querySelectorAll('.export-copy-json');
        copyJsonButtons.forEach(button => {
            button.addEventListener('click', function() {
                const workspaceId = this.getAttribute('data-workspace-id');
                copyWorkspaceJsonToClipboard(workspaceId);
                
                // Fechar dropdown após clicar
                const dropdown = document.getElementById('export-dropdown-menu');
                dropdown.classList.add('hidden');
            });
        });

        // Visualizar JSON
        const previewJsonButtons = document.querySelectorAll('.export-preview-json');
        previewJsonButtons.forEach(button => {
            button.addEventListener('click', function() {
                const workspaceId = this.getAttribute('data-workspace-id');
                previewWorkspaceJson(workspaceId);
                
                // Fechar dropdown após clicar
                const dropdown = document.getElementById('export-dropdown-menu');
                dropdown.classList.add('hidden');
            });
        });

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('export-dropdown');
            const dropdownMenu = document.getElementById('export-dropdown-menu');
            const dropdownButton = document.getElementById('export-dropdown-button');
            
            if (!dropdown.contains(event.target) && !dropdownMenu.classList.contains('hidden')) {
                dropdownMenu.classList.add('hidden');
            }
        });
    });

    // Função para copiar JSON para clipboard
    async function copyWorkspaceJsonToClipboard(workspaceId) {
        const button = document.querySelector(`.export-copy-json[data-workspace-id="${workspaceId}"]`);
        const originalContent = button.innerHTML;
        
        try {
            // Mostrar loading
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-3 text-blue-500"></i><div><div class="font-medium">Copiando...</div><div class="text-xs text-gray-500 dark:text-gray-400">Aguarde</div></div>';
            button.disabled = true;

            const response = await fetch(`/workspace/${workspaceId}/export`);
            if (!response.ok) throw new Error('Erro na resposta do servidor');
            
            const data = await response.json();
            await navigator.clipboard.writeText(JSON.stringify(data, null, 2));
            
            showToast('JSON copiado para a área de transferência!', 'success');
            
        } catch (error) {
            console.error('Erro ao copiar JSON:', error);
            showToast('Erro ao copiar JSON. Tente novamente.', 'error');
        } finally {
            // Restaurar botão
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    }

    // Função para visualizar JSON
    async function previewWorkspaceJson(workspaceId) {
        const button = document.querySelector(`.export-preview-json[data-workspace-id="${workspaceId}"]`);
        const originalContent = button.innerHTML;
        
        try {
            // Mostrar loading
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-3 text-green-500"></i><div><div class="font-medium">Carregando...</div><div class="text-xs text-gray-500 dark:text-gray-400">Aguarde</div></div>';
            button.disabled = true;

            const response = await fetch(`/workspace/${workspaceId}/export`);
            if (!response.ok) throw new Error('Erro na resposta do servidor');
            
            const data = await response.json();
            
            const jsonPreview = document.getElementById('json-preview-content');
            jsonPreview.textContent = JSON.stringify(data, null, 2);
            
            const modal = document.getElementById('json-preview-modal');
            modal.classList.remove('hidden');
            
        } catch (error) {
            console.error('Erro ao carregar JSON:', error);
            showToast('Erro ao carregar JSON para visualização', 'error');
        } finally {
            // Restaurar botão
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    }

    // Fechar modal de preview
    function closeJsonPreview() {
        const modal = document.getElementById('json-preview-modal');
        modal.classList.add('hidden');
    }

    // Copiar JSON do modal
    function copyJsonToClipboard() {
        const jsonContent = document.getElementById('json-preview-content').textContent;
        navigator.clipboard.writeText(jsonContent).then(() => {
            showToast('JSON copiado para a área de transferência!', 'success');
            closeJsonPreview();
        }).catch(() => {
            showToast('Erro ao copiar JSON', 'error');
        });
    }

    // Sistema de Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('export-toast');
        const toastIcon = document.getElementById('toast-icon');
        const toastMessage = document.getElementById('toast-message');
        
        // Configurar ícone e cores baseadas no tipo
        if (type === 'success') {
            toastIcon.className = 'fas fa-check text-green-500';
        } else {
            toastIcon.className = 'fas fa-exclamation-triangle text-red-500';
        }
        
        toastMessage.textContent = message;
        toast.classList.remove('hidden');
        
        // Auto-esconder após 4 segundos
        setTimeout(() => {
            hideToast();
        }, 4000);
    }

    function hideToast() {
        const toast = document.getElementById('export-toast');
        toast.classList.add('hidden');
    }

    // Fechar modal com ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeJsonPreview();
        }
    });
    </script>
@endpush


