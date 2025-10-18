<div id="tab-security" class="hidden tab-content">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Coluna principal -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Card de Visualização da API -->
            <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('workspace_settings_security.api_preview') }}</h2>
                    <button id="save-setting-type-view-api" class="text-teal-600 bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 dark:text-teal-400 dark:hover:bg-teal-900/50 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-save mr-2"></i> {{ __('workspace_settings_security.save') }}
                    </button>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('workspace_settings_security.api_preview_description') }}</p>
                
                <div class="grid gap-4 md:grid-cols-2">
                    <!-- Opção: Visualização GUI -->
                    <div>
                        <input type="radio" id="interface-api" name="type_view_workspace" value="1" class="hidden peer" @if($workspace->type_view_workspace_id == 1) checked @endif/>
                        <label for="interface-api" class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-teal-500 peer-checked:border-teal-600 peer-checked:text-teal-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                            <div class="block">
                                <div class="w-full text-lg font-semibold">{{ __('workspace_settings_security.api_interface') }}</div>
                                <div class="w-full text-sm">{{ __('workspace_settings_security.api_interface_description') }}</div>
                            </div>
                            <i class="fas fa-desktop text-xl" title="{{ __('workspace_settings_security.interface_icon') }}"></i>
                        </label>
                    </div>
                    
                    <!-- Opção: Visualização JSON Puro -->
                    @if(auth()->user()->isStart() || auth()->user()->isPro() || auth()->user()->isPremium() || auth()->user()->isAdmin())
                        <div>
                            <input type="radio" id="json-rest-api" name="type_view_workspace" value="2" class="hidden peer" @if($workspace->type_view_workspace_id == 2) checked @endif/>
                            <label for="json-rest-api" class="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-teal-500 peer-checked:border-teal-600 peer-checked:text-teal-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700">
                                <div class="block">
                                    <div class="w-full text-lg font-semibold">{{ __('workspace_settings_security.json_rest_api') }}</div>
                                    <div class="w-full text-sm">{{ __('workspace_settings_security.json_rest_api_description') }}</div>
                                </div>
                                <i class="fas fa-code text-xl" title="{{ __('workspace_settings_security.code_icon') }}"></i>
                            </label>
                        </div>
                    @else
                        <a href="{{ route('subscription.pricing') }}" class="dark:bg-purple-900/20 rounded-lg">
                            <label class="inline-flex items-center justify-between w-full p-4 text-gray-500 border border-gray-200 rounded-lg cursor-pointer dark:border-gray-700">
                                <div class="block rounded-full items-center justify-center mr-3 bg-gradient-to-r">
                                    <div class="flex items-center">
                                        <span class="text-lg font-semibold text-white">{{ __('workspace_settings_security.json_rest_api') }}</span>
                                        @include("components.badges.upgrade-badge")
                                    </div>
                                    <div class="w-full text-sm text-purple-300 mt-1">{{ __('workspace_settings_security.json_rest_api_description') }}</div>
                                </div>
                                <i class="fas fa-code text-xl"></i>
                            </label>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Card de Key da API -->
            <div class="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('workspace_settings_security.workspace_api_key') }}</h2>
                    <button id="generate-hash-button" class="text-teal-600 bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 dark:text-teal-400 dark:hover:bg-teal-900/50 px-4 py-2 rounded-lg text-sm">
                        <i class="fas fa-sync-alt mr-1" title="{{ __('workspace_settings_security.refresh_icon') }}"></i> {{ __('workspace_settings_security.generate_new_hash') }}
                    </button>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('workspace_settings_security.api_key_description') }}</p>
                
                <div class="flex">
                    <input type="text" id="api-key" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" value="{{ $workspace->workspace_key_api }}" readonly />
                    <button id="copy-hash-button" class="relative text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-r-lg text-sm px-4 text-center inline-flex items-center dark:bg-teal-500 dark:hover:bg-teal-600 dark:focus:ring-teal-800">
                        <i class="fas fa-copy" title="{{ __('workspace_settings_security.copy_icon') }}"></i>
                        <span class="copied-tooltip absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded">{{ __('workspace_settings_security.copied') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Coluna lateral -->
        <div class="space-y-8">
            <!-- Card de Ações Rápidas -->
            @include('components.cards.quick-actions-worskpace-card', $workspace)
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Traduções para JavaScript
        const translations = {
            generating: "{{ __('workspace_settings_security.generating') }}",
            generated: "{{ __('workspace_settings_security.generated') }}",
            saving: "{{ __('workspace_settings_security.saving') }}",
            saved: "{{ __('workspace_settings_security.saved') }}",
            error_generating_hash: "{{ __('workspace_settings_security.error_generating_hash') }}",
            connection_error: "{{ __('workspace_settings_security.connection_error') }}",
            duplicating: "{{ __('workspace_settings_security.duplicating') }}",
            duplicate_error: "{{ __('workspace_settings_security.duplicate_error') }}",
            title_exists_error: "{{ __('workspace_settings_security.title_exists_error') }}"
        };

        // Funcionalidade de copiar hash
        const copyButton = document.getElementById('copy-hash-button');
        const apiHash = document.getElementById('api-key');
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
            const $generateButton = $(this);
            
            $generateButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>' + translations.generating);
            
            $.ajax({
                url: "{{ route('workspace.update.generateNewHashApi', ['id' => $workspace->id]) }}",
                method: "PUT",
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function (response) {
                    if (response.success) {
                        // Atualiza os códigos na interface
                        apiHash.value = response.data.workspace_key_api;
                        $generateButton.html('<i class="fas fa-check mr-1"></i>' + translations.generated);
                    } else {
                        alert(translations.error_generating_hash);
                    }
                },
                error: function (xhr) {
                    alert(translations.error_occurred.replace(':error', xhr.responseText));
                },
                complete: function() {
                    setTimeout(function() {
                        $generateButton.prop('disabled', false).html(originalText);
                    }, 2000);
                }
            });        
        });

        // Modal de duplicação
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
                document.getElementById('topicsCount').textContent = `• ${topicsCount} {{ __('workspace_settings_security.topics_count', ['count' => '']) }}`.replace(':count', topicsCount);
                document.getElementById('fieldsCount').textContent = `• ${fieldsCount} {{ __('workspace_settings_security.fields_count', ['count' => '']) }}`.replace(':count', fieldsCount);
                
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
            submitBtn.innerHTML = translations.duplicating;
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
                    errorDiv.textContent = data.message || translations.duplicate_error;
                    errorDiv.classList.remove('hidden');
                    
                    // Focar no campo de erro se for de título
                    if (data.error === 'title_exists') {
                        document.getElementById('new_title').focus();
                    }
                }
            })
            .catch(error => {
                errorDiv.textContent = translations.connection_error;
                errorDiv.classList.remove('hidden');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        $('#save-setting-type-view-api').on('click', function(){
            var $button = $(this);
            var originalHtml = $button.html();
            
            // Desabilita o botão e mostra loading
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>' + translations.saving);

            $.ajax({
                url: "{{ route('workspace.update.viewWorkspace', ['id' => $workspace->id]) }}",
                method: "PUT",
                data: {
                    type_view_workspace: $('[name=type_view_workspace]:checked').val(),
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    if (response.success) {
                        // Atualiza a interface
                        $button.html('<i class="fas fa-check mr-1"></i>' + translations.saved);
                        
                        setTimeout(function() {
                            $button.prop('disabled', false).html(originalHtml);
                        }, 2000);
                    } else {
                        alert('Erro: ' + response.message);
                        $button.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function(xhr) {
                    alert(translations.error_occurred.replace(':error', xhr.responseJSON?.message || xhr.responseText));
                    $button.prop('disabled', false).html(originalHtml);
                },
                complete: function() {
                    // Restaura o botão após timeout
                    setTimeout(function() {
                        $button.prop('disabled', false).html(originalHtml);
                    }, 2000);
                }
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
        @if(auth()->user()->isPro() || auth()->user()->isPremium() || auth()->user()->isAdmin())
            initializeEditRequests();
        @endif
    });
</script>