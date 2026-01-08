// resources/js/Components/Workspace/Settings/PasswordProtectionCard.jsx
import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';

export default function PasswordProtectionCard({ workspace, hasPasswordWorkspace, upgrade = false }) {
    const { data, setData, put, processing, errors } = useForm({
        is_published: workspace?.is_published || false,
        password_enabled: hasPasswordWorkspace || false,
        password: ''
    });

    const [showPasswordField, setShowPasswordField] = useState(data.password_enabled);
    const [notification, setNotification] = useState(null);

    useEffect(() => {
        setShowPasswordField(data.password_enabled);
    }, [data.password_enabled]);

    const handleSubmit = async (e) => {
        e.preventDefault();
        
        if (data.password_enabled && !data.password) {
            setNotification({
                type: 'error',
                message: 'Por favor, digite uma senha quando a proteção por senha estiver ativada.'
            });
            return;
        }

        if (data.password_enabled && data.password.length < 8) {
            setNotification({
                type: 'error',
                message: 'A senha deve ter pelo menos 8 caracteres.'
            });
            return;
        }

        try {
            await put(route('workspace.update.access-settings', workspace.id), {
                preserveScroll: true,
                onSuccess: () => {
                    // Limpar senha do formulário após sucesso
                    setData('password', '');
                }
            });
        } catch (error) {
            setNotification({
                type: 'error',
                message: 'Erro ao salvar configurações.'
            });
        }
    };

    if (upgrade) {
        return (
            <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div className="text-center py-8">
                    <div className="bg-gradient-to-r from-purple-500 to-pink-500 text-white p-3 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <i className="fas fa-lock text-2xl"></i>
                    </div>
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                        Proteção por Senha
                    </h3>
                    <p className="text-gray-500 dark:text-gray-400 mb-4">
                        Proteja seu workspace com senha. Disponível para usuários Pro.
                    </p>
                    <a
                        href={route('subscription.pricing')}
                        className="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg hover:from-purple-600 hover:to-pink-600 transition-colors"
                    >
                        <i className="fas fa-crown mr-2"></i>
                        Fazer Upgrade
                    </a>
                </div>
            </div>
        );
    }

    return (
        <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div className="flex items-center justify-between mb-4">
                <h2 className="text-lg font-medium text-gray-900 dark:text-white">
                    Tipo de Acesso
                </h2>
                <button
                    onClick={handleSubmit}
                    disabled={processing}
                    className="text-teal-600 bg-teal-50 hover:bg-teal-100 dark:bg-teal-900/30 dark:text-teal-400 dark:hover:bg-teal-900/50 px-4 py-2 rounded-lg text-sm disabled:opacity-50"
                >
                    <i className="fas fa-save mr-2"></i>
                    {processing ? 'Salvando...' : 'Salvar'}
                </button>
            </div>

            {notification && (
                <div className={`mb-4 p-3 rounded-lg ${
                    notification.type === 'success' 
                        ? 'bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-700'
                        : 'bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-700'
                }`}>
                    <div className="flex items-center">
                        <i className={`fas fa-${notification.type === 'success' ? 'check' : 'exclamation-triangle'} mr-2`}></i>
                        <span>{notification.message}</span>
                    </div>
                </div>
            )}

            <div className="mb-6">
                <p className="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Escolha quem pode acessar este workspace e seus dados via API.
                </p>
                <div className="grid grid-cols-2 gap-3">
                    <AccessTypeOption
                        id="access-private"
                        name="access_type"
                        value="private"
                        checked={!data.is_published}
                        onChange={() => setData('is_published', false)}
                        title="Privado"
                        description="Somente para mim"
                    />
                    
                    <AccessTypeOption
                        id="access-public"
                        name="access_type"
                        value="public"
                        checked={data.is_published}
                        onChange={() => setData('is_published', true)}
                        title="Público"
                        description="Qualquer pessoa pode visualizar"
                    />
                </div>
            </div>

            {data.is_published && (
                <div className="border-t pt-4 border-gray-200 dark:border-gray-700">
                    <div className="flex items-center justify-between mb-4">
                        <div>
                            <h3 className="text-md font-medium text-gray-900 dark:text-white">
                                Proteção por Senha
                            </h3>
                            <p className="text-sm text-gray-500 dark:text-gray-400">
                                Adicione uma senha para controlar o acesso público
                            </p>
                        </div>
                        <label className="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                checked={data.password_enabled}
                                onChange={e => setData('password_enabled', e.target.checked)}
                                className="sr-only peer"
                            />
                            <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-teal-600"></div>
                        </label>
                    </div>
                    
                    {data.password_enabled && (
                        <div className="mt-4">
                            <div className="flex gap-3">
                                <div className="flex-1">
                                    <label htmlFor="workspace-password" className="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Senha de Acesso
                                    </label>
                                    <input
                                        type="password"
                                        id="workspace-password"
                                        value={data.password}
                                        onChange={e => setData('password', e.target.value)}
                                        className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-teal-500 focus:border-teal-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                        placeholder="Digite uma senha segura"
                                        autoComplete="new-password"
                                    />
                                </div>
                            </div>
                            <p className="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                A senha deve ter no mínimo 8 caracteres. Quem tiver a senha poderá acessar os dados públicos.
                            </p>
                            {errors.password && (
                                <p className="mt-1 text-sm text-red-600 dark:text-red-400">{errors.password}</p>
                            )}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}

function AccessTypeOption({ id, name, value, checked, onChange, title, description }) {
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
                className="flex flex-col p-3 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer peer-checked:border-teal-500 peer-checked:bg-teal-50 peer-checked:text-teal-700 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400 dark:peer-checked:bg-teal-900/20 dark:peer-checked:border-teal-500 dark:peer-checked:text-teal-300"
            >
                <span className="font-medium">{title}</span>
                <span className="text-xs mt-1">{description}</span>
            </label>
        </div>
    );
}