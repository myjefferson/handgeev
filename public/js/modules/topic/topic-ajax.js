// CSRF
const csrfToken = $('meta[name="csrf-token"]').attr('content');

// CREATE
export function createTopic(workspace_id, title, route_create) {
    const data = {
        workspace_id,
        title,
        order: $('.topic-tab').length
    };
    
    $.ajax({
        url: route_create,
        method: 'POST',
        data: data,
        headers: { 'X-CSRF-TOKEN': csrfToken },
        success: function(response) {
            // window.location.reload();
        },
        error: function(xhr) {
            alert('Erro ao criar tópico: ' + (xhr.responseJSON?.error || 'Erro desconhecido'));
        }
    });
}


// UPDATE
export function updateTopic(topicId) {
    const url = routesTopic.deleteTopic.replace(':id', topicId);
    
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

// DELETE
export function deleteTopic(topicId) {
    const url = routesTopic.deleteTopic.replace(':id', topicId);
    
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