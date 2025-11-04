// use copy-button from button
// use copy-input from input

export class ClipboardManager {
    constructor() {
        this.autoResetTime = 2000; // 2 segundos
        this.init();
    }

    init() {
        // Delegação de evento para todos os botões de copiar
        $(document).on('click', '.copy-button', (e) => {
            this.copyToClipboard(e.currentTarget);
        });
    }

    copyToClipboard(buttonElement) {
        // Encontra o input mais próximo com a classe copy-input
        const $input = $(buttonElement).closest('.flex').find('.copy-input');
        
        if ($input.length === 0) {
            console.error('Input com classe copy-input não encontrado');
            return;
        }

        const textToCopy = $input.val();
        
        // Usa a API moderna do clipboard
        navigator.clipboard.writeText(textToCopy)
            .then(() => {
                // Feedback visual de sucesso
                this.showCopyFeedback(buttonElement, true);
            })
            .catch(err => {
                console.error('Falha ao copiar: ', err);
                // Fallback para método antigo
                this.fallbackCopyText(textToCopy, buttonElement);
            });
    }

    // Método fallback para navegadores mais antigos
    fallbackCopyText(text, buttonElement) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                this.showCopyFeedback(buttonElement, true);
            } else {
                this.showCopyFeedback(buttonElement, false);
            }
        } catch (err) {
            console.error('Fallback copy failed: ', err);
            this.showCopyFeedback(buttonElement, false);
        } finally {
            document.body.removeChild(textArea);
        }
    }

    // Função para mostrar feedback visual
    showCopyFeedback(buttonElement, success) {
        const $button = $(buttonElement);
        const originalHtml = $button.html();
        
        if (success) {
            // Altera para ícone de check e texto "Copiado"
            $button.html('<i class="fas fa-check mr-1"></i><span class="hidden md:inline">Copiado!</span>');
            
            // Atualiza o tooltip
            this.updateTooltip($button, 'Copiado!');
        } else {
            // Feedback de erro
            $button.html('<i class="fas fa-times mr-1"></i><span class="hidden md:inline">Erro</span>');
            
            // Atualiza o tooltip
            this.updateTooltip($button, 'Erro ao copiar');
        }
        
        // Restaura o estado original após o tempo definido
        setTimeout(() => {
            this.resetButtonState($button, originalHtml);
        }, this.autoResetTime);
    }

    // Atualiza o texto do tooltip
    updateTooltip($button, text) {
        const $tooltip = $button.closest('.flex').find('[id^="tooltip-"]');
        if ($tooltip.length) {
            const originalText = $tooltip.data('original-text') || $tooltip.text();
            $tooltip.data('original-text', originalText);
            $tooltip.text(text);
        }
    }

    // Restaura o estado original do botão
    resetButtonState($button, originalHtml) {
        $button.html(originalHtml);
        
        // Restaura o tooltip
        const $tooltip = $button.closest('.flex').find('[id^="tooltip-"]');
        if ($tooltip.length && $tooltip.data('original-text')) {
            $tooltip.text($tooltip.data('original-text'));
        }
    }

    // Método para copiar texto diretamente (sem botão)
    copyText(text) {
        return navigator.clipboard.writeText(text)
            .then(() => {
                return true;
            })
            .catch(err => {
                console.error('Falha ao copiar: ', err);
                return this.fallbackCopyTextDirectly(text);
            });
    }

    // Fallback para cópia direta
    fallbackCopyTextDirectly(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand('copy');
            document.body.removeChild(textArea);
            return successful;
        } catch (err) {
            console.error('Fallback copy failed: ', err);
            document.body.removeChild(textArea);
            return false;
        }
    }
}