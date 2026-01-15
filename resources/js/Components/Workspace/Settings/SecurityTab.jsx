// resources/js/Components/Workspace/Settings/SecurityTab.jsx
import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import QuickActionsCard from './QuickActionsCard';
import Alert from '@/Components/Alerts/Alert';

export default function SecurityTab({ workspace, auth }) {
    const [copied, setCopied] = useState(false);
    const { data, setData, put, processing } = useForm({
        type_view_workspace: workspace.type_view_workspace_id
    });

    const handleCopyApiKey = async () => {
        try {
            await navigator.clipboard.writeText(workspace.workspace_key_api);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch (error) {
            console.error('Erro ao copiar chave API:', error);
        }
    };

    const handleGenerateNewHash = async () => {
        if (!confirm('Tem certeza que deseja gerar uma nova chave API? Isso invalidará a chave atual.')) {
            return;
        }

        try {
            await router.put(route('workspace.update.generateNewHashApi', workspace.id));
            // Recarregar a página para mostrar a nova chave
            router.reload();
        } catch (error) {
            console.error('Erro ao gerar nova chave:', error);
        }
    };

    const handleSaveViewType = () => {
        put(route('workspace.update.viewWorkspace', workspace.id), {
            preserveScroll: true
        });
    };

    return (
        <>
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div className="lg:col-span-2 space-y-8">
                    <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <div className="flex justify-between items-center mb-4">
                            <h2 className="text-lg font-medium text-gray-900 dark:text-white">
                                Visualização da API
                            </h2>
                            <button
                                onClick={handleSaveViewType}
                                disabled={processing}
                                className="text-teal-600 bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 dark:text-teal-400 dark:hover:bg-teal-900/50 px-4 py-2 rounded-lg text-sm disabled:opacity-50"
                            >
                                <i className="fas fa-save mr-2"></i>
                                {processing ? 'Salvando...' : 'Salvar'}
                            </button>
                        </div>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            Escolha como deseja visualizar e acessar os dados deste workspace.
                        </p>
                        
                        <div className="grid gap-4 md:grid-cols-2">
                            <ViewTypeOption
                                id="geev-studio"
                                name="type_view_workspace"
                                value={1}
                                checked={data.type_view_workspace == 1}
                                onChange={() => setData('type_view_workspace', 1)}
                                title="Geev Studio"
                                description="Interface visual amigável para gerenciar dados"
                                icon="desktop"
                            />
                            
                            {['start', 'pro', 'premium', 'admin'].includes(auth.user.plan?.name.toLowerCase()) ? (
                                <ViewTypeOption
                                    id="json-geev-api"
                                    name="type_view_workspace"
                                    value={2}
                                    checked={data.type_view_workspace == 2}
                                    onChange={() => setData('type_view_workspace', 2)}
                                    title="Geev API"
                                    description="Acesso direto via API REST com JSON"
                                    icon="code"
                                />
                            ) : (
                                <UpgradeViewTypeOption />
                            )}
                        </div>
                    </div>

                    <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <div className="flex justify-between items-center mb-4">
                            <h2 className="text-lg font-medium text-gray-900 dark:text-white">
                                Chave API do Workspace
                            </h2>
                            <button
                                onClick={handleGenerateNewHash}
                                className="text-teal-600 bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 dark:text-teal-400 dark:hover:bg-teal-900/50 px-4 py-2 rounded-lg text-sm"
                            >
                                <i className="fas fa-sync-alt mr-1"></i> Gerar Nova Chave
                            </button>
                        </div>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            Use esta chave para acessar a API deste workspace.
                        </p>
                        
                        <div className="flex">
                            <input
                                type="text"
                                value={workspace.workspace_key_api}
                                readOnly
                                className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                            />
                            <button
                                onClick={handleCopyApiKey}
                                className="relative text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-r-lg text-sm px-4 text-center inline-flex items-center dark:bg-teal-500 dark:hover:bg-teal-600 dark:focus:ring-teal-800"
                            >
                                <i className="fas fa-copy"></i>
                                {copied && (
                                    <span className="copied-tooltip absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs py-1 px-2 rounded">
                                        Copiado!
                                    </span>
                                )}
                            </button>
                        </div>
                    </div>
                </div>

                <div className="space-y-8">
                    <QuickActionsCard workspace={workspace} />
                </div>
            </div>
        </>
    );
}

function ViewTypeOption({ id, name, value, checked, onChange, title, description, icon }) {
    return (
        <div>
            <input
                type="radio"
                id={id}
                name={name}
                value={value}
                checked={checked}
                onChange={onChange}
                className="hidden peer"
            />
            <label
                htmlFor={id}
                className="inline-flex items-center justify-between w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-gray-700 dark:peer-checked:text-teal-500 peer-checked:border-teal-600 peer-checked:text-teal-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700"
            >
                <div className="block">
                    <div className="w-full text-lg font-semibold">{title}</div>
                    <div className="w-full text-sm">{description}</div>
                </div>
                <i className={`fas fa-${icon} text-xl`}></i>
            </label>
        </div>
    );
}

function UpgradeViewTypeOption() {
    return (
        <a href={route('subscription.pricing')} className="dark:bg-purple-900/20 rounded-lg block">
            <label className="inline-flex items-center justify-between w-full p-4 text-gray-500 border border-gray-200 rounded-lg cursor-pointer dark:border-gray-700">
                <div className="block rounded-full items-center justify-center mr-3 bg-gradient-to-r">
                    <div className="flex items-center">
                        <span className="text-lg font-semibold text-white">Geev API</span>
                        <span className="ml-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-xs px-2 py-0.5 rounded-full">
                            PRO
                        </span>
                    </div>
                    <div className="w-full text-sm text-purple-300 mt-1">
                        Acesso direto via API REST com JSON
                    </div>
                </div>
                <i className="fas fa-code text-xl"></i>
            </label>
        </a>
    );
}