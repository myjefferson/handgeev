// resources/js/Components/Workspace/Modals/ExportModal.jsx
import React from 'react';
import { router } from '@inertiajs/react';

export default function ExportModal({ isOpen, onClose, topic }) {
    const handleExportJson = () => {
        if (!topic) return;
        
        // Exportar como JSON (visualização)
        fetch(`/topics/${topic.id}/export`)
            .then(response => response.json())
            .then(data => {
                alert('Tópico exportado! Verifique o console do navegador.');
                onClose();
            })
            .catch(error => {
                console.error('Erro ao exportar tópico:', error);
                alert('Erro ao exportar tópico');
            });
    };

    const handleExportDownload = () => {
        if (!topic) return;
        
        // Download do arquivo
        window.location.href = `/topics/${topic.id}/download`;
        onClose();
        alert('Download iniciado!');
    };

    if (!isOpen || !topic) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div className="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4 border border-slate-700">
                <div className="flex justify-between items-center mb-4">
                    <h3 className="text-lg font-semibold text-white">Exportar Tópico</h3>
                    <button onClick={onClose} className="text-slate-400 hover:text-white">
                        <i className="fas fa-times"></i>
                    </button>
                </div>
                
                <div className="space-y-4">
                    <p className="text-slate-400">Escolha o método de exportação para "{topic.title}"</p>
                    
                    <div className="grid grid-cols-2 gap-4">
                        <button 
                            onClick={handleExportJson}
                            className="p-4 bg-slate-700 hover:bg-slate-600 rounded-lg border border-slate-600 transition-colors group"
                        >
                            <div className="text-center">
                                <i className="fas fa-code text-green-400 text-xl mb-2"></i>
                                <p className="text-white font-medium">Exportar JSON</p>
                                <p className="text-slate-400 text-sm mt-1">Visualizar estrutura JSON</p>
                            </div>
                        </button>
                        
                        <button 
                            onClick={handleExportDownload}
                            className="p-4 bg-slate-700 hover:bg-slate-600 rounded-lg border border-slate-600 transition-colors group"
                        >
                            <div className="text-center">
                                <i className="fas fa-file-download text-blue-400 text-xl mb-2"></i>
                                <p className="text-white font-medium">Download</p>
                                <p className="text-slate-400 text-sm mt-1">Baixar arquivo JSON</p>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}