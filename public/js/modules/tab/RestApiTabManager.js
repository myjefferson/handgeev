// Gerenciamento de Tabs com URL
class RestApiTabManager {
    constructor() {
        this.tabParam = 'tab';
        this.defaultTab = 'statistics-tab';
        this.init();
    }

    init() {
        // Configurar event listeners
        this.setupTabListeners();
        // Ativar tab baseada na URL
        this.activateTabFromURL();
    }

    setupTabListeners() {
        const tabButtons = document.querySelectorAll('[data-tab-target]');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const targetTab = button.getAttribute('data-tab-target');
                this.switchTab(targetTab);
                this.updateURL(targetTab);
            });
        });
    }

    switchTab(targetTab) {
        // Esconder todas as tabs
        document.querySelectorAll('[role="tabpanel"]').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Mostrar tab alvo
        const targetElement = document.getElementById(targetTab);
        if (targetElement) {
            targetElement.classList.remove('hidden');
        }

        // Atualizar botões ativos
        document.querySelectorAll('[role="tab"]').forEach(tab => {
            tab.classList.remove('text-cyan-400', 'border-cyan-400');
            tab.classList.add('text-slate-400', 'border-transparent');
            tab.setAttribute('aria-selected', 'false');
        });

        // Ativar botão atual
        const activeButton = document.querySelector(`[data-tab-target="${targetTab}"]`);
        if (activeButton) {
            activeButton.classList.remove('text-slate-400', 'border-transparent');
            activeButton.classList.add('text-cyan-400', 'border-cyan-400');
            activeButton.setAttribute('aria-selected', 'true');
        }
    }

    updateURL(tabId) {
        const url = new URL(window.location);
        
        if (tabId === this.defaultTab) {
            // Remover parâmetro se for a tab padrão
            url.searchParams.delete(this.tabParam);
        } else {
            // Atualizar parâmetro
            url.searchParams.set(this.tabParam, this.getTabSlug(tabId));
        }

        // Atualizar URL sem recarregar a página
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
            'endpoints-tab': 'endpoints',
            'documentation-tab': 'documentation',
            'permissions-tab': 'permissions',
            'settings-tab': 'settings',
            'statistics-tab': 'statistics',
        };
        return slugs[tabId] || tabId;
    }

    getTabIdFromSlug(slug) {
        const mapping = {
            'endpoints': 'endpoints-tab',
            'documentation': 'documentation-tab',
            'permissions': 'permissions-tab',
            'settings': 'settings-tab',
            'statistics': 'statistics-tab',
        };
        return mapping[slug];
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    new RestApiTabManager();
});