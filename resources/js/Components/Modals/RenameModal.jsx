// resources/js/Components/Workspace/Modals/RenameModal.jsx
import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';

export default function RenameModal({ isOpen, onClose, topic }) {
    const [charCount, setCharCount] = useState(0);
    const { data, setData, put, processing, errors } = useForm({
        title: ''
    });

    useEffect(() => {
        if (topic && isOpen) {
            setData('title', topic.title);
            setCharCount(topic.title.length);
        }
    }, [topic, isOpen]);

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!topic) return;

        put(route('topic.update', { id: topic.id }), {
            onSuccess: () => {
                onClose();
            }
        });
    };

    const handleTitleChange = (e) => {
        const value = e.target.value;
        setData('title', value);
        setCharCount(value.length);
    };

    if (!isOpen || !topic) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="relative p-4 w-full max-w-md">
                <div className="relative bg-slate-800 rounded-lg shadow-sm border border-slate-700">
                    <div className="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-slate-600">
                        <h3 className="text-xl font-semibold text-white">
                            Renomear Tópico
                        </h3>
                        <button 
                            onClick={onClose}
                            className="text-slate-400 bg-transparent hover:bg-slate-600 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        >
                            <i className="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form onSubmit={handleSubmit}>
                        <div className="p-4 md:p-5 space-y-4">
                            <div>
                                <label htmlFor="rename-topic-title" className="block text-sm font-medium text-gray-300 mb-2">
                                    Título do Tópico
                                </label>
                                <input 
                                    type="text"
                                    id="rename-topic-title"
                                    value={data.title}
                                    onChange={handleTitleChange}
                                    placeholder="Meu Novo Tópico"
                                    className="w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    maxLength={100}
                                    autoComplete="off"
                                    required
                                />
                                <div className="text-xs text-gray-400 mt-1 flex justify-between">
                                    <span className={charCount > 90 ? 'text-red-400' : ''}>
                                        {charCount}/100
                                    </span>
                                    <span>caracteres</span>
                                </div>
                                {errors.title && (
                                    <p className="text-red-400 text-xs mt-1">{errors.title}</p>
                                )}
                            </div>
                        </div>
                        
                        <div className="flex items-center space-x-3 justify-end p-4 md:p-5 border-t border-slate-600 rounded-b">
                            <button 
                                type="button"
                                onClick={onClose}
                                className="py-2.5 px-5 text-sm font-medium text-gray-300 focus:outline-none bg-transparent rounded-lg border border-slate-600 hover:bg-slate-700 hover:text-white focus:z-10 focus:ring-4 focus:ring-slate-700"
                            >
                                Cancelar
                            </button>
                            <button 
                                type="submit"
                                disabled={processing}
                                className="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center disabled:opacity-50"
                            >
                                {processing ? 'Salvando...' : 'Salvar'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}