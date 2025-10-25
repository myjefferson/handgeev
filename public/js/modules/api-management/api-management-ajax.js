import { AlertManager } from '../alert.js';
const alertManager = new AlertManager();

// CSRF
const csrfToken = $('meta[name="csrf-token"]').attr('content');

// Toggle Status da API
export function toggleApiStatus(route, enable) {
    const modal = document.getElementById('confirmModal');
    const message = document.getElementById('modalMessage');
    const confirmBtn = document.getElementById('confirmAction');
    const cancelBtn = document.getElementById('cancelAction');
    
    message.textContent = enable 
        ? 'Tem certeza que deseja ativar esta API? Ela ficará publicamente acessível.'
        : 'Tem certeza que deseja desativar esta API? Ela não estará mais acessível.';
    
    // Mostrar modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    confirmBtn.onclick = function() {
        // Usar o workspaceId passado como parâmetro
        fetch(route, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alertManager.show(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                alertManager.show(data.message || 'Erro ao alterar status da API', 'error');
            }
        })
        .catch((error) => {
            alertManager.show('Erro ao alterar status da API', 'error');
        })
        .finally(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    };

    // Configurar cancelamento
    cancelBtn.onclick = function() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };
}
window.toggleApiStatus = toggleApiStatus;