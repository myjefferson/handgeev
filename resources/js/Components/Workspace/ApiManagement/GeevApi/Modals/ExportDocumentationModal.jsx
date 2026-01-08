// resources/js/Components/Modals/ExportDocumentationModal.jsx
import React, { useState } from 'react';
import Modal from '@/Components/Workspace/ApiManagement/GeevApi/Modals/Modal';

export default function ExportDocumentationModal({ 
    show = false, 
    onClose = () => {}, 
    workspace,
    onExport = () => {}
}) {
    const [exportFormat, setExportFormat] = useState('openapi');
    const [includeExamples, setIncludeExamples] = useState(true);
    const [includeSchemas, setIncludeSchemas] = useState(true);

    const formats = [
        { value: 'openapi', label: 'OpenAPI 3.0 (JSON)', description: 'Especificação OpenAPI padrão' },
        { value: 'postman', label: 'Postman Collection', description: 'Coleção para importar no Postman' },
        { value: 'insomnia', label: 'Insomnia', description: 'Workspace para Insomnia' },
        { value: 'markdown', label: 'Markdown', description: 'Documentação em Markdown' }
    ];

    const handleExport = () => {
        onExport({
            format: exportFormat,
            includeExamples,
            includeSchemas,
            workspaceId: workspace.id
        });
        onClose();
    };

    return (
        <Modal show={show} onClose={onClose} maxWidth="lg">
            <div className="bg-slate-800 rounded-lg p-6">
                <div className="flex justify-between items-center mb-6">
                    <h3 className="text-lg font-semibold text-white">Exportar Documentação</h3>
                    <button 
                        onClick={onClose}
                        className="text-slate-400 hover:text-white"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div className="space-y-6">
                    {/* Seleção de Formato */}
                    <div>
                        <label className="block text-sm font-medium text-slate-300 mb-3">
                            Formato de Exportação
                        </label>
                        <div className="grid grid-cols-1 gap-3">
                            {formats.map((format) => (
                                <label 
                                    key={format.value}
                                    className="flex items-center p-3 border border-slate-600 rounded-lg cursor-pointer hover:border-cyan-500/50 transition-colors"
                                >
                                    <input
                                        type="radio"
                                        name="exportFormat"
                                        value={format.value}
                                        checked={exportFormat === format.value}
                                        onChange={(e) => setExportFormat(e.target.value)}
                                        className="text-cyan-500 focus:ring-cyan-500"
                                    />
                                    <div className="ml-3">
                                        <span className="text-white text-sm font-medium">{format.label}</span>
                                        <p className="text-slate-400 text-xs mt-1">{format.description}</p>
                                    </div>
                                </label>
                            ))}
                        </div>
                    </div>

                    {/* Opções Adicionais */}
                    <div className="border-t border-slate-700 pt-4">
                        <label className="block text-sm font-medium text-slate-300 mb-3">
                            Opções de Exportação
                        </label>
                        <div className="space-y-3">
                            <label className="flex items-center">
                                <input
                                    type="checkbox"
                                    checked={includeExamples}
                                    onChange={(e) => setIncludeExamples(e.target.checked)}
                                    className="rounded border-slate-600 text-cyan-500 focus:ring-cyan-500"
                                />
                                <span className="ml-2 text-sm text-slate-300">Incluir exemplos de código</span>
                            </label>
                            <label className="flex items-center">
                                <input
                                    type="checkbox"
                                    checked={includeSchemas}
                                    onChange={(e) => setIncludeSchemas(e.target.checked)}
                                    className="rounded border-slate-600 text-cyan-500 focus:ring-cyan-500"
                                />
                                <span className="ml-2 text-sm text-slate-300">Incluir schemas JSON</span>
                            </label>
                        </div>
                    </div>

                    {/* Informações do Workspace */}
                    <div className="bg-slate-700 rounded-lg p-4">
                        <h6 className="text-sm font-medium text-slate-300 mb-2">Workspace</h6>
                        <p className="text-white text-sm">{workspace.title}</p>
                        <p className="text-slate-400 text-xs mt-1">
                            ID: {workspace.id} • {workspace.topics?.length || 0} tópicos • {workspace.fields_count || 0} campos
                        </p>
                    </div>

                    {/* Ações */}
                    <div className="flex justify-end space-x-3 pt-4 border-t border-slate-700">
                        <button 
                            onClick={onClose}
                            className="px-4 py-2 text-sm font-medium text-slate-400 bg-slate-700 rounded-lg hover:bg-slate-600 hover:text-white transition-colors"
                        >
                            Cancelar
                        </button>
                        <button 
                            onClick={handleExport}
                            className="px-4 py-2 text-sm font-medium text-white bg-cyan-600 rounded-lg hover:bg-cyan-500 transition-colors flex items-center"
                        >
                            <svg className="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Exportar Documentação
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    );
}