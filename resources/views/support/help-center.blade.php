@extends('template.template-legal')

@section('title', 'Central de Ajuda - HandGeev')
@section('description', 'Encontre respostas e suporte para suas dúvidas')

@push('style')
    <style>
        .help-search:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(45, 212, 191, 0.3);
        }
        
        .category-card {
            transition: all 0.3s ease;
            border: 1px solid #334155;
        }
        
        .category-card:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
        }
        
        .article-item {
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }
        
        .article-item:hover {
            border-left-color: #3b82f6;
            background: #1e293b;
        }
        
        .search-highlight {
            background-color: rgba(34, 197, 94, 0.2);
            padding: 0.1rem 0.2rem;
            border-radius: 0.25rem;
        }
    </style>
@endpush

@section('content_legal')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-white mb-4">Central de Ajuda</h1>
            <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                Encontre respostas rápidas para suas dúvidas ou entre em contato com nosso suporte
            </p>
        </div>

        <!-- Barra de Pesquisa -->
        <div class="max-w-3xl mx-auto mb-12">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400"></i>
                </div>
                <input type="text" 
                    id="helpSearch" 
                    placeholder="Buscar por palavras-chave, problemas ou funcionalidades..."
                    class="w-full pl-12 pr-4 py-4 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-400 help-search text-lg">
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <kbd class="px-2 py-1 text-xs font-semibold text-slate-400 bg-slate-700 border border-slate-600 rounded">Enter</kbd>
                </div>
            </div>
        </div>

        <!-- Categorias Principais -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <!-- Getting Started -->
            <div class="category-card bg-slate-800 rounded-xl p-6 cursor-pointer" onclick="filterByCategory('getting-started')">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-rocket text-blue-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Primeiros Passos</h3>
                </div>
                <p class="text-slate-400 text-sm mb-4">
                    Guias iniciais para configurar sua conta e começar a usar o HandGeev
                </p>
                <div class="flex items-center text-blue-400 text-sm">
                    <span>12 artigos</span>
                    <i class="fas fa-chevron-right ml-2 text-xs"></i>
                </div>
            </div>

            <!-- Workspaces & Topics -->
            <div class="category-card bg-slate-800 rounded-xl p-6 cursor-pointer" onclick="filterByCategory('workspaces')">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-folder text-green-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Workspaces & Tópicos</h3>
                </div>
                <p class="text-slate-400 text-sm mb-4">
                    Gerencie workspaces, crie tópicos e organize seus dados
                </p>
                <div class="flex items-center text-green-400 text-sm">
                    <span>18 artigos</span>
                    <i class="fas fa-chevron-right ml-2 text-xs"></i>
                </div>
            </div>

            <!-- Fields & Data -->
            <div class="category-card bg-slate-800 rounded-xl p-6 cursor-pointer" onclick="filterByCategory('fields')">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-tags text-purple-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Campos & Dados</h3>
                </div>
                <p class="text-slate-400 text-sm mb-4">
                    Tipos de campos, importação/exportação e gestão de dados
                </p>
                <div class="flex items-center text-purple-400 text-sm">
                    <span>15 artigos</span>
                    <i class="fas fa-chevron-right ml-2 text-xs"></i>
                </div>
            </div>

            <!-- API & Integration -->
            <div class="category-card bg-slate-800 rounded-xl p-6 cursor-pointer" onclick="filterByCategory('api')">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-code text-orange-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">API & Integração</h3>
                </div>
                <p class="text-slate-400 text-sm mb-4">
                    Documentação da API, webhooks e integrações
                </p>
                <div class="flex items-center text-orange-400 text-sm">
                    <span>22 artigos</span>
                    <i class="fas fa-chevron-right ml-2 text-xs"></i>
                </div>
            </div>
        </div>

        <!-- Artigos Populares -->
        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Artigos Populares</h2>
                <button class="text-blue-400 hover:text-blue-300 text-sm font-medium flex items-center">
                    Ver todos
                    <i class="fas fa-chevron-right ml-1 text-xs"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($popularArticles as $article)
                <div class="article-item bg-slate-750 rounded-lg p-4 cursor-pointer" onclick="openArticle('{{ $article['id'] }}')">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="font-semibold text-white text-sm">{{ $article['title'] }}</h3>
                        <span class="text-xs text-slate-400 bg-slate-700 px-2 py-1 rounded-full">
                            {{ $article['category'] }}
                        </span>
                    </div>
                    <p class="text-slate-400 text-xs mb-3 line-clamp-2">{{ $article['excerpt'] }}</p>
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>{{ $article['read_time'] }} min de leitura</span>
                        <span>{{ $article['views'] }} visualizações</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Resultados da Busca -->
        <div id="searchResults" class="hidden bg-slate-800 rounded-xl border border-slate-700 p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">Resultados da Busca</h2>
                <span id="resultsCount" class="text-slate-400 text-sm"></span>
            </div>
            
            <div id="resultsList" class="space-y-3">
                <!-- Resultados serão carregados aqui via JavaScript -->
            </div>
        </div>

        <!-- Canais de Suporte -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Base de Conhecimento -->
            <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-book text-blue-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Base de Conhecimento</h3>
                </div>
                <p class="text-slate-400 text-sm mb-4">
                    Explore nossa documentação completa com tutoriais e guias detalhados
                </p>
                <button class="w-full bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                    Explorar Documentação
                </button>
            </div>

            <!-- Criar Ticket -->
            <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-ticket-alt text-green-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">Suporte por Ticket</h3>
                </div>
                <p class="text-slate-400 text-sm mb-4">
                    Não encontrou o que procura? Abra um ticket e nossa equipe te ajudará
                </p>
                <button onclick="openTicketModal()" 
                        class="w-full bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                    Criar Ticket
                </button>
            </div>

            <!-- Status do Sistema -->
            <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-{{ $systemStatus['color'] }}-500/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-server text-{{ $systemStatus['color'] }}-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white">Status do Sistema</h3>
                        <span class="text-{{ $systemStatus['color'] }}-400 text-sm">{{ $systemStatus['status'] }}</span>
                    </div>
                </div>
                <p class="text-slate-400 text-sm mb-4">
                    Todos os sistemas estão operando normalmente
                </p>
                <button class="w-full bg-slate-700 hover:bg-slate-600 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                    Ver Detalhes
                </button>
            </div>
        </div>

        <!-- FAQ Rápida -->
        <div class="bg-slate-800 rounded-xl border border-slate-700 p-6">
            <h2 class="text-2xl font-bold text-white mb-6">Perguntas Frequentes</h2>
            
            <div class="space-y-4">
                @foreach($faqs as $faq)
                <div class="faq-item border border-slate-700 rounded-lg overflow-hidden">
                    <button class="w-full text-left p-4 bg-slate-750 hover:bg-slate-700 transition-colors flex justify-between items-center"
                            onclick="toggleFAQ('faq-{{ $faq['id'] }}')">
                        <span class="font-medium text-white text-sm">{{ $faq['question'] }}</span>
                        <i class="fas fa-chevron-down text-slate-400 transition-transform"></i>
                    </button>
                    <div id="faq-{{ $faq['id'] }}" class="hidden p-4 border-t border-slate-700">
                        <p class="text-slate-400 text-sm">{{ $faq['answer'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal de Criar Ticket -->
    <div id="ticketModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-slate-800 rounded-xl p-6 max-w-2xl w-full mx-4 border border-slate-700">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-white">Criar Ticket de Suporte</h3>
                <button onclick="closeTicketModal()" class="text-slate-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="ticketForm" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Categoria</label>
                        <select name="category" required class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white">
                            <option value="">Selecione uma categoria</option>
                            <option value="technical">Problema Técnico</option>
                            <option value="billing">Faturamento</option>
                            <option value="feature">Sugestão de Feature</option>
                            <option value="bug">Reportar Bug</option>
                            <option value="general">Geral</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-2">Prioridade</label>
                        <select name="priority" required class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white">
                            <option value="low">Baixa</option>
                            <option value="medium" selected>Média</option>
                            <option value="high">Alta</option>
                            <option value="urgent">Urgente</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Assunto</label>
                    <input type="text" name="subject" required 
                        class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white placeholder-slate-400"
                        placeholder="Descreva brevemente o assunto">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Descrição</label>
                    <textarea name="description" required rows="5"
                            class="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white placeholder-slate-400 resize-none"
                            placeholder="Descreva detalhadamente seu problema ou dúvida..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-400 mb-2">Anexos (Opcional)</label>
                    <div class="border-2 border-dashed border-slate-600 rounded-lg p-4 text-center">
                        <i class="fas fa-cloud-upload-alt text-slate-400 text-2xl mb-2"></i>
                        <p class="text-slate-400 text-sm">Arraste arquivos ou clique para selecionar</p>
                        <input type="file" name="attachments[]" multiple class="hidden" id="fileInput">
                        <button type="button" onclick="document.getElementById('fileInput').click()" 
                                class="mt-2 bg-slate-700 hover:bg-slate-600 text-white py-2 px-4 rounded-lg text-sm">
                            Selecionar Arquivos
                        </button>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-700">
                    <button type="button" onclick="closeTicketModal()" 
                            class="px-4 py-2 text-slate-400 hover:text-white transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg transition-colors font-medium">
                        <i class="fas fa-paper-plane mr-2"></i>Enviar Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts_end')
    <script>
        // Dados de exemplo (em produção viriam do backend)
        const articles = [
            // Artigos populares
            @foreach($popularArticles as $article)
            {
                id: '{{ $article['id'] }}',
                title: '{{ $article['title'] }}',
                excerpt: '{{ $article['excerpt'] }}',
                category: '{{ $article['category'] }}',
                content: '{{ $article['content'] }}',
                tags: ['{{ implode("','", $article['tags']) }}']
            },
            @endforeach
            
            // Mais artigos...
            {
                id: 'import-export',
                title: 'Como importar e exportar tópicos',
                excerpt: 'Aprenda a usar as funcionalidades de importação e exportação de tópicos entre workspaces',
                category: 'fields',
                content: 'Conteúdo completo sobre importação/exportação...',
                tags: ['importação', 'exportação', 'tópicos', 'workspace']
            },
            {
                id: 'api-authentication',
                title: 'Autenticação na API HandGeev',
                excerpt: 'Guia completo sobre como autenticar suas requisições na API',
                category: 'api',
                content: 'Conteúdo sobre autenticação API...',
                tags: ['api', 'autenticação', 'token', 'segurança']
            }
        ];

        // Busca em tempo real
        document.getElementById('helpSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            
            if (searchTerm.length < 2) {
                document.getElementById('searchResults').classList.add('hidden');
                return;
            }
            
            const results = articles.filter(article => 
                article.title.toLowerCase().includes(searchTerm) ||
                article.excerpt.toLowerCase().includes(searchTerm) ||
                article.tags.some(tag => tag.toLowerCase().includes(searchTerm))
            );
            
            displaySearchResults(results, searchTerm);
        });

        // Enter para buscar
        document.getElementById('helpSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = e.target.value.toLowerCase().trim();
                if (searchTerm.length >= 2) {
                    const results = articles.filter(article => 
                        article.title.toLowerCase().includes(searchTerm) ||
                        article.excerpt.toLowerCase().includes(searchTerm) ||
                        article.tags.some(tag => tag.toLowerCase().includes(searchTerm))
                    );
                    displaySearchResults(results, searchTerm);
                }
            }
        });

        function displaySearchResults(results, searchTerm) {
            const resultsContainer = document.getElementById('resultsList');
            const resultsCount = document.getElementById('resultsCount');
            
            resultsCount.textContent = `${results.length} resultado(s) encontrado(s)`;
            
            if (results.length === 0) {
                resultsContainer.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-search text-slate-400 text-4xl mb-4"></i>
                        <p class="text-slate-400">Nenhum resultado encontrado para "${searchTerm}"</p>
                        <p class="text-slate-500 text-sm mt-2">Tente usar palavras-chave diferentes</p>
                    </div>
                `;
            } else {
                resultsContainer.innerHTML = results.map(article => `
                    <div class="article-item bg-slate-750 rounded-lg p-4 cursor-pointer hover:border-l-4 hover:border-l-blue-500 transition-all"
                        onclick="openArticle('${article.id}')">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-white text-sm">
                                ${highlightText(article.title, searchTerm)}
                            </h3>
                            <span class="text-xs text-slate-400 bg-slate-700 px-2 py-1 rounded-full">
                                ${article.category}
                            </span>
                        </div>
                        <p class="text-slate-400 text-xs mb-3">
                            ${highlightText(article.excerpt, searchTerm)}
                        </p>
                        <div class="flex items-center text-xs text-slate-500">
                            <i class="fas fa-tags mr-1"></i>
                            ${article.tags.map(tag => `<span class="bg-slate-700 px-2 py-1 rounded mr-2">${tag}</span>`).join('')}
                        </div>
                    </div>
                `).join('');
            }
            
            document.getElementById('searchResults').classList.remove('hidden');
        }

        function highlightText(text, searchTerm) {
            if (!searchTerm) return text;
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            return text.replace(regex, '<span class="search-highlight">$1</span>');
        }

        function filterByCategory(category) {
            const results = articles.filter(article => article.category === category);
            document.getElementById('helpSearch').value = '';
            displaySearchResults(results, '');
        }

        function toggleFAQ(id) {
            const faq = document.getElementById(id);
            const icon = faq.previousElementSibling.querySelector('i');
            
            faq.classList.toggle('hidden');
            icon.classList.toggle('fa-chevron-down');
            icon.classList.toggle('fa-chevron-up');
        }

        function openTicketModal() {
            document.getElementById('ticketModal').classList.remove('hidden');
        }

        function closeTicketModal() {
            document.getElementById('ticketModal').classList.add('hidden');
        }

        function openArticle(articleId) {
            // Em produção, redirecionaria para a página do artigo
            alert(`Abrindo artigo: ${articleId}`);
            // window.location.href = `/help/articles/${articleId}`;
        }

        // Fechar modal ao clicar fora
        document.getElementById('ticketModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTicketModal();
            }
        });

        // Envio do formulário de ticket
        document.getElementById('ticketForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Em produção, enviaria via AJAX
            alert('Ticket enviado com sucesso!');
            closeTicketModal();
        });
    </script>
@endpush