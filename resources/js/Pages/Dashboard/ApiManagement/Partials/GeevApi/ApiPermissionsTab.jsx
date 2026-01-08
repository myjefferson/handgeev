// resources/js/Pages/ApiManagement/Partials/ApiPermissionsTab.jsx
import React, { useState, useEffect } from 'react';
import { usePage, router } from '@inertiajs/react';

export default function ApiPermissionsTab({ workspace }) {
    const { auth } = usePage().props;
    const [permissions, setPermissions] = useState({
        workspace: ['GET'],
        topics: ['GET'],
        fields: ['GET']
    });
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadPermissions();
    }, []);

    const loadPermissions = async () => {
        try {
            // Implementar chamada à API para buscar permissões
            // const response = await fetch(`/api/workspace/${workspace.id}/permissions`);
            // const data = await response.json();
            // setPermissions(data.permissions);
            
            setLoading(false);
        } catch (error) {
            console.error('Erro ao carregar permissões:', error);
            setLoading(false);
        }
    };

    const updatePermission = async (endpoint, method, isAllowed) => {
        if (!['pro', 'premium', 'admin'].includes(auth.user.plan?.name.toLowerCase())) {
            alert('Este recurso está disponível apenas para planos Pro e Premium');
            return;
        }

        try {
            const currentMethods = [...(permissions[endpoint] || [])];
            let newMethods;

            if (isAllowed) {
                newMethods = [...new Set([...currentMethods, method])];
            } else {
                newMethods = currentMethods.filter(m => m !== method);
            }

            // Implementar chamada à API para atualizar permissões
            // await fetch(`/api/workspace/${workspace.id}/permissions`, {
            //     method: 'PUT',
            //     headers: {
            //         'Content-Type': 'application/json',
            //         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            //     },
            //     body: JSON.stringify({
            //         endpoint: endpoint,
            //         methods: newMethods
            //     })
            // });

            setPermissions(prev => ({
                ...prev,
                [endpoint]: newMethods
            }));

            // Mostrar mensagem de sucesso
        } catch (error) {
            console.error('Erro ao atualizar permissão:', error);
            // Mostrar mensagem de erro
        }
    };

    const renderPermissionSection = (endpoint, title) => {
        const methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        const allowedMethods = permissions[endpoint] || [];
        const canEdit = ['pro', 'premium', 'admin'].includes(auth.user.plan?.name.toLowerCase());

        return (
            <div className="bg-slate-800 rounded-xl p-6 border border-slate-700 mb-6">
                <h4 className="text-cyan-400 text-lg font-semibold mb-4">{title}</h4>
                <div className="grid grid-cols-2 md:grid-cols-5 gap-3">
                    {methods.map((method) => {
                        const isAllowed = allowedMethods.includes(method);
                        const isDisabled = !canEdit;
                        
                        return (
                            <label 
                                key={method}
                                className={`relative flex items-center p-3 rounded-lg border-2 cursor-pointer transition-all ${
                                    isAllowed 
                                        ? 'bg-cyan-500/10 border-cyan-500/50 text-cyan-400' 
                                        : 'bg-slate-800 border-slate-700 text-slate-400'
                                } ${isDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:border-cyan-400/50'}`}
                            >
                                <input 
                                    type="checkbox" 
                                    value={method}
                                    checked={isAllowed}
                                    disabled={isDisabled}
                                    onChange={(e) => updatePermission(endpoint, method, e.target.checked)}
                                    className="hidden"
                                />
                                <span className="font-mono text-sm font-medium">{method}</span>
                                {isDisabled && (
                                    <div 
                                        className="absolute -top-1 -right-1"
                                        title="Recurso disponível apenas para planos Pro e Premium"
                                    >
                                        <svg className="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fillRule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clipRule="evenodd"/>
                                        </svg>
                                    </div>
                                )}
                            </label>
                        );
                    })}
                </div>
            </div>
        );
    };

    if (loading) {
        return (
            <div className="p-6 rounded-lg bg-slate-800/50 border border-slate-700">
                <div className="text-center py-8">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-400 mx-auto"></div>
                    <p className="text-slate-300 mt-4">Carregando permissões...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="p-6 rounded-lg bg-slate-800/50 border border-slate-700">
            <div className="flex items-center mb-6">
                <h3 className="text-xl font-semibold text-white">🔐 Permissões de Métodos HTTP</h3>
            </div>

            <div className="space-y-6">
                {renderPermissionSection('workspace', '📁 Workspace')}
                {renderPermissionSection('topics', '🗂️ Tópicos')}
                {renderPermissionSection('fields', '🔤 Campos')}

                {(auth.user.isFree || auth.user.isStart) && (
                    <div className="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4">
                        <div className="flex items-center">
                            <svg className="w-5 h-5 text-amber-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.994-.833-2.764 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <div>
                                <h5 className="text-amber-400 font-semibold">Configuração Avançada de Permissões</h5>
                                <p className="text-amber-300 text-sm mt-1">
                                    A configuração granular de métodos HTTP está disponível apenas para planos Pro e Premium.
                                    <a 
                                        href="/subscription/pricing" 
                                        className="underline hover:text-amber-200 ml-1"
                                    >
                                        Faça upgrade para desbloquear este recurso.
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}