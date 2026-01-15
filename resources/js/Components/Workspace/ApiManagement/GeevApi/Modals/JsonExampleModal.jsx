// resources/js/Components/Modals/JsonExampleModal.jsx
import React from 'react';
import Modal from '@/Components/Modals/Modal';

export default function JsonExampleModal({ 
    show = false, 
    onClose = () => {}, 
    title = "Exemplo de Resposta",
    jsonContent = "",
    onCopy = () => {}
}) {
    const copyToClipboard = async () => {
        try {
            await navigator.clipboard.writeText(jsonContent);
            onCopy();
        } catch (err) {
            console.error('Erro ao copiar:', err);
        }
    };

    return (
        <Modal show={show} onClose={onClose} maxWidth="4xl">
            <div className="bg-slate-800 rounded-lg p-6 max-h-[80vh] overflow-hidden">
                <div className="flex justify-between items-center mb-4">
                    <h3 className="text-lg font-semibold text-white">{title}</h3>
                    <button 
                        onClick={onClose}
                        className="text-slate-400 hover:text-white"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div className="bg-slate-900 rounded p-4 max-h-[60vh] overflow-y-auto">
                    <pre className="text-slate-300 text-sm font-mono whitespace-pre-wrap">
                        {jsonContent}
                    </pre>
                </div>
                <div className="mt-4 flex justify-end space-x-2">
                    <button 
                        onClick={copyToClipboard}
                        className="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded text-sm transition-colors flex items-center"
                    >
                        <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Copiar JSON
                    </button>
                    <button 
                        onClick={onClose}
                        className="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded text-sm transition-colors"
                    >
                        Fechar
                    </button>
                </div>
            </div>
        </Modal>
    );
}