<div id="rename-topic-modal" tabindex="-1" aria-hidden="true" 
     class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-slate-800 rounded-lg shadow-sm border border-slate-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-slate-600">
                <h3 class="text-xl font-semibold text-white">
                    Renomear Tópico
                </h3>
                <button type="button" 
                        class="text-slate-400 bg-transparent hover:bg-slate-600 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" 
                        data-modal-hide="rename-topic-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Fechar modal</span>
                </button>
            </div>
            
            <!-- Modal body -->
            <form id="renameTopicForm">
                @csrf
                <input type="hidden" id="rename-topic-id" name="topic_id">
                
                <div class="p-4 md:p-5 space-y-4">
                    <div>
                        <label for="rename-topic-title" class="block text-sm font-medium text-gray-300 mb-2">
                            Título do Tópico
                        </label>
                        <input type="text" 
                               id="rename-topic-title" 
                               name="title" 
                               placeholder="Meu Novo Tópico"
                               class="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                               maxlength="100"
                               autocomplete="off"
                               required>
                        <div class="text-xs text-gray-400 mt-1 flex justify-between">
                            <span id="rename-char-count">0/100</span>
                            <span>caracteres</span>
                        </div>
                    </div>
                </div>
                
                <!-- Modal footer -->
                <div class="flex items-center space-x-3 justify-end p-4 md:p-5 border-t border-slate-600 rounded-b">
                    <button type="button" 
                            data-modal-hide="rename-topic-modal" 
                            class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-300 focus:outline-none bg-transparent rounded-lg border border-slate-600 hover:bg-slate-700 hover:text-white focus:z-10 focus:ring-4 focus:ring-slate-700">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts_end')
    <script type="module">
        import { AlertManager } from '/js/modules/alert.js';
        const alertManager = new AlertManager();
    </script>
    <script>
        // Inicializar dropdowns e modais quando o documento estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar todos os dropdowns
            const dropdownButtons = document.querySelectorAll('[data-dropdown-toggle]');
            dropdownButtons.forEach(button => {
                const dropdownId = button.getAttribute('data-dropdown-toggle');
                const dropdown = document.getElementById(dropdownId);
                
                if (dropdown) {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation();
                        dropdown.classList.toggle('hidden');
                    });
                    
                    // Fechar dropdown ao clicar fora
                    document.addEventListener('click', function() {
                        dropdown.classList.add('hidden');
                    });
                    
                    // Prevenir que clicks dentro do dropdown fechem ele
                    dropdown.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }
            });
            
            // Inicializar botões de renomear
            const renameButtons = document.querySelectorAll('.rename-topic-btn');
            renameButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const topicId = this.getAttribute('data-topic-id');
                    const topicTitle = this.getAttribute('data-topic-title');
                    
                    // Preencher o modal com os dados
                    document.getElementById('rename-topic-id').value = topicId;
                    document.getElementById('rename-topic-title').value = topicTitle;
                    
                    // Atualizar contador de caracteres
                    const charCount = document.getElementById('rename-char-count');
                    charCount.textContent = `${topicTitle.length}/100`;
                    
                    // Mostrar o modal
                    const modal = document.getElementById('rename-topic-modal');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                });
            });
            
            // Fechar modal com botões de fechar
            const closeButtons = document.querySelectorAll('[data-modal-hide="rename-topic-modal"]');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    closeRenameModal();
                });
            });
            
            // Fechar modal ao clicar no overlay (fora do modal)
            document.getElementById('rename-topic-modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeRenameModal();
                }
            });
            
            // Contador de caracteres para o input de renomear
            const titleInput = document.getElementById('rename-topic-title');
            if (titleInput) {
                titleInput.addEventListener('input', function() {
                    const charCount = document.getElementById('rename-char-count');
                    charCount.textContent = `${this.value.length}/100`;
                    
                    // Mudar cor se estiver perto do limite
                    if (this.value.length > 90) {
                        charCount.classList.add('text-red-400');
                        charCount.classList.remove('text-gray-400');
                    } else {
                        charCount.classList.remove('text-red-400');
                        charCount.classList.add('text-gray-400');
                    }
                });
            }
            
            // Submit do formulário de renomear
            const renameForm = document.getElementById('renameTopicForm');
            if (renameForm) {
                renameForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    const topicId = document.getElementById('rename-topic-id').value;
                    const newTitle = document.getElementById('rename-topic-title').value.trim();
                    
                    if (!newTitle) {
                        alertManager.show('Por favor, insira um título para o tópico.', 'error');
                        return;
                    }
                    
                    // Desabilitar botão durante a requisição
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalText = submitButton.textContent;
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Renomeando...';
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                    
                    try {
                        // Chamar função de renomear
                        const result = await renameTopic(topicId, newTitle);
                        
                        if (result.success) {
                            // Fechar modal apenas se for sucesso
                            closeRenameModal();
                        }
                        // Se der erro, manter o modal aberto para correção
                        
                    } catch (error) {
                        console.error('Erro no processo de renomeação:', error);
                        alertManager.show('Erro inesperado ao renomear tópico.', 'error');
                    } finally {
                        // Reabilitar botão
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                        submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                });
            }

            // Função para fechar modal
            function closeRenameModal() {
                const modal = document.getElementById('rename-topic-modal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
                
                // Limpar formulário
                document.getElementById('renameTopicForm').reset();
            }

            // Função para renomear tópico
            async function renameTopic(topicId, newTitle) {
                try {
                    const response = await fetch(`/topic/${topicId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            title: newTitle,
                            _method: 'PUT'
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw data;
                    }

                    if (data.success) {
                        console.log(data)
                        $('div[data-topic-id="' + topicId + '"]').find('.topic-title').html(data.topic.title);
                        $('div[data-topic-id="' + topicId + '"]').find('h3').html(data.topic.title);
                        // Atualizar o título na interface
                        updateTopicTitleInUI(topicId, newTitle);
                        
                        // Mostrar mensagem de sucesso
                        alertManager.show(data.message || 'Tópico renomeado com sucesso!', 'success');
                        
                        return { success: true, data: data.data };
                    } else {
                        // Tratar diferentes tipos de erro
                        handleRenameError(data);
                        return { success: false, error: data };
                    }
                } catch (error) {
                    console.error('Erro ao renomear tópico:', error);
                    
                    if (error.errors) {
                        // Erro de validação
                        const errorMessages = Object.values(error.errors).flat().join(', ');
                        alertManager.show('Erro de validação: ' + errorMessages, 'error');
                    } else if (error.message) {
                        alertManager.show(error.message, 'error');
                    } else {
                        alertManager.show('Erro ao renomear tópico. Tente novamente.', 'error');
                    }
                    
                    return { success: false, error: error };
                }
            }

            // Atualizar a interface após renomear
            function updateTopicTitleInUI(topicId, newTitle) {
                // Atualizar no dropdown button (botão de renomear)
                const renameButtons = document.querySelectorAll(`.rename-topic-btn[data-topic-id="${topicId}"]`);
                renameButtons.forEach(button => {
                    button.setAttribute('data-topic-title', newTitle);
                });
                
                // Atualizar no elemento de título principal do tópico
                const topicTitleElement = document.querySelector(`#topic-title-${topicId}`);
                if (topicTitleElement) {
                    topicTitleElement.textContent = newTitle;
                }
                
                // Tentar encontrar e atualizar outros elementos que mostram o título
                const topicElements = document.querySelectorAll(`[data-topic-id="${topicId}"]`);
                topicElements.forEach(element => {
                    if (element.classList.contains('topic-title') || element.textContent === element.getAttribute('data-topic-title')) {
                        element.textContent = newTitle;
                    }
                });
                
                // Atualizar no sidebar ou lista de tópicos
                const sidebarTopic = document.querySelector(`[href*="topic=${topicId}"]`);
                if (sidebarTopic) {
                    sidebarTopic.textContent = newTitle;
                }
            }

            // Tratamento de erros
            function handleRenameError(errorData) {
                switch (errorData.error) {
                    case 'validation_error':
                        alertManager.show('Erro de validação: ' + Object.values(errorData.errors).flat().join(', '), 'error');
                        break;
                    case 'permission_denied':
                        alertManager.show('Você não tem permissão para renomear este tópico.', 'error');
                        break;
                    case 'not_found':
                        alertManager.show('Tópico não encontrado.', 'error');
                        break;
                    default:
                        alertManager.show(errorData.message || 'Erro ao renomear tópico.', 'error');
                }
            }
        });
    </script>
@endpush