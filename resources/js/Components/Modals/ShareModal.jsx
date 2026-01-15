// resources/js/Components/Workspace/Modals/ShareModal.jsx
import { Link } from '@inertiajs/react';
import React, { useState } from 'react';
import Modal from '@/Components/Modals/Modal';

export default function ShareModal({ isOpen, onClose, workspace, auth }) {
    const [copied, setCopied] = useState(false);
    const shareLink = route('workspace.shared-geev-studio.show', {
        global_key_api: auth.user.global_key_api,
        workspace_key_api: workspace.workspace_key_api
    });

    const handleCopyLink = async () => {
        try {
            await navigator.clipboard.writeText(shareLink);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch (error) {
            console.error('Erro ao copiar link:', error);
        }
    };

    if (!isOpen) return null;

    return (
        <Modal show={isOpen} onClose={onClose} maxWidth="md">
            <div className="fixed inset-0 flex items-center justify-center z-50">
                <div className="bg-slate-700 rounded-lg w-11/12 max-w-2xl">
                    <div className="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 className="text-xl font-semibold text-gray-900 dark:text-white">
                            Compartilhar Workspace
                        </h3>
                        <button 
                            onClick={onClose}
                            className="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        >
                            <i className="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div className="p-4 md:p-5 space-y-4">
                        <div>
                            <label className="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Link de Compartilhamento
                            </label>
                            <div className="flex">
                                <input 
                                    type="text" 
                                    value={shareLink}
                                    readOnly
                                    className="copy-input bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white"
                                />
                                <button 
                                    onClick={handleCopyLink}
                                    className="copy-button flex-shrink-0 inline-flex items-center py-2.5 px-4 text-sm font-medium text-white bg-blue-700 rounded-r-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                                >
                                    <i className="fas fa-copy mr-1"></i>
                                    <span className="hidden md:inline">
                                        {copied ? 'Copiado!' : 'Copiar'}
                                    </span>
                                </button>
                            </div>
                            <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Este link contém suas chaves de acesso de forma segura.
                            </p>
                        </div>
                        
                        <div className="flex items-center justify-end py-4 md:py-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <Link 
                                href={shareLink}
                                rel="noopener noreferrer"
                                className="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                            >
                                <i className="fas fa-external-link-alt mr-1"></i> Abrir Link
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    );
}