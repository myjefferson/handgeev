import React, { useState, useEffect } from 'react';
import { Head, usePage } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

export default function HelpCenter() {
    const { popularArticles, systemStatus, faqs } = usePage().props;
    
    const [searchTerm, setSearchTerm] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const [showSearchResults, setShowSearchResults] = useState(false);
    const [showTicketModal, setShowTicketModal] = useState(false);
    const [openFaqs, setOpenFaqs] = useState({});
    const [ticketForm, setTicketForm] = useState({
        category: '',
        priority: 'medium',
        subject: '',
        description: '',
        attachments: []
    });

    // Dados de exemplo (em produção viriam do backend via props)
    const articles = [
        ...popularArticles,
        {
            id: 'import-export',
            title: 'Como importar e exportar tópicos',
            excerpt: 'Aprenda a usar as funcionalidades de importação e exportação de tópicos entre workspaces',
            category: 'fields',
            content: 'Conteúdo completo sobre importação/exportação...',
            tags: ['importação', 'exportação', 'tópicos', 'workspace'],
            read_time: 3,
            views: 89
        },
        {
            id: 'api-authentication',
            title: 'Autenticação na API HandGeev',
            excerpt: 'Guia completo sobre como autenticar suas requisições na API',
            category: 'api',
            content: 'Conteúdo sobre autenticação API...',
            tags: ['api', 'autenticação', 'token', 'segurança'],
            read_time: 5,
            views: 156
        }
    ];

    // Busca em tempo real
    useEffect(() => {
        if (searchTerm.length < 2) {
            setShowSearchResults(false);
            return;
        }

        const results = articles.filter(article => 
            article.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
            article.excerpt.toLowerCase().includes(searchTerm.toLowerCase()) ||
            article.tags.some(tag => tag.toLowerCase().includes(searchTerm.toLowerCase()))
        );

        setSearchResults(results);
        setShowSearchResults(true);
    }, [searchTerm]);

    const handleSearch = (e) => {
        if (e.key === 'Enter') {
            const term = e.target.value.toLowerCase().trim();
            if (term.length >= 2) {
                const results = articles.filter(article => 
                    article.title.toLowerCase().includes(term) ||
                    article.excerpt.toLowerCase().includes(term) ||
                    article.tags.some(tag => tag.toLowerCase().includes(term))
                );
                setSearchResults(results);
                setShowSearchResults(true);
            }
        }
    };

    const filterByCategory = (category) => {
        const results = articles.filter(article => article.category === category);
        setSearchTerm('');
        setSearchResults(results);
        setShowSearchResults(true);
    };

    const toggleFaq = (faqId) => {
        setOpenFaqs(prev => ({
            ...prev,
            [faqId]: !prev[faqId]
        }));
    };

    const handleTicketSubmit = (e) => {
        e.preventDefault();
        // Em produção, enviaria via Inertia.post()
        alert('Ticket enviado com sucesso!');
        setShowTicketModal(false);
        setTicketForm({
            category: '',
            priority: 'medium',
            subject: '',
            description: '',
            attachments: []
        });
    };

    const handleFileUpload = (e) => {
        const files = Array.from(e.target.files);
        setTicketForm(prev => ({
            ...prev,
            attachments: [...prev.attachments, ...files]
        }));
    };

    const highlightText = (text, term) => {
        if (!term) return text;
        const regex = new RegExp(`(${term})`, 'gi');
        return text.replace(regex, '<span class="search-highlight">$1</span>');
    };

    const openArticle = (articleId) => {
        // Em produção, redirecionaria para a página do artigo
        alert(`Abrindo artigo: ${articleId}`);
        // router.visit(`/help/articles/${articleId}`);
    };

    return (
        <SiteLayout>
            <Head>
                <title>Central de Ajuda - HandGeev</title>
                <meta name="description" content="Encontre respostas e suporte para suas dúvidas" />
            </Head>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {/* Header */}
                <div className="text-center mb-12">
                    <h1 className="text-4xl font-bold text-white mb-4">Central de Ajuda</h1>
                    <p className="text-xl text-slate-400 max-w-3xl mx-auto">
                        Encontre respostas rápidas para suas dúvidas ou entre em contato com nosso suporte
                    </p>
                </div>

                {/* Barra de Pesquisa */}
                <div className="max-w-3xl mx-auto mb-12">
                    <div className="relative">
                        <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i className="fas fa-search text-slate-400"></i>
                        </div>
                        <input 
                            type="text" 
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            onKeyPress={handleSearch}
                            placeholder="Buscar por palavras-chave, problemas ou funcionalidades..."
                            className="w-full pl-12 pr-4 py-4 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-400 help-search text-lg"
                        />
                        <div className="absolute inset-y-0 right-0 pr-4 flex items-center">
                            <kbd className="px-2 py-1 text-xs font-semibold text-slate-400 bg-slate-700 border border-slate-600 rounded">Enter</kbd>
                        </div>
                    </div>
                </div>

                {/* Categorias Principais */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    {/* Getting Started */}
                    <div 
                        className="category-card bg-slate-800 rounded-xl p-6 cursor-pointer" 
                        onClick={() => filterByCategory('getting-started')}
                    >
                        <div className="flex items-center mb-4">
                            <div className="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mr-4">
                                <i className="fas fa-rocket text-blue-400 text-xl"></i>
                            </div>
                            <h3 className="text-lg font-semibold text-white">Primeiros Passos</h3>
                        </div>
                        <p className="text-slate-400 text-sm mb-4">
                            Guias iniciais para configurar sua conta e começar a usar o HandGeev
                        </p>
                        <div className="flex items-center text-blue-400 text-sm">
                            <span>12 artigos</span>
                            <i className="fas fa-chevron-right ml-2 text-xs"></i>
                        </div>
                    </div>

                    {/* Workspaces & Topics */}
                    <div 
                        className="category-card bg-slate-800 rounded-xl p-6 cursor-pointer" 
                        onClick={() => filterByCategory('workspaces')}
                    >
                        <div className="flex items-center mb-4">
                            <div className="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center mr-4">
                                <i className="fas fa-folder text-green-400 text-xl"></i>
                            </div>
                            <h3 className="text-lg font-semibold text-white">Workspaces & Tópicos</h3>
                        </div>
                        <p className="text-slate-400 text-sm mb-4">
                            Gerencie workspaces, crie tópicos e organize seus dados
                        </p>
                        <div className="flex items-center text-green-400 text-sm">
                            <span>18 artigos</span>
                            <i className="fas fa-chevron-right ml-2 text-xs"></i>
                        </div>
                    </div>

                    {/* Fields & Data */}
                    <div 
                        className="category-card bg-slate-800 rounded-xl p-6 cursor-pointer" 
                        onClick={() => filterByCategory('fields')}
                    >
                        <div className="flex items-center mb-4">
                            <div className="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mr-4">
                                <i className="fas fa-tags text-purple-400 text-xl"></i>
                            </div>
                            <h3 className="text-lg font-semibold text-white">Campos & Dados</h3>
                        </div>
                        <p className="text-slate-400 text-sm mb-4">
                            Tipos de campos, importação/exportação e gestão de dados
                        </p>
                        <div className="flex items-center text-purple-400 text-sm">
                            <span>15 artigos</span>
                            <i className="fas fa-chevron-right ml-2 text-xs"></i>
                        </div>
                    </div>

                    {/* API & Integration */}
                    <div 
                        className="category-card bg-slate-800 rounded-xl p-6 cursor-pointer" 
                        onClick={() => filterByCategory('api')}
                    >
                        <div className="flex items-center mb-4">
                            <div className="w-12 h-12 bg-orange-500/20 rounded-lg flex items-center justify-center mr-4">
                                <i className="fas fa-code text-orange-400 text-xl"></i>
                            </div>
                            <h3 className="text-lg font-semibold text-white">API & Integração</h3>
                        </div>
                        <p className="text-slate-400 text-sm mb-4">
                            Documentação da API, webhooks e integrações
                        </p>
                        <div className="flex items-center text-orange-400 text-sm">
                            <span>22 artigos</span>
                            <i className="fas fa-chevron-right ml-2 text-xs"></i>
                        </div>
                    </div>
                </div>

                {/* Resultados da Busca */}
                {showSearchResults && (
                    <div className="bg-slate-800 rounded-xl border border-slate-700 p-6 mb-8">
                        <div className="flex justify-between items-center mb-6">
                            <h2 className="text-2xl font-bold text-white">Resultados da Busca</h2>
                            <span className="text-slate-400 text-sm">
                                {searchResults.length} resultado(s) encontrado(s)
                            </span>
                        </div>
                        
                        <div className="space-y-3">
                            {searchResults.length === 0 ? (
                                <div className="text-center py-8">
                                    <i className="fas fa-search text-slate-400 text-4xl mb-4"></i>
                                    <p className="text-slate-400">Nenhum resultado encontrado para "{searchTerm}"</p>
                                    <p className="text-slate-500 text-sm mt-2">Tente usar palavras-chave diferentes</p>
                                </div>
                            ) : (
                                searchResults.map(article => (
                                    <div 
                                        key={article.id}
                                        className="article-item bg-slate-750 rounded-lg p-4 cursor-pointer hover:border-l-4 hover:border-l-blue-500 transition-all"
                                        onClick={() => openArticle(article.id)}
                                    >
                                        <div className="flex items-start justify-between mb-2">
                                            <h3 
                                                className="font-semibold text-white text-sm"
                                                dangerouslySetInnerHTML={{ 
                                                    __html: highlightText(article.title, searchTerm) 
                                                }}
                                            />
                                            <span className="text-xs text-slate-400 bg-slate-700 px-2 py-1 rounded-full">
                                                {article.category}
                                            </span>
                                        </div>
                                        <p 
                                            className="text-slate-400 text-xs mb-3"
                                            dangerouslySetInnerHTML={{ 
                                                __html: highlightText(article.excerpt, searchTerm) 
                                            }}
                                        />
                                        <div className="flex items-center text-xs text-slate-500">
                                            <i className="fas fa-tags mr-1"></i>
                                            {article.tags.map(tag => (
                                                <span key={tag} className="bg-slate-700 px-2 py-1 rounded mr-2">
                                                    {tag}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                    </div>
                )}

                {/* Artigos Populares */}
                {!showSearchResults && (
                    <div className="bg-slate-800 rounded-xl border border-slate-700 p-6 mb-8">
                        <div className="flex justify-between items-center mb-6">
                            <h2 className="text-2xl font-bold text-white">Artigos Populares</h2>
                            <button className="text-blue-400 hover:text-blue-300 text-sm font-medium flex items-center">
                                Ver todos
                                <i className="fas fa-chevron-right ml-1 text-xs"></i>
                            </button>
                        </div>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {popularArticles.map(article => (
                                <div 
                                    key={article.id}
                                    className="article-item bg-slate-750 rounded-lg p-4 cursor-pointer"
                                    onClick={() => openArticle(article.id)}
                                >
                                    <div className="flex items-start justify-between mb-2">
                                        <h3 className="font-semibold text-white text-sm">{article.title}</h3>
                                        <span className="text-xs text-slate-400 bg-slate-700 px-2 py-1 rounded-full">
                                            {article.category}
                                        </span>
                                    </div>
                                    <p className="text-slate-400 text-xs mb-3 line-clamp-2">{article.excerpt}</p>
                                    <div className="flex items-center justify-between text-xs text-slate-500">
                                        <span>{article.read_time} min de leitura</span>
                                        <span>{article.views} visualizações</span>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* Canais de Suporte */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    {/* Base de Conhecimento */}
                    <div className="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <div className="flex items-center mb-4">
                            <div className="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3">
                                <i className="fas fa-book text-blue-400"></i>
                            </div>
                            <h3 className="text-lg font-semibold text-white">Base de Conhecimento</h3>
                        </div>
                        <p className="text-slate-400 text-sm mb-4">
                            Explore nossa documentação completa com tutoriais e guias detalhados
                        </p>
                        <button className="w-full bg-blue-600 hover:bg-blue-500 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                            Explorar Documentação
                        </button>
                    </div>

                    {/* Criar Ticket */}
                    <div className="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <div className="flex items-center mb-4">
                            <div className="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center mr-3">
                                <i className="fas fa-ticket-alt text-green-400"></i>
                            </div>
                            <h3 className="text-lg font-semibold text-white">Suporte por Ticket</h3>
                        </div>
                        <p className="text-slate-400 text-sm mb-4">
                            Não encontrou o que procura? Abra um ticket e nossa equipe te ajudará
                        </p>
                        <button 
                            onClick={() => setShowTicketModal(true)}
                            className="w-full bg-green-600 hover:bg-green-500 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium"
                        >
                            Criar Ticket
                        </button>
                    </div>

                    {/* Status do Sistema */}
                    <div className="bg-slate-800 rounded-xl border border-slate-700 p-6">
                        <div className="flex items-center mb-4">
                            <div className={`w-10 h-10 bg-${systemStatus.color}-500/20 rounded-lg flex items-center justify-center mr-3`}>
                                <i className={`fas fa-server text-${systemStatus.color}-400`}></i>
                            </div>
                            <div>
                                <h3 className="text-lg font-semibold text-white">Status do Sistema</h3>
                                <span className={`text-${systemStatus.color}-400 text-sm`}>
                                    {systemStatus.status}
                                </span>
                            </div>
                        </div>
                        <p className="text-slate-400 text-sm mb-4">
                            Todos os sistemas estão operando normalmente
                        </p>
                        <button className="w-full bg-slate-700 hover:bg-slate-600 text-white py-2 px-4 rounded-lg transition-colors text-sm font-medium">
                            Ver Detalhes
                        </button>
                    </div>
                </div>

                {/* FAQ Rápida */}
                <div className="bg-slate-800 rounded-xl border border-slate-700 p-6">
                    <h2 className="text-2xl font-bold text-white mb-6">Perguntas Frequentes</h2>
                    
                    <div className="space-y-4">
                        {faqs.map(faq => (
                            <div key={faq.id} className="faq-item border border-slate-700 rounded-lg overflow-hidden">
                                <button 
                                    className="w-full text-left p-4 bg-slate-750 hover:bg-slate-700 transition-colors flex justify-between items-center"
                                    onClick={() => toggleFaq(faq.id)}
                                >
                                    <span className="font-medium text-white text-sm">{faq.question}</span>
                                    <i className={`fas fa-chevron-${openFaqs[faq.id] ? 'up' : 'down'} text-slate-400 transition-transform`}></i>
                                </button>
                                <div 
                                    className={`p-4 border-t border-slate-700 ${openFaqs[faq.id] ? 'block' : 'hidden'}`}
                                >
                                    <p className="text-slate-400 text-sm">{faq.answer}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Modal de Criar Ticket */}
            {showTicketModal && (
                <div 
                    className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                    onClick={() => setShowTicketModal(false)}
                >
                    <div 
                        className="bg-slate-800 rounded-xl p-6 max-w-2xl w-full mx-4 border border-slate-700"
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="flex justify-between items-center mb-6">
                            <h3 className="text-xl font-semibold text-white">Criar Ticket de Suporte</h3>
                            <button 
                                onClick={() => setShowTicketModal(false)}
                                className="text-slate-400 hover:text-white"
                            >
                                <i className="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <form onSubmit={handleTicketSubmit} className="space-y-4">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-medium text-slate-400 mb-2">Categoria</label>
                                    <select 
                                        name="category" 
                                        required 
                                        value={ticketForm.category}
                                        onChange={(e) => setTicketForm(prev => ({ ...prev, category: e.target.value }))}
                                        className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white"
                                    >
                                        <option value="">Selecione uma categoria</option>
                                        <option value="technical">Problema Técnico</option>
                                        <option value="billing">Faturamento</option>
                                        <option value="feature">Sugestão de Feature</option>
                                        <option value="bug">Reportar Bug</option>
                                        <option value="general">Geral</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-slate-400 mb-2">Prioridade</label>
                                    <select 
                                        name="priority" 
                                        required 
                                        value={ticketForm.priority}
                                        onChange={(e) => setTicketForm(prev => ({ ...prev, priority: e.target.value }))}
                                        className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white"
                                    >
                                        <option value="low">Baixa</option>
                                        <option value="medium">Média</option>
                                        <option value="high">Alta</option>
                                        <option value="urgent">Urgente</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-slate-400 mb-2">Assunto</label>
                                <input 
                                    type="text" 
                                    name="subject" 
                                    required 
                                    value={ticketForm.subject}
                                    onChange={(e) => setTicketForm(prev => ({ ...prev, subject: e.target.value }))}
                                    className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white placeholder-slate-400"
                                    placeholder="Descreva brevemente o assunto"
                                />
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-slate-400 mb-2">Descrição</label>
                                <textarea 
                                    name="description" 
                                    required 
                                    rows="5"
                                    value={ticketForm.description}
                                    onChange={(e) => setTicketForm(prev => ({ ...prev, description: e.target.value }))}
                                    className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white placeholder-slate-400 resize-none"
                                    placeholder="Descreva detalhadamente seu problema ou dúvida..."
                                />
                            </div>
                            
                            <div>
                                <label className="block text-sm font-medium text-slate-400 mb-2">Anexos (Opcional)</label>
                                <div className="border-2 border-dashed border-slate-600 rounded-lg p-4 text-center">
                                    <i className="fas fa-cloud-upload-alt text-slate-400 text-2xl mb-2"></i>
                                    <p className="text-slate-400 text-sm">Arraste arquivos ou clique para selecionar</p>
                                    <input 
                                        type="file" 
                                        multiple 
                                        className="hidden" 
                                        id="fileInput"
                                        onChange={handleFileUpload}
                                    />
                                    <button 
                                        type="button" 
                                        onClick={() => document.getElementById('fileInput').click()}
                                        className="mt-2 bg-slate-700 hover:bg-slate-600 text-white py-2 px-4 rounded-lg text-sm"
                                    >
                                        Selecionar Arquivos
                                    </button>
                                </div>
                            </div>
                            
                            <div className="flex justify-end space-x-3 pt-4 border-t border-slate-700">
                                <button 
                                    type="button" 
                                    onClick={() => setShowTicketModal(false)}
                                    className="px-4 py-2 text-slate-400 hover:text-white transition-colors"
                                >
                                    Cancelar
                                </button>
                                <button 
                                    type="submit" 
                                    className="px-4 py-2 bg-green-600 hover:bg-green-500 text-white rounded-lg transition-colors font-medium"
                                >
                                    <i className="fas fa-paper-plane mr-2"></i>Enviar Ticket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            <style>{`
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
            `}</style>
        </SiteLayout>
    );
}