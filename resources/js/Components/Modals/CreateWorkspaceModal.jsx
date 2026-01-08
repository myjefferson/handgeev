import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function ModalCreateWorkspace({ show, onClose }){
    const [workspaceType, setWorkspaceType] = useState('1');

    const { data, setData, post, processing, errors, reset } = useForm({
        title: '',
        type_workspace_id: '1',
        is_published: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('workspace.create'), {
            onSuccess: () => {
                reset();
                onClose();
            },
        });
    };

    if (!show) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
            <div className="relative w-full max-w-md">
                <div className="relative bg-slate-800 rounded-xl shadow-sm border border-slate-700">
                    {/* Modal header */}
                    <div className="flex items-center justify-between p-6 border-b border-slate-700">
                        <h3 className="text-xl font-semibold text-white">
                            Criar Novo Workspace
                        </h3>
                        <button 
                            type="button"
                            onClick={onClose}
                            className="text-slate-400 bg-transparent hover:bg-slate-700 hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center"
                        >
                            <svg className="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span className="sr-only">Fechar modal</span>
                        </button>
                    </div>

                    {/* Modal body */}
                    <div className="p-6">
                        <form className="space-y-6" onSubmit={handleSubmit}>
                            <div>
                                <label htmlFor="workspace-title" className="block mb-2 text-sm font-medium text-slate-300">
                                    Título do Workspace
                                </label>
                                <input 
                                    type="text" 
                                    name="title" 
                                    id="workspace-title"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    className="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg focus:ring-cyan-500 focus:border-cyan-500 block w-full p-3" 
                                    placeholder="Ex: Meu Portfólio de Projetos" 
                                    required 
                                />
                                {errors.title && (
                                    <p className="text-red-400 text-sm mt-1">{errors.title}</p>
                                )}
                            </div>
                            
                            <div>
                                <label className="block mb-2 text-sm font-medium text-slate-300">
                                    Tipo de Tópico
                                </label>
                                <div className="grid gap-4 md:grid-cols-2">
                                    {/* Opção: Tópico Único */}
                                    <div>
                                        <input 
                                            type="radio" 
                                            id="single-topic-card" 
                                            name="type_workspace_id" 
                                            value="1" 
                                            checked={data.type_workspace_id === '1'}
                                            onChange={(e) => setData('type_workspace_id', e.target.value)}
                                            className="hidden peer" 
                                            required 
                                        />
                                        <label 
                                            htmlFor="single-topic-card" 
                                            className="inline-flex items-center justify-between w-full p-4 text-slate-400 bg-slate-700 border border-slate-600 rounded-lg cursor-pointer hover:border-cyan-500/50 peer-checked:border-cyan-500 peer-checked:text-cyan-400 transition-all duration-200"
                                        >
                                            <div className="block">
                                                <div className="w-full text-lg font-semibold">Tópico Único</div>
                                                <div className="w-full text-sm mt-1">Uma única seção para todo o conteúdo.</div>
                                            </div>
                                            <svg className="w-5 h-5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                            </svg>
                                        </label>
                                    </div>
                                    
                                    {/* Opção: Vários Tópicos */}
                                    <div>
                                        <input 
                                            type="radio" 
                                            id="multiple-topics-card" 
                                            name="type_workspace_id" 
                                            value="2" 
                                            checked={data.type_workspace_id === '2'}
                                            onChange={(e) => setData('type_workspace_id', e.target.value)}
                                            className="hidden peer" 
                                        />
                                        <label 
                                            htmlFor="multiple-topics-card" 
                                            className="inline-flex items-center justify-between w-full p-4 text-slate-400 bg-slate-700 border border-slate-600 rounded-lg cursor-pointer hover:border-cyan-500/50 peer-checked:border-cyan-500 peer-checked:text-cyan-400 transition-all duration-200"
                                        >
                                            <div className="block">
                                                <div className="w-full text-lg font-semibold">Vários Tópicos</div>
                                                <div className="w-full text-sm mt-1">Organize seu conteúdo em várias seções.</div>
                                            </div>
                                            <svg className="w-5 h-5 ms-3 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                                <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                                            </svg>
                                        </label>
                                    </div>
                                </div>
                                {errors.type_workspace_id && (
                                    <p className="text-red-400 text-sm mt-1">{errors.type_workspace_id}</p>
                                )}
                            </div>
                            
                            <div className="flex items-start">
                                <div className="flex items-center h-5">
                                    <input 
                                        id="is-published" 
                                        type="checkbox" 
                                        name="is_published"
                                        checked={data.is_published}
                                        onChange={(e) => setData('is_published', e.target.checked)}
                                        className="w-4 h-4 border border-slate-600 rounded-sm bg-slate-700 focus:ring-2 focus:ring-cyan-500 focus:ring-offset-slate-800" 
                                    />
                                </div>
                                <label htmlFor="is-published" className="ms-2 text-sm font-medium text-slate-300">
                                    Publicar agora
                                </label>
                            </div>
                            
                            <button 
                                type="submit" 
                                disabled={processing}
                                className="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-lg px-5 py-3 text-center transition-colors disabled:opacity-50"
                            >
                                {processing ? 'Criando...' : 'Criar Workspace'}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
};