import React, { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import useLang from '@/Hooks/useLang';

const Settings = ({ auth, settings }) => {
    const { __ } = useLang()
    const { props } = usePage();
    
    const [isGenerating, setIsGenerating] = useState(false);
    const [apiKey, setApiKey] = useState(settings?.global_key_api || __('not_generated'));
    const [languageMessage, setLanguageMessage] = useState('');
    const [timezoneMessage, setTimezoneMessage] = useState('');

    // Função para copiar texto
    const copyToClipboard = (text) => {
        if (text && text !== __('not_generated')) {
            navigator.clipboard.writeText(text).then(() => {
                alert(__('copied'));
            });
        }
    };

    // Função para mostrar mensagens temporárias
    const showTemporaryMessage = (setter, message, isSuccess = true) => {
        setter(message);
        setTimeout(() => setter(''), 3000);
    };

    // Alterar Idioma
    const handleLanguageChange = async (e) => {
        const language = e.target.value;
        
        try {
            const response = await fetch(route('settings.language'), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': props.csrf_token,
                },
                body: JSON.stringify({ language }),
            });

            const data = await response.json();

            if (data.success) {
                showTemporaryMessage(setLanguageMessage, data.message, true);
                // Recarrega a página para aplicar o novo idioma
                setTimeout(() => window.location.reload(), 1000);
            } else {
                showTemporaryMessage(setLanguageMessage, data.message, false);
            }
        } catch (error) {
            showTemporaryMessage(setLanguageMessage, __('update_error'), false);
        }
    };

    // Alterar Fuso Horário
    const handleTimezoneChange = async (e) => {
        const timezone = e.target.value;
        
        try {
            const response = await fetch(route('timezone'), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': props.csrf_token,
                },
                body: JSON.stringify({ timezone }),
            });

            const data = await response.json();

            if (data.success) {
                showTemporaryMessage(setTimezoneMessage, data.message, true);
            } else {
                showTemporaryMessage(setTimezoneMessage, data.message, false);
            }
        } catch (error) {
            showTemporaryMessage(setTimezoneMessage, __('update_error'), false);
        }
    };

    // Gerar novo código API
    const handleGenerateApiKey = async () => {
        if (!confirm(__('generate_confirm'))) {
            return;
        }

        setIsGenerating(true);

        try {
            const response = await fetch(route('dashboard.settings.update.hash'), {
                method: 'PUT',
                headers: {
                    // 'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': props.csrf_token,
                },
            });

            const data = await response.json();

            if (data.success) {
                setApiKey(data.data.global_key_api);
                alert(__('generate_success'));
            } else {
                alert(__('generate_error'));
            }
        } catch (error) {
            alert(`${__('update_error')}: ${error.message}`);
        } finally {
            setIsGenerating(false);
        }
    };

    return (
        <DashboardLayout>
            <Head 
                title={__('title')} 
                description={__('description')} 
            />

            <div className="max-w-4xl mx-auto">        
                <div className="flex justify-between items-center">
                    <h3 className="title-header text-2xl font-semibold text-white">
                        {__('title')}
                    </h3>
                </div>
                
                <div className="mt-8 space-y-8">
                    {/* Seção de API */}
                    <section className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                        <div className="flex items-center justify-between mb-6">
                            <h2 className="text-xl font-semibold text-white flex items-center">
                                <svg className="w-5 h-5 mr-2 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                {__('api_code')}
                            </h2>
                        </div>
                        
                        <div className="space-y-6">
                            {/* Código Primário */}
                            <div className="bg-slate-900 rounded-lg p-5 border border-slate-700">
                                <label className="block text-sm font-medium text-slate-400 mb-2">
                                    {__('global_api_key')}
                                </label>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center">
                                        <code className="font-mono text-teal-400 bg-slate-800 px-3 py-2 rounded-lg text-sm break-all">
                                            {apiKey}
                                        </code>
                                    </div>
                                    <button 
                                        className="copy-btn text-slate-400 hover:text-teal-400 transition-colors"
                                        onClick={() => copyToClipboard(apiKey)}
                                        title={__('copy')}
                                    >
                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                                <p className="text-xs text-slate-500 mt-2">
                                    {__('global_api_description')}
                                </p>
                            </div>
                                            
                            {/* Botão de Gerar Código */}
                            <div className="bg-gradient-to-r from-teal-400/10 to-blue-400/10 rounded-lg p-5 border border-teal-400/20">
                                <h3 className="font-medium text-white mb-2">
                                    {__('generate_new_code')}
                                </h3>
                                <p className="text-sm text-slate-400 mb-4">
                                    {__('generate_warning')}
                                </p>
                                <button 
                                    id="generateCodeButton"
                                    onClick={handleGenerateApiKey}
                                    disabled={isGenerating}
                                    className="bg-teal-400 hover:bg-teal-600 text-slate-900 font-medium py-3 px-6 rounded-lg transition-colors flex items-center justify-center disabled:opacity-50"
                                >
                                    {isGenerating ? (
                                        <>
                                            <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-slate-900 mr-2"></div>
                                            {__('generating')}
                                        </>
                                    ) : (
                                        <>
                                            <svg className="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M4 20v-2h2.75l-.4-.35q-1.225-1.225-1.787-2.662T4 12.05q0-2.775 1.663-4.937T10 4.25v2.1Q8.2 7 7.1 8.563T6 12.05q0 1.125.425 2.188T7.75 16.2l.25.25V14h2v6zm10-.25v-2.1q1.8-.65 2.9-2.212T18 11.95q0-1.125-.425-2.187T16.25 7.8L16 7.55V10h-2V4h6v2h-2.75l.4.35q1.225 1.225 1.788 2.663T20 11.95q0 2.775-1.662 4.938T14 19.75"/>
                                            </svg>
                                            {__('generate_global_api')}
                                        </>
                                    )}
                                </button>
                            </div>
                        </div>
                    </section>

                    {/* Seção de Preferências */}
                    <section className="bg-slate-800 rounded-xl p-6 border border-slate-700">
                        <h2 className="text-xl font-semibold text-white mb-6 flex items-center">
                            <svg className="w-5 h-5 mr-2 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {__('preferences')}
                        </h2>
                        
                        <div className="grid md:grid-cols-2 gap-6">
                            {/* Idioma */}
                            <div className="bg-slate-900 rounded-lg p-4 border border-slate-700">
                                <h3 className="font-medium text-white mb-2">
                                    {__('language')}
                                </h3>
                                <p className="text-sm text-slate-400 mb-4">
                                    {__('language_description')}
                                </p>
                                <select 
                                    id="languageSelect"
                                    defaultValue={auth.user.language}
                                    onChange={handleLanguageChange}
                                    className="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-3 py-2 focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition-colors"
                                >
                                    <option value="pt_BR">Português (Brasil)</option>
                                    <option value="en">English</option>
                                    <option value="es">Español</option>
                                </select>
                                {languageMessage && (
                                    <div 
                                        id="languageMessage" 
                                        className={`mt-2 text-xs ${
                                            languageMessage.includes('sucesso') || languageMessage.includes('success') 
                                                ? 'text-green-400' 
                                                : 'text-red-400'
                                        }`}
                                    >
                                        {languageMessage}
                                    </div>
                                )}
                            </div>
                            
                            {/* Fuso Horário */}
                            <div className="bg-slate-900 rounded-lg p-4 border border-slate-700">
                                <h3 className="font-medium text-white mb-2">
                                    {__('timezone')}
                                </h3>
                                <p className="text-sm text-slate-400 mb-4">
                                    {__('timezone_description')}
                                </p>
                                <select 
                                    id="timezoneSelect"
                                    defaultValue={auth.user.timezone}
                                    onChange={handleTimezoneChange}
                                    className="w-full bg-slate-800 border border-slate-700 text-white rounded-lg px-3 py-2 focus:border-teal-400 focus:ring-1 focus:ring-teal-400 transition-colors"
                                >
                                    <option value="America/Sao_Paulo">Brasília (GMT-3)</option>
                                    <option value="UTC">UTC</option>
                                    <option value="America/New_York">New York (GMT-5)</option>
                                    <option value="Europe/London">London (GMT+0)</option>
                                    <option value="Asia/Tokyo">Tokyo (GMT+9)</option>
                                </select>
                                {timezoneMessage && (
                                    <div 
                                        id="timezoneMessage" 
                                        className={`mt-2 text-xs ${
                                            timezoneMessage.includes('sucesso') || timezoneMessage.includes('success') 
                                                ? 'text-green-400' 
                                                : 'text-red-400'
                                        }`}
                                    >
                                        {timezoneMessage}
                                    </div>
                                )}
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <style jsx>{`
                .animate-spin {
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
                code {
                    word-break: break-all;
                    font-family: 'Fira Code', 'Monaco', 'Cascadia Code', 'Roboto Mono', monospace;
                }
            `}</style>
        </DashboardLayout>
    );
};

export default Settings;