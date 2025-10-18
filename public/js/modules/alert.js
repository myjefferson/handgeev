export class AlertManager {
    constructor() {
        this.container = this.createAlertContainer();
        this.autoDismissTime = 5000; // 5 segundos
    }

    createAlertContainer() {
        // Verificar se já existe um container
        let container = document.getElementById('alert-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'alert-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-3 w-96';
            document.body.appendChild(container);
        }
        return container;
    }

    show(message, type = 'success') {
        const alert = this.createAlertElement(message, type);
        this.container.appendChild(alert);

        // Auto dismiss
        setTimeout(() => {
            this.dismiss(alert);
        }, this.autoDismissTime);
    }

    createAlertElement(message, type) {
        const types = {
            success: {
                border: 'border-green-500/20',
                text: 'text-green-400',
                icon: 'fa-check-circle'
            },
            error: {
                border: 'border-red-500/20',
                text: 'text-red-400',
                icon: 'fa-exclamation-circle'
            },
            warning: {
                border: 'border-yellow-500/20',
                text: 'text-yellow-400',
                icon: 'fa-exclamation-triangle'
            },
            info: {                
                border: 'border-blue-500/20',
                text: 'text-blue-400',
                icon: 'fa-info-circle'
            }
        };

        const config = types[type] || types.success; 

        const alert = document.createElement('div');
        alert.className = `mb-6 p-4 border ${config.border} rounded-lg bg-slate-900`;
        alert.innerHTML = `
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <i class="fas ${config.icon} ${config.text} mr-3 text-lg"></i>
                    <div class="flex-1">
                        <p class="${config.text} font-medium">${message}</p>
                    </div>
                </div>
                <button class="ml-3 ${config.text} hover:opacity-70 transition-opacity dismiss-btn flex-shrink-0">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Adicionar evento de dismiss
        const dismissBtn = alert.querySelector('.dismiss-btn');
        dismissBtn.addEventListener('click', () => {
            this.dismiss(alert);
        });

        return alert;
    }

    dismiss(alert) {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }

    // Métodos de conveniência
    success(message) {
        this.show(message, 'success');
    }

    error(message) {
        this.show(message, 'error');
    }

    warning(message) {
        this.show(message, 'warning');
    }

    info(message) {
        this.show(message, 'info');
    }
}

// Instância global
window.alertManager = new AlertManager();