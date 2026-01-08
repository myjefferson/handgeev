// resources/js/Components/Workspace/Settings/Modals/DeleteModal.jsx
import React from 'react';
import { useForm } from '@inertiajs/react';

export default function DeleteModal({ isOpen, onClose, workspace }) {
    const { delete: destroy, processing } = useForm();

    const handleDelete = () => {
        destroy(route('workspace.destroy', workspace.id), {
            preserveScroll: true,
            onSuccess: () => {
                onClose();
                // O Inertia vai redirecionar para a lista de workspaces
            }
        });
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="relative p-4 w-full max-w-md max-h-full">
                <div className="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <div className="p-4 md:p-5 text-center">
                        <svg 
                            className="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" 
                            aria-hidden="true" 
                            xmlns="http://www.w3.org/2000/svg" 
                            fill="none" 
                            viewBox="0 0 20 20"
                        >
                            <path 
                                stroke="currentColor" 
                                strokeLinecap="round" 
                                strokeLinejoin="round" 
                                strokeWidth="2" 
                                d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                            />
                        </svg>
                        <h3 className="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">
                            Tem certeza que deseja remover o workspace "<strong>{workspace.title}</strong>"?
                        </h3>
                        <div className="flex justify-center space-x-3">
                            <button
                                type="button"
                                onClick={handleDelete}
                                disabled={processing}
                                className="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center disabled:opacity-50"
                            >
                                {processing ? (
                                    <>
                                        <i className="fas fa-spinner fa-spin mr-2"></i>
                                        Removendo...
                                    </>
                                ) : (
                                    'Sim, remover'
                                )}
                            </button>
                            <button
                                type="button"
                                onClick={onClose}
                                disabled={processing}
                                className="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700"
                            >
                                Não, cancelar
                            </button>
                        </div>
                        <div className="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                            <p className="text-sm text-yellow-800 dark:text-yellow-300">
                                <i className="fas fa-exclamation-triangle mr-1"></i>
                                Esta ação não pode ser desfeita. Todos os tópicos e campos serão permanentemente removidos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}