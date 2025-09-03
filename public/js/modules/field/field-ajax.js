// CSRF
const csrfToken = $('meta[name="csrf-token"]').attr('content');

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
    const visibility = row.find('.visibility-checkbox').is(':checked') ? 1 : 0;
    const key_name = row.find('.key-input').val();
    const value = row.find('.value-input').val();
    
    const data = {
        workspace_id,
        topic_id,
        visibility,
        key_name,
        value
    };

    $.ajax({
        url: route_create,
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        success: function(response) {
            // if (successCallback) successCallback(response);
            row.attr('data-id', response.data.id);
            showSaveFeedback(row);
        },
        error: function(xhr, status, error) {
            console.error('Erro na requisição:', error);            
            // Mostrar mensagem de erro
            alert('Erro ao salvar: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
        }
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
    
    const data = {
        topic_id,
        visibility,
        key_name,
        value,
        _method: 'PUT'
    };

    $.ajax({
        url: route_update,
        method: 'POST',
        data,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        success: function(response) {
            // if (successCallback) successCallback(response);
            row.attr('data-id', response.data.id);
            showSaveFeedback(row);
        },
        error: function(xhr, status, error) {
            console.error('Erro na requisição:', error);
            // if (errorCallback) errorCallback(xhr, status, error);
            
            // Mostrar mensagem de erro
            alert('Erro ao salvar: ' + (xhr.responseJSON?.message || 'Erro desconhecido'));
        }
    });
}

// Função para DELETE um campo
export function deleteField(row, callback) {
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
    
    const url = routesField.delete.replace(':id', fieldId);
    
    ajaxRequest(url, 'DELETE', {}, function(response) {
        row.remove();
        if (callback) callback(response);
    });
}
