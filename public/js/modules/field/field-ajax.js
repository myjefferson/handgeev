
// CSRF
const csrfToken = $('meta[name="csrf-token"]').attr('content');

// Importar funções de interação
import { updateSaveIndicator, showSaveFeedback, removeFieldCounter,  } from './field-interations.js';

// Função para fazer requisições AJAX
export function ajaxRequest(url, method, data, successCallback, errorCallback) {
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
            const errorMessage = xhr.responseJSON?.message || 'Erro desconhecido';
            alertManager.error('Erro ao salvar: ' + errorMessage);
        }
    });
}

// Função para CREATE um novo campo (ATUALIZADA COM TIPAGEM)
export function createField(row, topic_id, workspace_id, route_create) {
    checkFieldLimit(workspace_id, topic_id).then(response => {
        if (!response.can_add_more && !response.limits.is_unlimited) {
            const limits = response.limits;
            const message = `Limite de ${limits.max} campos por tópico atingido. Este tópico já tem ${limits.current} campos.`;
            
            alertManager.warning(message);
            
            // Remover a linha visualmente
            row.remove();
            return;
        }

        const visibility = row.find('.visibility-checkbox').is(':checked') ? 1 : 0;
        const key_name = row.find('.key-input').val();
        const value = row.find('.value-input, select[name="key_value"]').val();
        const type = row.find('.type-select').val();
        
        // Validação básica
        if (!key_name.trim()) {
            alertManager.warning('Por favor, informe um nome para a chave.');
            row.remove();
            return;
        }

        const data = {
            workspace_id,
            topic_id,
            visibility,
            key_name,
            value,
            type
        };

        updateSaveIndicator(true, false);
        
        $.ajax({
            url: route_create,
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            success: function(response) {
                if (response.success) {
                    row.attr('data-id', response.data.id);
                    row.removeClass('new-field');
                    
                    // Atualizar limites específicos do tópico
                    if (response.limits) {
                        updateTopicLimits(topic_id, response.limits);
                    }
                    
                    updateTopicFieldCount(topic_id);
                    showSaveFeedback(row);
                    updateSaveIndicator(false, true);
                    alertManager.success('Campo criado com sucesso!');
                    
                } else {
                    row.remove();
                    alertManager.error(response.message || 'Erro ao criar campo');
                }
            },
            error: function(xhr) {
                updateSaveIndicator(false, false);
                row.remove();
                
                let errorMessage = 'Erro ao criar campo';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                    
                    if (xhr.status === 403 && xhr.responseJSON.error === 'limit_exceeded') {
                        if (xhr.responseJSON.limits) {
                            updateTopicLimits(topic_id, xhr.responseJSON.limits);
                        }
                        alertManager.warning(xhr.responseJSON.message);
                        return;
                    }
                }
                
                alertManager.error(errorMessage);
            }
        });
    }).catch(error => {
        console.error('Erro ao verificar limite:', error);
        row.remove();
        alertManager.error('Erro ao verificar limite de campos');
    });
}

// Atualizar limites do tópico
function updateTopicLimits(topicId, limits) {
    if (!window.topicsWithLimits) {
        window.topicsWithLimits = {};
    }
    
    window.topicsWithLimits[topicId] = {
        canAddMoreFields: limits.is_unlimited || limits.current < limits.max,
        fieldsLimit: limits.max,
        currentFieldsCount: limits.current,
        remainingFields: limits.remaining,
        isUnlimited: limits.is_unlimited
    };
}

export function updateField(row, topic_id, route_update = {}) {
    const fieldId = row.attr('data-id');
    if (!fieldId) {
        console.error('ID do campo não encontrado');
        return;
    }
    
    const is_visible = row.find('.visibility-checkbox').is(':checked') ? 1 : 0;
    const key_name = row.find('.key-input').val();
    const value = row.find('.value-input, select[name="key_value"]').val();
    const type = row.find('.type-select').val();
    
    // Validação básica
    if (!key_name.trim()) {
        alertManager.warning('Por favor, informe um nome para a chave.');
        return;
    }

    const data = {
        topic_id,
        is_visible,
        key_name,
        value,
        type,
        _method: 'PUT'
    };

    updateSaveIndicator(true, false);
    
    $.ajax({
        url: route_update,
        method: 'POST',
        data,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        success: function(response) {
            if (response.success) {
                showSaveFeedback(row);
                updateSaveIndicator(false, true);                
                alertManager.success('Campo atualizado com sucesso!');
            } else {
                alertManager.error(response.message || 'Erro ao atualizar campo');
            }
        },
        error: function(xhr) {
            updateSaveIndicator(false, false);
            
            let errorMessage = 'Erro ao atualizar campo';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            alertManager.error(errorMessage);
        }
    });
}

