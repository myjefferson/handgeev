// resources/js/Components/Modals/DomainManagementModal.jsx
import React, { useState } from 'react';
import { useForm, usePage } from '@inertiajs/react';
import Modal from '@/Components/Workspace/ApiManagement/GeevApi/Modals/Modal';

export default function DomainManagementModal({ 
    show = false, 
    onClose = () => {}, 
    workspace,
    domains = [],
    onDomainAdded = () => {},
    onDomainRemoved = () => {}
}) {
    const { auth, rateLimitData, appVersion } = usePage().props;
    const [newDomain, setNewDomain] = useState('');
    const { data, setData, post, processing, errors, reset } = useForm({
        domain: ''
    });

    const addDomain = (e) => {
        e.preventDefault();
        if (!data.domain.trim()) return;

        post(`/workspace/${workspace.id}/domains`, {
            onSuccess: () => {
                reset();
                onDomainAdded();
            }
        });
    };

    const removeDomain = (domainId) => {
        if (confirm('Tem certeza que deseja remover este domínio?')) {
            onDomainRemoved(domainId);
        }
    };

    const activateDomain = (domainId) => {
        // Implementar ativação de domínio
        // console.log('Ativar domínio:', domainId);
    };

    const activeDomains = domains.filter(d => d.is_active);
    const inactiveDomains = domains.filter(d => !d.is_active);

    return (
        <Modal show={show} onClose={onClose} maxWidth="2xl">
            <div className="bg-slate-800 rounded-lg p-6 max-h-[80vh] overflow-hidden">
                <div className="flex justify-between items-center mb-6">
                    <div>
                        <h3 className="text-lg font-semibold text-white">Gerenciar Domínios Permitidos</h3>
                        <p className="text-slate-400 text-sm mt-1">
                            {activeDomains.length} domínio(s) ativo(s) de {auth.user.plan?.max_domains || 1} permitidos
                        </p>
                    </div>
                    <button 
                        onClick={onClose}
                        className="text-slate-400 hover:text-white"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {/* Formulário para Adicionar Domínio */}
                <form onSubmit={addDomain} className="mb-6">
                    <div className="flex items-start space-x-3">
                        <div className="flex-1">
                            <input 
                                type="text" 
                                value={data.domain}
                                onChange={(e) => setData('domain', e.target.value)}
                                placeholder="exemplo.com ou *.exemplo.com" 
                                className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white text-sm focus:ring-cyan-500 focus:border-cyan-500"
                                pattern="^(\*\.)?([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}(:\d+)?$|^localhost(:\d+)?$|^(\*\.)?([a-z0-9]+(-[a-z0-9]+)*\.)?localhost(:\d+)?$"
                                title="Digite um domínio válido (ex: site.com, *.site.com)"
                                required
                            />
                            {errors.domain && (
                                <span className="text-red-400 text-xs mt-1">{errors.domain}</span>
                            )}
                            <p className="text-slate-500 text-xs mt-1">
                                Use *.exemplo.com para permitir todos os subdomínios
                            </p>
                        </div>
                        <button 
                            type="submit"
                            disabled={processing}
                            className="bg-teal-500 hover:bg-teal-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap disabled:opacity-50"
                        >
                            {processing ? 'Adicionando...' : 'Adicionar Domínio'}
                        </button>
                    </div>
                </form>

                {/* Lista de Domínios Ativos */}
                <div className="space-y-3 mb-6">
                    <h5 className="text-sm font-medium text-slate-300">Domínios Ativos</h5>
                    
                    {activeDomains.length > 0 ? (
                        activeDomains.map((domain) => (
                            <div key={domain.id} className="flex items-center justify-between p-3 bg-slate-700 rounded-lg">
                                <div className="flex items-center space-x-3">
                                    <svg className="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span className="text-white text-sm font-mono">{domain.domain}</span>
                                    <span className="text-slate-500 text-xs">{new Date(domain.created_at).toLocaleDateString('pt-BR')}</span>
                                    {domain.domain.startsWith('*.') && (
                                        <span className="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs">Wildcard</span>
                                    )}
                                </div>
                                <button 
                                    onClick={() => removeDomain(domain.id)}
                                    className="text-red-400 hover:text-red-300 transition-colors"
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
                    <div className="pt-6 border-t border-slate-700">
                        <h5 className="text-sm font-medium text-slate-300 mb-3">Domínios Inativos</h5>
                        <div className="space-y-2">
                            {inactiveDomains.map((domain) => (
                                <div key={domain.id} className="flex items-center justify-between p-2 bg-slate-700 rounded opacity-60">
                                    <div className="flex items-center space-x-2">
                                        <svg className="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        <span className="text-slate-300 text-xs font-mono">{domain.domain}</span>
                                    </div>
                                    <button 
                                        onClick={() => activateDomain(domain.id)}
                                        className="text-teal-400 hover:text-teal-300 text-xs transition-colors"
                                    >
                                        Reativar
                                    </button>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {/* Informações do Plano */}
                <div className="mt-6 p-4 bg-slate-700 rounded-lg">
                    <h6 className="text-sm font-medium text-slate-300 mb-2">Limites do Plano</h6>
                    <div className="flex justify-between items-center text-xs">
                        <span className="text-slate-400">Domínios ativos:</span>
                        <span className="text-white font-medium">
                            {activeDomains.length} / {auth.user.plan?.max_domains || 1}
                        </span>
                    </div>
                    <div className="w-full bg-slate-600 rounded-full h-2 mt-2">
                        <div 
                            className="bg-teal-400 h-2 rounded-full transition-all" 
                            style={{ 
                                width: `${Math.min(100, (activeDomains.length / (auth.user.plan?.max_domains || 1)) * 100)}%` 
                            }}
                        ></div>
                    </div>
                </div>
            </div>
        </Modal>
    );
}