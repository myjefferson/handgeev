// CSRF
const csrfToken = $('meta[name="csrf-token"]').attr('content');

// Importar funções de interação
import { updateSaveIndicator, showSaveFeedback, removeFieldCounter, refreshFieldsUI } from './field-interations.js';

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
            alert('Erro ao salvar: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
        }
    });
}

// Função para CREATE um novo campo
export function createField(row, topic_id, workspace_id, route_create) {
    checkFieldLimit(workspace_id).then(response => {
        if (!response.can_add_more) {
            alert('Limite de campos atingido. Faça upgrade do seu plano.');
            removeFieldCounter(); // Reverte o contador visual
            return;
        }

        const visibility = row.find('.visibility-checkbox').is(':checked') ? 1 : 0;
        const key_name = row.find('.key-input').val();
        const value = row.find('.value-input').val();
        
        // Validação básica
        if (!key_name.trim()) {
            alert('Por favor, informe um nome para a chave.');
            return;
        }
        
        const data = {
            workspace_id,
            topic_id,
            visibility,
            key_name,
            value
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
                    showSaveFeedback(row);
                    updateSaveIndicator(false, true);
                    
                    // Campo criado com sucesso, não precisa atualizar contador aqui
                    // pois já foi incrementado quando adicionou a linha visualmente
                } else {
                    // Se falhou no servidor, reverter o contador
                    removeFieldCounter();
                    alert(response.message || 'Erro ao criar campo');
                }
            },
            error: function(xhr) {
                updateSaveIndicator(false, false);
                // Reverter contador se falhar
                removeFieldCounter();
                
                let errorMessage = 'Erro ao criar campo';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                    
                    // Verificar se é erro de limite
                    if (xhr.status === 403 && xhr.responseJSON.error === 'limit_exceeded') {
                        // Forçar atualização da UI para mostrar botão de upgrade
                        refreshFieldsUI();
                    }
                }
                
                alert(errorMessage);
            }
        });
    }).catch(error => {
        console.error('Erro ao verificar limite:', error);
        // Continua mesmo com erro na verificação?
    });
}

// Função para UPDATE um campo existente
export function updateField(row, topic_id, route_update = {}) {
    const fieldId = row.attr('data-id');
    if (!fieldId) {
        console.error('ID do campo não encontrado');
        return;
    }
    
    const visibility = row.find('.visibility-checkbox').is(':checked') ? 1 : 0;
    const key_name = row.find('.key-input').val();
    const value = row.find('.value-input').val();
    
    // Validação básica
    if (!key_name.trim()) {
        alert('Por favor, informe um nome para a chave.');
        return;
    }
    
    const data = {
        topic_id,
        visibility,
        key_name,
        value,
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
            } else {
                alert(response.message || 'Erro ao atualizar campo');
            }
        },
        error: function(xhr) {
            updateSaveIndicator(false, false);
            
            let errorMessage = 'Erro ao atualizar campo';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            alert(errorMessage);
        }
    });
}

// Função para DELETE um campo
export function deleteField(row, topic_id, route_delete = {}) {
    const fieldId = row.attr('data-id');

    if (!fieldId) {
        // Se não tem ID, é uma linha nova não salva ainda - apenas remove visualmente
        row.remove();
        // Remove do contador (já que foi adicionado visualmente)
        removeFieldCounter();
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
                // Remove a linha visualmente
                row.remove();
                // Atualiza contador (diminui 1)
                removeFieldCounter();
                
                // Feedback opcional
                // showSaveFeedback(row); // Não faz sentido para delete
            } else {
                alert(response.message || 'Erro ao excluir campo');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Erro ao excluir campo';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            alert(errorMessage);
        }
    });
}

// Função para verificar limite no servidor antes de adicionar (opcional)
export function checkFieldLimit(workspace_id) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '/field/check-limit',
            method: 'POST',
            data: { workspace_id },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            success: function(response) {
                if (response.success) {
                    // Atualizar variáveis globais
                    if (typeof window.currentFieldsCount !== 'undefined') {
                        window.currentFieldsCount = response.limits.current;
                        window.canAddMoreFields = response.can_add_more;
                    }
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