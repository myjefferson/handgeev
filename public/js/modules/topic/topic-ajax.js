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
            if (response.success) {
                window.location.reload();
            }
        },
        error: function(xhr) {
            const errorData = xhr.responseJSON;
            
            if (errorData?.error === 'plan_limit_exceeded') {
                // Mostrar modal ou alerta mais amigável
                showLimitExceededModal(errorData);
            } else {
                alert('Erro ao criar tópico: ' + (errorData?.message || 'Erro desconhecido'));
            }
        }
    });
}

function showLimitExceededModal(errorData) {
    // Você pode usar SweetAlert, Modal do Bootstrap, ou criar seu próprio modal
    const message = `
        <div class="p-4">
            <div class="flex items-center mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mr-3"></i>
                <h3 class="text-lg font-semibold text-white">Limite do Plano Atingido</h3>
            </div>
            <p class="text-gray-300 mb-4">${errorData.message}</p>
            <div class="bg-slate-700 rounded-lg p-3 mb-4">
                <p class="text-sm text-gray-400">Seu plano atual:</p>
                <p class="text-white font-medium">${errorData.limits.current_topics} / ${errorData.limits.max_topics} tópicos utilizados</p>
            </div>
            <div class="flex justify-end space-x-3">
                <button onclick="closeLimitModal()" class="px-4 py-2 bg-slate-600 hover:bg-slate-500 text-white rounded-lg transition-colors">
                    Fechar
                </button>
                <button onclick="upgradePlan()" class="px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-900 font-medium rounded-lg transition-colors">
                    Fazer Upgrade
                </button>
            </div>
        </div>
    `;
    
    // Criar modal dinamicamente ou usar biblioteca existente
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Limite do Plano',
            html: message.replace(/<button/g, '<button class="swal-button"'),
            icon: 'warning',
            showConfirmButton: false,
            showCloseButton: true
        });
    } else {
        // Fallback para alert simples
        if (confirm(`${errorData.message}\n\nDeseja fazer upgrade do plano?`)) {
            window.location.href = '/subscription/pricing';
        }
    }
}

function closeLimitModal() {
    if (typeof Swal !== 'undefined') {
        Swal.close();
    }
}

function upgradePlan() {
    window.location.href = '/subscription/pricing';
}


// UPDATE
export function updateTopic(topicId) {
    const url = routesTopic.deleteTopic.replace(':id', topicId);
    
    $.ajax({
        url: url,
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        success: function(response) {
            window.location.reload();
        },
        error: function(xhr) {
            alert('Erro ao atualizar tópico: ' + (xhr.responseJSON?.error || 'Erro desconhecido'));
        }
    });
}

// DELETE
export function deleteTopic(route_create) {    
    $.ajax({
        url: route_create,
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