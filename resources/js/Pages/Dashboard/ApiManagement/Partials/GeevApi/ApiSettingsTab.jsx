// resources/js/Pages/ApiManagement/Partials/ApiSettingsTab.jsx
import React, { useState, useEffect } from 'react';
import { usePage, router, useForm } from '@inertiajs/react';
import Modal from '@/Components/Workspace/ApiManagement/GeevApi/Modals/Modal';
// import { useToast } from '@/Hooks/useToast';

export default function ApiSettingsTab({ workspace }) {
    const { auth } = usePage().props;
    // const { showToast } = useToast();
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const [modalConfig, setModalConfig] = useState({});
    const [domains, setDomains] = useState(workspace.allowed_domains || []);
    const [activeDomains, setActiveDomains] = useState([]);
    const [inactiveDomains, setInactiveDomains] = useState([]);
    const [processing, setProcessing] = useState(false);

    const { data, setData, post, put, processing: formProcessing, errors, reset } = useForm({
        domain: ''
    });

    // Separar domínios ativos e inativos
    useEffect(() => {
        if (domains.length > 0) {
            const active = domains.filter(d => d.is_active);
            const inactive = domains.filter(d => !d.is_active);
            setActiveDomains(active);
            setInactiveDomains(inactive);
        }
    }, [domains]);

    // Calcular estatísticas do plano
    const user = auth.user;
    const plan = user.plan || { name: 'Free', max_domains: 1, api_requests_per_minute: 30, api_requests_per_day: 2000 };
    const activeDomainsCount = activeDomains.length;
    const maxDomains = plan.max_domains || 1;

    const toggleApiStatus = (enabled) => {
        setModalConfig({
            title: enabled ? 'Ativar API' : 'Desativar API',
            message: enabled 
                ? 'Tem certeza que deseja ativar o acesso à API? Isso permitirá requisições externas.'
                : 'Tem certeza que deseja desativar o acesso à API? Isso bloqueará todas as requisições externas.',
            action: () => {
                router.put(route('management.api.access.toggle', workspace.id), {}, {
                    onSuccess: () => {
                        showToast(
                            enabled ? 'API ativada com sucesso!' : 'API desativada com sucesso!',
                            'success'
                        );
                    },
                    onError: (errors) => {
                        showToast('Erro ao alterar status da API', 'error');
                    }
                });
                setShowConfirmModal(false);
            }
        });
        setShowConfirmModal(true);
    };

    const toggleJWTRequirement = () => {
        setProcessing(true);
        router.put(route('workspace.api.jwt-requirement.toggle', workspace.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                showToast('Configuração JWT atualizada!', 'success');
            },
            onError: () => {
                showToast('Erro ao atualizar configuração JWT', 'error');
            },
            onFinish: () => setProcessing(false)
        });
    };

    const toggleHTTPSRequirement = () => {
        setProcessing(true);
        router.put(route('workspace.api.https-requirement.toggle', workspace.id), {}, {
            preserveScroll: true,
            onSuccess: () => {
                showToast('Configuração HTTPS atualizada!', 'success');
            },
            onError: () => {
                showToast('Erro ao atualizar configuração HTTPS', 'error');
            },
            onFinish: () => setProcessing(false)
        });
    };

    const toggleDomainRestriction = () => {
        setProcessing(true);
        router.put(route('workspace.api.domain-restriction.toggle', workspace), {}, {
            preserveScroll: true,
            onSuccess: () => {
                showToast('Restrição de domínio atualizada!', 'success');
            },
            onError: () => {
                showToast('Erro ao atualizar restrição de domínio', 'error');
            },
            onFinish: () => setProcessing(false)
        });
    };

    const addDomain = (e) => {
        e.preventDefault();
        if (!data.domain.trim()) return;

        setProcessing(true);
        router.post(route('workspace.api.domains.add', workspace), {
            domain: data.domain
        }, {
            preserveScroll: true,
            onSuccess: () => {
                showToast('Domínio adicionado com sucesso!', 'success');
                reset();
                // Recarregar a página para atualizar os domínios
                router.reload({ preserveScroll: true });
            },
            onError: () => {
                showToast('Erro ao adicionar domínio', 'error');
            },
            onFinish: () => setProcessing(false)
        });
    };

    const removeDomain = (domainId) => {
        setModalConfig({
            title: 'Remover Domínio',
            message: 'Tem certeza que deseja remover este domínio? O acesso será bloqueado imediatamente.',
            action: () => {
                router.delete(route('workspace.api.domains.remove', workspace), {
                    data: { domain_id: domainId },
                    preserveScroll: true,
                    onSuccess: () => {
                        showToast('Domínio removido com sucesso!', 'success');
                        router.reload({ preserveScroll: true });
                    },
                    onError: () => {
                        showToast('Erro ao remover domínio', 'error');
                    }
                });
                setShowConfirmModal(false);
            }
        });
        setShowConfirmModal(true);
    };

    const activateDomain = (domainId) => {
        setProcessing(true);
        router.put(route('workspace.api.domains.activate', workspace), {
            domain_id: domainId
        }, {
            preserveScroll: true,
            onSuccess: () => {
                showToast('Domínio reativado com sucesso!', 'success');
                router.reload({ preserveScroll: true });
            },
            onError: () => {
                showToast('Erro ao reativar domínio', 'error');
            },
            onFinish: () => setProcessing(false)
        });
    };

    return (
        <div className="p-6 rounded-lg bg-slate-800/50 border border-slate-700">
            <h3 className="text-xl font-semibold text-white mb-6">⚙️ Configurações de Segurança</h3>
            
            {workspace.api_domain_restriction && activeDomainsCount === 0 && (
                <div className="bg-red-500/10 border border-red-500/20 rounded-lg p-4 mb-4">
                    <div className="flex items-center">
                        <svg className="w-5 h-5 text-red-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <p className="text-red-400 font-medium">Atenção: API bloqueada</p>
                            <p className="text-red-300 text-sm mt-1">
                                A restrição por domínio está ativa, mas nenhum domínio foi configurado. 
                                <strong className="ml-1">A API está bloqueando todas as requisições.</strong> 
                                Adicione domínios abaixo para permitir o acesso.
                            </p>
                        </div>
                    </div>
                </div>
            )}
            
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {/* Coluna Principal */}
                <div className="lg:col-span-2">
                    {/* Status da API */}
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
                        <div className="flex items-center justify-between mb-4">
                            <div>
                                <h4 className="text-lg font-semibold text-white">Status do Acesso API</h4>
                                <p className="text-slate-400 text-sm">Controle o acesso à API deste workspace</p>
                            </div>
                            <button 
                                onClick={() => toggleApiStatus(!workspace.api_enabled)}
                                disabled={processing}
                                className={`relative inline-flex items-center h-6 rounded-full w-11 transition-colors disabled:opacity-50 ${
                                    workspace.api_enabled ? 'bg-teal-500' : 'bg-gray-300 dark:bg-gray-600'
                                }`}
                            >
                                <span className={`inline-block w-4 h-4 transform bg-white rounded-full transition ${
                                    workspace.api_enabled ? 'translate-x-6' : 'translate-x-1'
                                }`} />
                            </button>
                        </div>
                        
                        {!workspace.api_enabled && (
                            <div className="bg-yellow-500/10 border border-yellow-500/20 rounded-lg p-3">
                                <p className="text-yellow-400 text-sm flex items-center">
                                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    A API está desativada. Nenhum acesso externo será permitido.
                                </p>
                            </div>
                        )}
                    </div>

                    {/* Autenticação JWT */}
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
                        <div className="flex items-center justify-between mb-4">
                            <div>
                                <h4 className="text-lg font-semibold text-white">Autenticação JWT Obrigatória</h4>
                                <p className="text-slate-400 text-sm">Forçar uso de tokens JWT via rota de autenticação</p>
                            </div>
                            <div className="flex items-center">
                                <span className="mr-3 text-sm font-medium text-white">
                                    {workspace.api_jwt_required ? 'JWT Obrigatório' : 'JWT Obrigatório'}
                                </span>
                                <button 
                                    onClick={toggleJWTRequirement}
                                    disabled={processing}
                                    className={`relative inline-flex items-center h-6 rounded-full w-11 transition-colors disabled:opacity-50 ${
                                        workspace.api_jwt_required ? 'bg-teal-500' : 'bg-gray-600'
                                    }`}
                                >
                                    <span className={`inline-block w-4 h-4 transform bg-white rounded-full transition ${
                                        workspace.api_jwt_required ? 'translate-x-6' : 'translate-x-1'
                                    }`} />
                                </button>
                            </div>
                        </div>

                        {!workspace.api_jwt_required ? (
                            <div className="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3">
                                <p className="text-blue-400 text-sm flex items-center">
                                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Tokens fixos do workspace são aceitos. Ative para exigir autenticação JWT.
                                </p>
                            </div>
                        ) : (
                            <>
                                <div className="bg-green-500/10 border border-green-500/20 rounded-lg p-3 mb-4">
                                    <p className="text-green-400 text-sm flex items-center">
                                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        Autenticação JWT obrigatória. Use a rota <code className="bg-slate-700 px-1 rounded">/api/auth/login/token</code> para obter tokens.
                                    </p>
                                </div>
                                
                                {/* Informações da Rota de Autenticação */}
                                <div className="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4">
                                    <h5 className="text-amber-400 font-medium mb-2">🔐 Rota de Autenticação</h5>
                                    <div className="space-y-2 text-sm">
                                        <div className="flex items-start">
                                            <span className="text-amber-300 font-mono text-xs bg-amber-500/20 px-2 py-1 rounded mr-2">POST</span>
                                            <div>
                                                <code className="text-amber-200">{window.location.origin}/api/auth/login/token</code>
                                                <p className="text-amber-300 mt-1">Obtenha um token JWT válido usando suas credenciais</p>
                                            </div>
                                        </div>
                                        
                                        <div className="mt-3 p-2 bg-slate-700 rounded text-xs">
                                            <p className="text-amber-300 mb-1"><strong>Body da requisição:</strong></p>
                                            <pre className="text-amber-200"><code>{`{
    "email": "seu-email@exemplo.com",
    "password": "sua-senha"
}`}</code></pre>
                                        </div>
                                        
                                        <div className="mt-2 p-2 bg-slate-700 rounded text-xs">
                                            <p className="text-amber-300 mb-1"><strong>Resposta:</strong></p>
                                            <pre className="text-amber-200"><code>{`{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "Bearer",
    "expires_at": "2024-01-01T00:00:00Z"
}`}</code></pre>
                                        </div>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>

                    {/* Controle HTTPS */}
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
                        <div className="flex items-center justify-between mb-4">
                            <div>
                                <h4 className="text-lg font-semibold text-white">Requer Conexão HTTPS</h4>
                                <p className="text-slate-400 text-sm">Forçar uso de conexões seguras (HTTPS)</p>
                            </div>
                            <div className="flex items-center">
                                <span className="mr-3 text-sm font-medium text-white">
                                    {workspace.api_https_required ? 'HTTPS Obrigatório' : 'HTTP Permitido'}
                                </span>
                                <button 
                                    onClick={toggleHTTPSRequirement}
                                    disabled={processing}
                                    className={`relative inline-flex items-center h-6 rounded-full w-11 transition-colors disabled:opacity-50 ${
                                        workspace.api_https_required ? 'bg-teal-500' : 'bg-gray-600'
                                    }`}
                                >
                                    <span className={`inline-block w-4 h-4 transform bg-white rounded-full transition ${
                                        workspace.api_https_required ? 'translate-x-6' : 'translate-x-1'
                                    }`} />
                                </button>
                            </div>
                        </div>

                        {workspace.api_https_required ? (
                            <div className="bg-green-500/10 border border-green-500/20 rounded-lg p-3">
                                <p className="text-green-400 text-sm flex items-center">
                                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    Conexões HTTPS obrigatórias. Requisições HTTP serão bloqueadas.
                                </p>
                            </div>
                        ) : (
                            <div className="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3">
                                <p className="text-blue-400 text-sm flex items-center">
                                    <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Conexões HTTP são permitidas. Ideal para desenvolvimento e testes.
                                </p>
                            </div>
                        )}
                    </div>

                    {/* Controle de Domínios */}
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
                        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                            <div className="flex-1 min-w-0">
                                <h4 className="text-lg font-semibold text-white truncate">Controle de Acesso por Domínio</h4>
                                <p className="text-slate-400 text-sm mt-1">Restringir acesso apenas a domínios específicos</p>
                            </div>
                            <div className="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
                                <span className="text-sm font-medium text-white whitespace-nowrap">
                                    {workspace.api_domain_restriction ? 'Apenas domínios permitidos' : 'Acesso livre'}
                                </span>
                                <button 
                                    onClick={toggleDomainRestriction}
                                    disabled={processing}
                                    className={`relative inline-flex items-center h-6 rounded-full w-11 flex-shrink-0 transition-colors disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 focus:ring-offset-slate-800 ${
                                        workspace.api_domain_restriction ? 'bg-teal-500' : 'bg-gray-600'
                                    }`}
                                >
                                    <span className={`inline-block w-4 h-4 transform bg-white rounded-full transition ${
                                        workspace.api_domain_restriction ? 'translate-x-6' : 'translate-x-1'
                                    }`} />
                                </button>
                            </div>
                        </div>

                        {!workspace.api_domain_restriction ? (
                            <div className="bg-blue-500/10 border border-blue-500/20 rounded-lg p-3">
                                <p className="text-blue-400 text-sm flex items-center">
                                    <svg className="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    A API aceita requisições de qualquer domínio. Ative a restrição para maior segurança.
                                </p>
                            </div>
                        ) : (
                            <div className="bg-green-500/10 border border-green-500/20 rounded-lg p-3">
                                <p className="text-green-400 text-sm flex items-center">
                                    <svg className="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    A API só aceita requisições dos domínios listados abaixo.
                                </p>
                            </div>
                        )}
                    </div>

                    {/* Domínios Permitidos (só mostra quando a restrição está ativa) */}
                    {workspace.api_domain_restriction && (
                        <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                            <div className="flex items-center justify-between mb-4">
                                <div>
                                    <h4 className="text-lg font-semibold text-white">Domínios Permitidos (Origin HTTPS)</h4>
                                    <p className="text-slate-400 text-sm">
                                        {activeDomainsCount} domínios ativos
                                    </p>
                                </div>
                            </div>

                            {/* Formulário para Adicionar Domínio */}
                            <form onSubmit={addDomain} className="mb-6">
                                <div className="flex items-start space-x-3">
                                    <div className="flex-1">
                                        <input 
                                            type="text" 
                                            name="domain"
                                            value={data.domain}
                                            onChange={e => setData('domain', e.target.value)}
                                            placeholder="exemplo.com ou *.exemplo.com" 
                                            className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm"
                                            pattern="^(\*\.)?([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}(:\d+)?$|^localhost(:\d+)?$|^(\*\.)?([a-z0-9]+(-[a-z0-9]+)*\.)?localhost(:\d+)?$"
                                            title="Digite um domínio válido (ex: site.com, *.site.com)"
                                            required
                                            disabled={processing || formProcessing}
                                        />
                                        <p className="text-slate-500 text-xs mt-1">
                                            Use *.exemplo.com para permitir todos os subdomínios
                                        </p>
                                        {errors.domain && (
                                            <span className="text-red-400 text-xs mt-1">{errors.domain}</span>
                                        )}
                                    </div>
                                    <button 
                                        type="submit"
                                        disabled={processing || formProcessing}
                                        className="bg-teal-500 hover:bg-teal-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap disabled:opacity-50"
                                    >
                                        {processing ? 'Processando...' : 'Adicionar Domínio'}
                                    </button>
                                </div>
                            </form>

                            {/* Lista de Domínios Ativos */}
                            <div className="space-y-3">
                                <h5 className="text-sm font-medium text-slate-300">Domínios Ativos</h5>
                                
                                {activeDomains.length > 0 ? (
                                    activeDomains.map(domain => (
                                        <div key={domain.id} className="flex items-center justify-between p-3 bg-slate-700 rounded-lg">
                                            <div className="flex items-center space-x-3">
                                                <svg className="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                <span className="text-white text-sm font-mono">{domain.domain}</span>
                                                <span className="text-slate-500 text-xs">
                                                    {new Date(domain.created_at).toLocaleDateString('pt-BR')}
                                                </span>
                                                {domain.domain.startsWith('*.') && (
                                                    <span className="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">Wildcard</span>
                                                )}
                                            </div>
                                            <button
                                                onClick={() => removeDomain(domain.id)}
                                                disabled={processing}
                                                className="text-red-400 hover:text-red-300 transition-colors disabled:opacity-50"
                                                title="Remover domínio"
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    ))
                                ) : (
                                    <div className="text-center p-4 bg-slate-700 rounded-lg">
                                        <p className="text-slate-400 text-sm">Nenhum domínio configurado</p>
                                        <p className="text-slate-500 text-xs mt-1">
                                            Adicione pelo menos um domínio para permitir acesso à API
                                        </p>
                                    </div>
                                )}
                            </div>

                            {/* Domínios Inativos */}
                            {inactiveDomains.length > 0 && (
                                <div className="mt-6 pt-6 border-t border-slate-700">
                                    <h5 className="text-sm font-medium text-slate-300 mb-3">Domínios Inativos</h5>
                                    <div className="space-y-2">
                                        {inactiveDomains.map(domain => (
                                            <div key={domain.id} className="flex items-center justify-between p-2 bg-slate-700 rounded opacity-60">
                                                <div className="flex items-center space-x-2">
                                                    <svg className="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    <span className="text-slate-300 text-xs font-mono">{domain.domain}</span>
                                                </div>
                                                <button
                                                    onClick={() => activateDomain(domain.id)}
                                                    disabled={processing}
                                                    className="text-teal-400 hover:text-teal-300 text-xs transition-colors disabled:opacity-50"
                                                >
                                                    Reativar
                                                </button>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    )}
                </div>

                {/* Coluna Lateral */}
                <div className="lg:col-span-1">
                    {/* Informações do Plano */}
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
                        <h4 className="text-lg font-semibold text-white mb-4">📊 Limites do Plano</h4>
                        
                        <div className="space-y-4">
                            {/* Domínios */}
                            <div>
                                <div className="flex justify-between text-sm mb-1">
                                    <span className="text-slate-400">Domínios permitidos</span>
                                    <span className="text-white font-medium">{activeDomainsCount} / {maxDomains}</span>
                                </div>
                                <div className="w-full bg-slate-700 rounded-full h-2">
                                    <div 
                                        className="bg-teal-400 h-2 rounded-full" 
                                        style={{ width: `${Math.min(100, (activeDomainsCount / maxDomains) * 100)}%` }}
                                    ></div>
                                </div>
                                {workspace.api_domain_restriction && activeDomainsCount === 0 && (
                                    <p className="text-red-400 text-xs mt-1">⚠️ Adicione domínios para permitir acesso</p>
                                )}
                            </div>

                            {/* Status do Controle */}
                            <div className="pt-3 border-t border-slate-700">
                                <div className="flex items-center justify-between">
                                    <span className="text-slate-400 text-sm">Controle de domínios</span>
                                    <span className={`px-2 py-1 rounded text-xs font-medium ${
                                        workspace.api_domain_restriction 
                                            ? 'bg-green-500/20 text-green-400' 
                                            : 'bg-blue-500/20 text-blue-400'
                                    }`}>
                                        {workspace.api_domain_restriction ? 'Restrito' : 'Livre'}
                                    </span>
                                </div>
                            </div>

                            {/* Requests por Minuto */}
                            <div>
                                <div className="flex justify-between text-sm mb-1">
                                    <span className="text-slate-400">Requests/minuto</span>
                                    <span className="text-white font-medium">{plan.api_requests_per_minute ?? 30}</span>
                                </div>
                                <div className="w-full bg-slate-700 rounded-full h-2">
                                    <div 
                                        className="bg-blue-400 h-2 rounded-full" 
                                        style={{ width: `${Math.min(100, ((plan.api_requests_per_minute ?? 30) / 250) * 100)}%` }}
                                    ></div>
                                </div>
                            </div>

                            {/* Requests por Dia */}
                            <div>
                                <div className="flex justify-between text-sm mb-1">
                                    <span className="text-slate-400">Requests/dia</span>
                                    <span className="text-white font-medium">{plan.api_requests_per_day ?? 2000}</span>
                                </div>
                                <div className="w-full bg-slate-700 rounded-full h-2">
                                    <div 
                                        className="bg-purple-400 h-2 rounded-full" 
                                        style={{ width: `${Math.min(100, ((plan.api_requests_per_day ?? 2000) / 250000) * 100)}%` }}
                                    ></div>
                                </div>
                            </div>

                            {/* Plano Atual */}
                            <div className="pt-3 border-t border-slate-700">
                                <div className="flex items-center justify-between">
                                    <span className="text-slate-400 text-sm">Plano atual</span>
                                    <span className="px-2 py-1 bg-teal-500/20 text-teal-400 rounded text-xs font-medium">
                                        {plan.name}
                                    </span>
                                </div>
                            </div>

                            {plan.name.toLowerCase() === 'free' && activeDomainsCount >= maxDomains && (
                                <div className="mt-4 p-3 bg-amber-500/10 border border-amber-500/20 rounded-lg">
                                    <p className="text-amber-400 text-xs">
                                        Limite de domínios atingido. 
                                        <a href={route('subscription.pricing')} className="underline hover:text-amber-300 ml-1">
                                            Faça upgrade para adicionar mais domínios.
                                        </a>
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Dicas de Segurança */}
                    <div className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                        <h4 className="text-lg font-semibold text-white mb-3">🔒 Dicas de Segurança</h4>
                        <ul className="text-slate-300 text-sm space-y-2">
                            <li className="flex items-start">
                                <svg className="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span><strong>JWT Obrigatório:</strong> Mais seguro, tokens com expiração</span>
                            </li>
                            <li className="flex items-start">
                                <svg className="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span><strong>Modo livre:</strong> Ideal para desenvolvimento e testes</span>
                            </li>
                            <li className="flex items-start">
                                <svg className="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span><strong>Modo restrito:</strong> Obrigatório para produção</span>
                            </li>
                            <li className="flex items-start">
                                <svg className="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Use *.seudominio.com para permitir todos os subdomínios
                            </li>
                            <li className="flex items-start">
                                <svg className="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Revogue acesso de domínios não utilizados
                            </li>
                            <li className="flex items-start">
                                <svg className="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span><strong>HTTPS obrigatório:</strong> Mais seguro.</span>
                            </li>
                            <li className="flex items-start">
                                <svg className="w-4 h-4 text-green-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span><strong>HTTP permitido:</strong> Use apenas para desenvolvimento local</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {/* Modal de Confirmação */}
            <Modal 
                show={showConfirmModal} 
                onClose={() => setShowConfirmModal(false)}
                maxWidth="md"
            >
                <div className="bg-slate-800 rounded-lg shadow border border-slate-700">
                    <div className="p-4 md:p-5 text-center">
                        <i className="fas fa-exclamation-triangle text-amber-400 text-4xl mb-4"></i>
                        <h3 className="text-lg font-normal text-white mb-5">{modalConfig.message}</h3>
                        <div className="flex justify-center space-x-4">
                            <button 
                                onClick={modalConfig.action}
                                disabled={processing}
                                className="py-2 px-4 text-sm font-medium text-white bg-teal-500 rounded-lg hover:bg-teal-600 focus:ring-4 focus:outline-none focus:ring-teal-300 disabled:opacity-50"
                            >
                                {processing ? 'Processando...' : 'Confirmar'}
                            </button>
                            <button 
                                onClick={() => setShowConfirmModal(false)}
                                disabled={processing}
                                className="py-2 px-4 text-sm font-medium text-slate-400 bg-slate-700 rounded-lg hover:bg-slate-600 hover:text-white focus:ring-4 focus:outline-none focus:ring-slate-600 disabled:opacity-50"
                            >
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </Modal>
        </div>
    );
}