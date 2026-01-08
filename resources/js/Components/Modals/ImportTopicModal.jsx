// resources/js/Components/Workspace/Modals/ImportModal.jsx
import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';

export default function ImportModal({ isOpen, onClose, workspace }) {
    const [activeTab, setActiveTab] = useState('file');
    const { data, setData, post, processing, errors } = useForm({
        topic_title: '',
        file: null,
        import_method: 'file'
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('workspace.import', { id: workspace.id }), {
            onSuccess: () => {
                onClose();
            }
        });
    };

    const handleFileChange = (e) => {
        setData('file', e.target.files[0]);
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 border border-slate-700">
                <div className="flex justify-between items-center mb-4">
                    <h3 className="text-lg font-semibold text-white">Importar Tópico</h3>
                    <button onClick={onClose} className="text-slate-400 hover:text-white">
                        <i className="fas fa-times"></i>
                    </button>
                </div>
                
                <div className="space-y-4">
                    {/* Abas de Importação */}
                    <div className="flex border-b border-slate-700">
                        <button 
                            onClick={() => setActiveTab('file')}
                            className={`import-tab py-2 px-4 border-b-2 font-medium ${
                                activeTab === 'file' 
                                    ? 'border-blue-500 text-white' 
                                    : 'border-transparent text-slate-400 hover:text-white'
                            }`}
                        >
                            <i className="fas fa-file-import mr-2"></i>Importar Arquivo
                        </button>
                    </div>
                    
                    {/* Conteúdo - Importar por Arquivo */}
                    {activeTab === 'file' && (
                        <form id="importFileForm" onSubmit={handleSubmit}>
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-sm font-medium text-slate-400 mb-2">
                                        Nome do Tópico
                                    </label>
                                    <input 
                                        type="text" 
                                        name="topic_title" 
                                        value={data.topic_title}
                                        onChange={(e) => setData('topic_title', e.target.value)}
                                        required
                                        className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white placeholder-slate-400"
                                        placeholder="Nome do tópico"
                                    />
                                    {errors.topic_title && (
                                        <p className="text-red-400 text-xs mt-1">{errors.topic_title}</p>
                                    )}
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-slate-400 mb-2">
                                        Arquivo JSON
                                    </label>
                                    <input 
                                        type="file" 
                                        name="file" 
                                        accept=".json" 
                                        onChange={handleFileChange}
                                        required
                                        className="w-full bg-slate-700 border border-slate-600 rounded-md px-3 py-2 text-white file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-400"
                                    />
                                    <p className="text-xs text-slate-400 mt-1">
                                        Selecione um arquivo JSON exportado do HandGeev
                                    </p>
                                    {errors.file && (
                                        <p className="text-red-400 text-xs mt-1">{errors.file}</p>
                                    )}
                                </div>
                            </div>
                            
                            <div className="flex justify-end space-x-3 pt-4 border-t border-slate-700">
                                <button 
                                    type="button"
                                    onClick={onClose}
                                    className="px-4 py-2 text-slate-400 hover:text-white transition-colors"
                                >
                                    Cancelar
                                </button>
                                <button 
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg transition-colors disabled:opacity-50"
                                >
                                    <i className="fas fa-download mr-2"></i>
                                    {processing ? 'Importando...' : 'Importar'}
                                </button>
                            </div>
                        </form>
                    )}
                </div>
            </div>
        </div>
    );
}