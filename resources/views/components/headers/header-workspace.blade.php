
<div class="flex justify-between items-center">
    <div class="flex gap-3 items-center">      
        <div class="flex items-center space-x-2">
            <!-- Dropdown Principal de Exportação -->
            <div class="relative" id="export-dropdown">               
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
                    <span>Geev Studio</span>
                </button>
            @endif
            @if($workspace->type_view_workspace_id == 2)
                <a href="{{ route('workspace.api-rest.show', [
                    'global_key_api' => auth()->user()->global_key_api, 
                    'workspace_key_api' => $workspace->workspace_key_api]) 
                }}" 
                    class="flex items-center px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 focus:ring-4 focus:ring-teal-300 transition-colors teal-glow-hover">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2" width="1.4em" height="1.4em" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M12 9a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3m0 8a5 5 0 0 1-5-5a5 5 0 0 1 5-5a5 5 0 0 1 5 5a5 5 0 0 1-5 5m0-12.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5"/>
                    </svg>
                    <span>Geev API</span>
                </a>
            @endif

            <a href="{{ route('workspace.setting', ['id' => $workspace->id]) }}"
                class="flex items-center px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-cog"></i>
            </a>
        </div>
    </div>
</div>

@push('modals')
    @include('components.modals.modal-json-preview')
@endpush

@push('scripts_end')
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // Dropdown toggle (works without Flowbite JS and supports multiple dropdowns)
        document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
            const menuId = button.getAttribute('data-dropdown-toggle');
            const menu = document.getElementById(menuId);
            if (!menu) return;

            // ensure initial state
            menu.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');

            button.addEventListener('click', (ev) => {
                ev.stopPropagation();
                // close other dropdowns
                document.querySelectorAll('[data-dropdown-toggle]').forEach(b => {
                    const mId = b.getAttribute('data-dropdown-toggle');
                    const mEl = document.getElementById(mId);
                    if (mEl && mEl !== menu) {
                        mEl.classList.add('hidden');
                        b.setAttribute('aria-expanded', 'false');
                    }
                });

                const isOpen = !menu.classList.contains('hidden');
                menu.classList.toggle('hidden');
                button.setAttribute('aria-expanded', String(!isOpen));

                if (!isOpen) {
                    // move focus to first focusable element inside menu for accessibility
                    const focusable = menu.querySelector('button, [href], input, [tabindex]:not([tabindex="-1"])');
                    if (focusable) focusable.focus();
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (event) => {
            document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
                const mid = button.getAttribute('data-dropdown-toggle');
                const menu = document.getElementById(mid);
                if (!menu) return;
                if (!menu.classList.contains('hidden') && !menu.contains(event.target) && event.target !== button) {
                    menu.classList.add('hidden');
                    button.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // Close on ESC
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                document.querySelectorAll('[data-dropdown-toggle]').forEach(button => {
                    const mid = button.getAttribute('data-dropdown-toggle');
                    const menu = document.getElementById(mid);
                    if (!menu) return;
                    if (!menu.classList.contains('hidden')) {
                        menu.classList.add('hidden');
                        button.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });

        // --- Export actions (copy / preview) ---
        const copyJsonButtons = document.querySelectorAll('.export-copy-json');
        copyJsonButtons.forEach(button => {
            button.addEventListener('click', async function() {
                const workspaceId = this.getAttribute('data-workspace-id');
                await copyWorkspaceJsonToClipboard(workspaceId);
                // close parent dropdown (if any)
                const dropdownId = this.closest('[id^="export-dropdown"]')?.querySelector('[data-dropdown-toggle]')?.getAttribute('data-dropdown-toggle');
                if (dropdownId) document.getElementById(dropdownId)?.classList.add('hidden');
            });
        });

        const previewJsonButtons = document.querySelectorAll('.export-preview-json');
        previewJsonButtons.forEach(button => {
            button.addEventListener('click', async function() {
                const workspaceId = this.getAttribute('data-workspace-id');
                await previewWorkspaceJson(workspaceId);
                const dropdownId = this.closest('[id^="export-dropdown"]')?.querySelector('[data-dropdown-toggle]')?.getAttribute('data-dropdown-toggle');
                if (dropdownId) document.getElementById(dropdownId)?.classList.add('hidden');
            });
        });

    });

    // Função para copiar JSON para clipboard
    async function copyWorkspaceJsonToClipboard(workspaceId) {
        const button = document.querySelector(`.export-copy-json[data-workspace-id="${workspaceId}"]`);
        if (!button) return;
        const originalContent = button.innerHTML;
        try {
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-3 text-blue-500"></i><div><div class="font-medium">Copiando...</div><div class="text-xs text-gray-500 dark:text-gray-400">Aguarde</div></div>';
            button.disabled = true;

            const response = await fetch(`/workspace/${workspaceId}/export`);
            if (!response.ok) throw new Error('Erro na resposta do servidor');

            const data = await response.json();
            await navigator.clipboard.writeText(JSON.stringify(data, null, 2));
            alertManager.show('JSON copiado para a área de transferência!', 'success');

        } catch (error) {
            console.error('Erro ao copiar JSON:', error);
            alertManager.show('Erro ao copiar JSON. Tente novamente.', 'error');
        } finally {
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    }

    // Função para visualizar JSON
    async function previewWorkspaceJson(workspaceId) {
        const button = document.querySelector(`.export-preview-json[data-workspace-id="${workspaceId}"]`);
        if (!button) return;
        const originalContent = button.innerHTML;
        try {
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-3 text-green-500"></i><div><div class="font-medium">Carregando...</div><div class="text-xs text-gray-500 dark:text-gray-400">Aguarde</div></div>';
            button.disabled = true;

            const response = await fetch(`/workspace/${workspaceId}/export`);
            if (!response.ok) throw new Error('Erro na resposta do servidor');

            const data = await response.json();
            const jsonPreview = document.getElementById('json-preview-content');
            if (jsonPreview) jsonPreview.textContent = JSON.stringify(data, null, 2);
            const modal = document.getElementById('json-preview-modal');
            if (modal) modal.classList.remove('hidden');

        } catch (error) {
            console.error('Erro ao carregar JSON:', error);
            alertManager.show('Erro ao carregar JSON para visualização', 'error');
        } finally {
            button.innerHTML = originalContent;
            button.disabled = false;
        }
    }

    // Fechar modal de preview
    function closeJsonPreview() {
        const modal = document.getElementById('json-preview-modal');
        if (modal) modal.classList.add('hidden');
    }

    // Copiar JSON do modal
    function copyJsonToClipboard() {
        const jsonContent = document.getElementById('json-preview-content')?.textContent;
        if (!jsonContent) { alertManager.show('Nada para copiar', 'error'); return; }
        navigator.clipboard.writeText(jsonContent).then(() => {
            alertManager.show('JSON copiado para a área de transferência!', 'success');
            closeJsonPreview();
        }).catch(() => {
            alertManager.show('Erro ao copiar JSON', 'error');
        });
    }

    // Fechar modal com ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeJsonPreview();
        }
    });
    </script>
@endpush


