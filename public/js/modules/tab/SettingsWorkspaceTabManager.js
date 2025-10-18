// Gerenciamento de Tabs para Settings
class SettingsWorkspaceTabManager {
    constructor() {
        this.tabParam = 'tab';
        this.defaultTab = 'tab-overview';
        this.init();
    }

    init() {
        this.setupTabListeners();
        this.activateTabFromURL();
    }

    setupTabListeners() {
        const tabButtons = document.querySelectorAll('.tab-button[data-tab]');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const targetTab = button.getAttribute('data-tab');
                this.switchTab(targetTab);
                this.updateURL(targetTab);
            });
        });
    }

    switchTab(targetTab) {
        // Esconder todos os conteúdos de tab
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Mostrar tab selecionada
        const targetElement = document.getElementById(targetTab);
        if (targetElement) {
            targetElement.classList.remove('hidden');
        }

        // Atualizar botões ativos
        document.querySelectorAll('.tab-button').forEach(tab => {
            tab.classList.remove('text-gray-700', 'dark:text-gray-300', 'border-teal-500');
            tab.classList.add('text-gray-500', 'dark:text-gray-400');
            
            // Remover indicador ativo
            const border = tab.querySelector('.tab-border');
            if (border) {
                border.remove();
            }
        });

        // Ativar botão atual
        const activeButton = document.querySelector(`[data-tab="${targetTab}"]`);
        if (activeButton) {
            activeButton.classList.remove('text-gray-500', 'dark:text-gray-400');
            activeButton.classList.add('text-gray-700', 'dark:text-gray-300');
            
            // Adicionar indicador de borda
            const border = document.createElement('div');
            border.className = 'tab-border absolute bottom-0 left-0 w-full h-0.5 bg-teal-500';
            activeButton.appendChild(border);
        }
    }

    updateURL(tabId) {
        const url = new URL(window.location);
        
        if (tabId === this.defaultTab) {
            url.searchParams.delete(this.tabParam);
        } else {
            url.searchParams.set(this.tabParam, this.getTabSlug(tabId));
        }

        window.history.replaceState({}, '', url);
    }

    activateTabFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const tabSlug = urlParams.get(this.tabParam);
        
        if (tabSlug) {
            const targetTab = this.getTabIdFromSlug(tabSlug);
            if (targetTab) {
                this.switchTab(targetTab);
                return;
            }
        }

        // Fallback para tab padrão
        this.switchTab(this.defaultTab);
    }

    getTabSlug(tabId) {
        const slugs = {
            'tab-overview': 'overview',
            'tab-security': 'security',
            'tab-access': 'access'
        };
        return slugs[tabId] || tabId;
    }

    getTabIdFromSlug(slug) {
        const mapping = {
            'overview': 'tab-overview',
            'security': 'tab-security',
            'access': 'tab-access'
        };
        return mapping[slug];
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    new SettingsWorkspaceTabManager();
});