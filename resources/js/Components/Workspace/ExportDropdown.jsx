// resources/js/Components/Workspace/ExportDropdown.jsx
import React, { useRef, useEffect } from 'react';
import { router } from '@inertiajs/react';

export default function ExportDropdown({ workspace, isOpen, onToggle, onClose }) {
    const dropdownRef = useRef(null);

    useEffect(() => {
        function handleClickOutside(event) {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target)) {
                onClose();
            }
        }

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, [onClose]);

    const handleCopyJson = async () => {
        try {
            const response = await fetch(`/workspace/${workspace.id}/export`);
            const data = await response.json();
            await navigator.clipboard.writeText(JSON.stringify(data, null, 2));
            alert('JSON copiado para a área de transferência!');
            onClose();
        } catch (error) {
            console.error('Erro ao copiar JSON:', error);
            alert('Erro ao copiar JSON');
        }
    };

    const handlePreviewJson = async () => {
        try {
            const response = await fetch(`/workspace/${workspace.id}/export`);
            const data = await response.json();
            
            // Abrir modal de preview (implementar se necessário)
            alert('Verifique o console do navegador para ver o JSON');
            onClose();
        } catch (error) {
            console.error('Erro ao carregar JSON:', error);
            alert('Erro ao carregar JSON');
        }
    };

    const handleExportAll = () => {
        router.get(`/workspace/${workspace.id}/export`);
        onClose();
    };

    if (!isOpen) return null;

    return (
        <div className="relative" ref={dropdownRef}>
            <button
                id="exportDropdownButton"
                onClick={onToggle}
                className="flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            >
                <i className="fas fa-download mr-2"></i>
                Exportar
                <svg className="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="m1 1 4 4 4-4"/>
                </svg>
            </button>

            <div 
                id="exportDropdown"
                className="z-10 absolute right-0 top-12 bg-slate-800 divide-y divide-slate-700 rounded-lg shadow-lg border border-slate-700 w-44 backdrop-blur-sm bg-opacity-95"
            >
                <ul className="py-2 text-sm text-slate-200">
                    <li>
                        <button
                            onClick={handleCopyJson}
                            className="export-copy-json w-full px-4 py-2 hover:bg-slate-750 transition-colors duration-200 text-left flex items-center"
                        >
                            <i className="fas fa-copy w-5 h-5 mr-2 text-blue-400"></i>
                            Copiar JSON
                        </button>
                    </li>
                    <li>
                        <button
                            onClick={handlePreviewJson}
                            className="export-preview-json w-full px-4 py-2 hover:bg-slate-750 transition-colors duration-200 text-left flex items-center"
                        >
                            <i className="fas fa-eye w-5 h-5 mr-2 text-green-400"></i>
                            Visualizar JSON
                        </button>
                    </li>
                    <li>
                        <button
                            onClick={handleExportAll}
                            className="w-full px-4 py-2 hover:bg-slate-750 transition-colors duration-200 text-left flex items-center"
                        >
                            <i className="fas fa-file-export w-5 h-5 mr-2 text-teal-400"></i>
                            Exportar Tudo
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    );
}