// Nova função para atualizar contador de campos no sidebar
function updateTopicFieldCount(topicId) {
    const topicContent = $(`.topic-content[data-topic-id="${topicId}"]`);
    const fieldCount = topicContent.find('tr[data-id]').length;
        
    const sidebarItem = $(`.topic-item[data-topic-id="${topicId}"]`);
    if (sidebarItem.length) {
        const counterText = window.translations?.workspace?.sidebar?.fields_count?.replace(':count', fieldCount) || `${fieldCount}`;
        sidebarItem.find('.text-xs').text(counterText);
    }
}

// Função para DELETE um campo (ATUALIZADA COM ALERTS)
export function deleteField(row, topic_id, route_delete = {}) {
    const fieldId = row.attr('data-id');

    if (!fieldId) {
        row.remove();
        return;
    }

    if (!confirm('Tem certeza que deseja excluir este campo?')) {
        return;
    }
    
    const data = {
        _method: 'DELETE'
    };

    $.ajax({
        url: route_delete,
        method: 'POST',
        data,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        success: function(response) {
            if (response.success) {
                row.remove();
                
                // Atualizar limites quando campo é removido
                if (response.limits) {
                    updateTopicLimits(topic_id, response.limits);
                } else {
                    // Se não veio limits na response, recalcular manualmente
                    setTimeout(() => {
                        checkFieldLimit(workspace_id, topic_id).then(limitResponse => {
                            if (limitResponse.success && limitResponse.limits) {
                                updateTopicLimits(topic_id, limitResponse.limits);
                            }
                        });
                    }, 100);
                }
                
                updateTopicFieldCount(topic_id);
                checkEmptyTable(topic_id);
                alertManager.success(response.message || 'Campo excluído com sucesso!');
            } else {
                alertManager.error(response.message || 'Erro ao excluir campo');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Erro ao excluir campo';
            
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
            }
            alertManager.error(errorMessage);
        }
    });
}

// Função para verificar limite no servidor antes de adicionar
export function checkFieldLimit(workspace_id, topic_id = null) {
    return new Promise((resolve, reject) => {
        const data = { workspace_id };
        if (topic_id) {
            data.topic_id = topic_id;
        }
        
        $.ajax({
            url: '/field/check-limit',
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            success: function(response) {
                if (response.success) {
                    resolve(response);
                } else {
                    reject(response);
                }
            },
            error: function(xhr) {
                reject(xhr);
            }
        });
    });
}

// Nova função para verificar tabela vazia (ATUALIZADA PARA 5 COLUNAS)
function checkEmptyTable(topicId) {
    const topicContent = $(`.topic-content[data-topic-id="${topicId}"]`);
    const tbody = topicContent.find('tbody');
    const existingRows = tbody.find('tr[data-id]');
    
    // Se não há campos, mostrar mensagem de vazio
    if (existingRows.length === 0) {
        const emptyRow = `
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-2xl mb-2"></i>
                    <p>Nenhum campo cadastrado neste tópico</p>
                </td>
            </tr>
        `;
        
        // Manter apenas a linha de adicionar/limite
        const addRow = tbody.find('.add-field-trigger, .limit-reached-row');
        tbody.empty().append(emptyRow);
        if (addRow.length) {
            tbody.append(addRow);
        }
    }
}

// Nova função para obter dados do campo (útil para outras operações)
export function getFieldData(row) {
    return {
        id: row.attr('data-id'),
        key_name: row.find('.key-input').val(),
        value: row.find('.value-input, select[name="key_value"]').val(),
        type: row.find('.type-select').val(),
        is_visible: row.find('.visibility-checkbox').is(':checked') ? 1 : 0,
        topic_id: row.attr('data-topic-id')
    };
}

// Nova função para restaurar tipo de campo após erro
export function restoreFieldType(row, originalType) {
    const typeSelect = row.find('.type-select');
    typeSelect.val(originalType);
    
    // Disparar evento change para atualizar o input
    typeSelect.trigger('change');
}