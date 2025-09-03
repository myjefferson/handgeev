$(document).ready(function() {
        let autoSaveTimeout;
        let hasUnsavedChanges = false;
        const workspaceId = {{ $workspace->id }};
        const workspaceType = {{ $workspace->type_workspace_id }};
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        // URLs para as operações CRUD
        const apiUrls = {
            createField: "{{ route('field.store') }}",
            updateField: "{{ route('field.update', ['field' => ':id']) }}",
            deleteField: "{{ route('field.destroy', ['field' => ':id']) }}",
            
            createTopic: "{{ route('topic.store') }}",
            deleteTopic: "{{ route('topic.destroy', ['topic' => ':id']) }}"
        };

        // Navegação entre tabs de tópicos
        $('.topic-tab').on('click', function() {
            const topicId = $(this).data('topic-id');
            
            // Atualizar tabs ativas
            $('.topic-tab').removeClass('border-blue-600 text-blue-600').addClass('border-transparent');
            $(this).addClass('border-blue-600 text-blue-600').removeClass('border-transparent');
            
            // Mostrar conteúdo do tópico selecionado
            $('.topic-content').addClass('hidden');
            $(`.topic-content[data-topic-id="${topicId}"]`).removeClass('hidden');
        });

        // Adicionar novo tópico
        $('#addTopicBtn').on('click', function() {
            const topicName = prompt('Digite o nome do novo tópico:');
            if (topicName && topicName.trim() !== '') {
                createTopic(topicName.trim());
            }
        });

        // Deletar tópico
        $('.delete-topic-btn').on('click', function() {
            const topicId = $(this).data('topic-id');
            const topicTitle = $(this).closest('.topic-content').find('h3').text();
            
            if (confirm(`Tem certeza que deseja excluir o tópico "${topicTitle}"? Todos os campos serão removidos.`)) {
                deleteTopic(topicId);
            }
        });


        // Adicionar novo campo
        $('.add-field-trigger').on('click', function() {
            const topicId = $(this).data('topic-id');
            addNewField(topicId);
        });

        // Função para criar novo tópico
        function createTopic(name) {
            const data = {
                workspace_id: workspaceId,
                title: name,
                order: $('.topic-tab').length
            };
            
            $.ajax({
                url: apiUrls.createTopic,
                method: 'POST',
                data: data,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr) {
                    alert('Erro ao criar tópico: ' + (xhr.responseJSON?.error || 'Erro desconhecido'));
                }
            });
        }

// Função para deletar tópico
        function deleteTopic(topicId) {
            const url = apiUrls.deleteTopic.replace(':id', topicId);
            
            $.ajax({
                url: url,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr) {
                    alert('Erro ao excluir tópico: ' + (xhr.responseJSON?.error || 'Erro desconhecido'));
                }
            });
        }

        // Função para adicionar novo campo
        function addNewField(topicId) {
            const newRow = `
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200" data-topic-id="${topicId}">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <input type="checkbox" class="visibility-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" class="key-input w-full px-2 py-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" class="value-input w-full px-2 py-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    </td>
                    <td class="px-6 py-4 flex space-x-2">
                        <button type="button" class="save-row text-green-600 hover:text-green-800 dark:hover:text-green-400" title="Salvar">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                        <button type="button" class="remove-row text-red-600 hover:text-red-800 dark:hover:text-red-400" title="Remover">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;

            // Insere antes do trigger
            $(`.add-field-trigger[data-topic-id="${topicId}"]`).before(newRow);
        }

        
        // Função para atualizar o indicador de autosave
        function updateSaveIndicator(saving, saved) {
            const savingIcon = $('#savingIcon');
            const savedIcon = $('#savedIcon');
            const statusText = $('#saveStatusText');
            
            if (saving) {
                savingIcon.removeClass('hidden');
                savedIcon.addClass('hidden');
                statusText.text('Salvando...');
            } else if (saved) {
                savingIcon.addClass('hidden');
                savedIcon.removeClass('hidden');
                statusText.text('Todas as alterações salvas');
                
                // Esconder o ícone de salvo após 3 segundos
                setTimeout(() => {
                    if (!hasUnsavedChanges) {
                        savedIcon.addClass('hidden');
                    }
                }, 3000);
            }
        }
        
        // Função para fazer requisições AJAX
        function ajaxRequest(url, method, data, successCallback, errorCallback) {
            $.ajax({
                url: url,
                method: method,
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                success: function(response) {
                    if (successCallback) successCallback(response);
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', error);
                    if (errorCallback) errorCallback(xhr, status, error);
                    
                    // Mostrar mensagem de erro
                    alert('Erro ao salvar: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
                }
            });
        }
        
        // Função para CREATE um novo campo
        function createField(row, callback) {
            const visibility = row.find('.visibility-checkbox').is(':checked') ? 1 : 0;
            const key = row.find('.key-input').val();
            const value = row.find('.value-input').val();
            
            const data = {
                workspace_id: workspaceId,
                visibility: visibility,
                key: key,
                value: value
            };
            
            ajaxRequest(apiUrls.create, 'POST', data, function(response) {
                // Adiciona o ID ao atributo data-id da linha
                row.attr('data-id', response.data.id);
                
                // Feedback visual
                showSaveFeedback(row);
                
                if (callback) callback(response);
            });
        }
        
        // Função para UPDATE um campo existente
        function updateField(row, callback) {
            const fieldId = row.attr('data-id');
            if (!fieldId) {
                console.error('ID do campo não encontrado');
                return;
            }
            
            const visibility = row.find('.visibility-checkbox').is(':checked') ? 1 : 0;
            const key = row.find('.key-input').val();
            const value = row.find('.value-input').val();
            
            const data = {
                workspace_id: workspaceId,
                visibility: visibility,
                key: key,
                value: value,
                _method: 'PUT'
            };
            
            const url = apiUrls.update.replace(':id', fieldId);
            
            ajaxRequest(url, 'POST', data, function(response) {
                // Feedback visual
                showSaveFeedback(row);
                
                if (callback) callback(response);
            });
        }
        
        // Função para DELETE um campo
        function deleteField(row, callback) {
            const fieldId = row.attr('data-id');
            if (!fieldId) {
                // Se não tem ID, é uma linha nova não salva ainda
                row.remove();
                if (callback) callback();
                return;
            }
            
            if (!confirm('Tem certeza que deseja excluir este campo?')) {
                return;
            }
            
            const url = apiUrls.delete.replace(':id', fieldId);
            
            ajaxRequest(url, 'DELETE', {}, function(response) {
                row.remove();
                if (callback) callback(response);
            });
        }

        // Função para feedback visual de salvamento
        function showSaveFeedback(row) {
            row.addClass('bg-green-50 dark:bg-green-900/20');
            setTimeout(() => {
                row.removeClass('bg-green-50 dark:bg-green-900/20');
            }, 1000);
        }
        
        // Função para salvar uma linha (cria ou atualiza)
        function saveRow(row) {
            updateSaveIndicator(true, false);
            const fieldId = row.attr('data-id');
            
            if (fieldId && fieldId !== '') {
                // Campo existente - atualizar
                updateField(row, function() {
                    updateSaveIndicator(false, true);
                    hasUnsavedChanges = false;
                });
            } else {
                // Novo campo - criar
                createField(row, function() {
                    updateSaveIndicator(false, true);
                    hasUnsavedChanges = false;
                });
            }
        }
        
        // Função para adicionar nova linha
        function addNewRow() {
            const newRow = `
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <input type="checkbox" class="visibility-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" class="key-input w-full px-2 py-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" class="value-input w-full px-2 py-1 text-gray-900 bg-white border border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-blue-500 focus:border-blue-500">
                    </td>
                    <td class="px-6 py-4 flex space-x-2">
                        <button type="button" class="save-row text-green-600 hover:text-green-800 dark:hover:text-green-400" title="Salvar">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                        <button type="button" class="remove-row text-red-600 hover:text-red-800 dark:hover:text-red-400" title="Remover">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </td>
                </tr>
            `;

            // Insere a nova linha antes da linha de trigger
            $('#addRowTrigger').before(newRow);

            // Foca no primeiro input da nova linha
            $('#keyValueTable tr').eq(-2).find('.key-input').first().focus();
            
            // Marcar que há alterações não salvas
            hasUnsavedChanges = true;
            $('#saveStatusText').text('Alterações não salvas');
        }

        // DEBUG: Verificar se os data-ids estão sendo capturados corretamente
        console.log('Campos existentes:');
        $('#keyValueTable tr[data-id]').each(function() {
            console.log('ID:', $(this).attr('data-id'), 'Element:', this);
        });
        
        // Função para agendar autosave
        function scheduleAutoSave() {
            // Limpar timeout anterior se existir
            if (autoSaveTimeout) {
                clearTimeout(autoSaveTimeout);
            }
            
            // Agendar novo salvamento
            autoSaveTimeout = setTimeout(() => {
                // Salva todas as linhas modificadas
                updateSaveIndicator(true, false);
                
                let savedCount = 0;
                let totalToSave = 0;
                
                $('#keyValueTable tbody tr').not('#addRowTrigger').each(function() {
                    const row = $(this);
                    // Verifica se a linha foi modificada (simplificado)
                    totalToSave++;
                    
                    saveRow(row, function() {
                        savedCount++;
                        if (savedCount === totalToSave) {
                            updateSaveIndicator(false, true);
                            hasUnsavedChanges = false;
                        }
                    });
                });
                
                if (totalToSave === 0) {
                    updateSaveIndicator(false, true);
                }
            }, 2000); // 2 segundos após a última alteração
        }

        // Evento para adicionar nova linha ao clicar na área
        $('#addRowTrigger').on('click', function() {
            addNewRow();
        });

        // Evento para salvar linha individual
        $(document).on('click', '.save-row', function() {
            const row = $(this).closest('tr');
            const fieldId = row.attr('data-id');
            console.log('Salvando linha com ID:', fieldId);
            
            saveRow(row);
        });

        // Evento para remover linha (usando delegation para linhas dinâmicas)
        $(document).on('click', '.remove-row', function() {
            const row = $(this).closest('tr');
            deleteField(row, function() {
                hasUnsavedChanges = true;
                $('#saveStatusText').text('Alterações não salvas');
            });
        });
        
        // Evento para detectar mudanças nos inputs (autosave)
        $(document).on('input', '#keyValueTable tbody tr:not(#addRowTrigger) input', function() {
            hasUnsavedChanges = true;
            $('#saveStatusText').text('Alterações não salvas');
            scheduleAutoSave();
        });
        
        // Evento para mudança no checkbox
        $(document).on('change', '.visibility-checkbox', function() {
            hasUnsavedChanges = true;
            $('#saveStatusText').text('Alterações não salvas');
            scheduleAutoSave();
        });
    });