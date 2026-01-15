// resources/js/Components/Modals/ConfirmModal.jsx
import React from 'react';
import Modal from '@/Components/Modals/Modal';

export default function ConfirmModal({ 
    show = false, 
    onClose = () => {}, 
    onConfirm = () => {},
    title = "Confirmação",
    message = "Tem certeza que deseja realizar esta ação?",
    confirmText = "Confirmar",
    cancelText = "Cancelar",
    type = "warning" // warning, danger, success, info
}) {
    const getIcon = () => {
        switch (type) {
            case 'danger':
                return 'fas fa-exclamation-circle text-red-400';
            case 'success':
                return 'fas fa-check-circle text-green-400';
            case 'info':
                return 'fas fa-info-circle text-blue-400';
            default:
                return 'fas fa-exclamation-triangle text-amber-400';
        }
    };

    const getConfirmButtonClass = () => {
        switch (type) {
            case 'danger':
                return 'bg-red-600 hover:bg-red-500 focus:ring-red-300';
            case 'success':
                return 'bg-green-600 hover:bg-green-500 focus:ring-green-300';
            case 'info':
                return 'bg-blue-600 hover:bg-blue-500 focus:ring-blue-300';
            default:
                return 'bg-amber-600 hover:bg-amber-500 focus:ring-amber-300';
        }
    };

    return (
        <Modal show={show} onClose={onClose} maxWidth="md">
            <div className="bg-slate-800 relative rounded-lg items-center z-10 shadow border border-slate-700">
                <div className="p-4 md:p-5 text-center">
                    <i className={`${getIcon()} text-4xl mb-4`}></i>
                    <h3 className="text-lg font-normal text-white mb-5">{message}</h3>
                    <div className="flex justify-center space-x-4">
                        <button 
                            onClick={onConfirm}
                            className={`py-2 px-4 text-sm font-medium text-white rounded-lg focus:ring-4 focus:outline-none ${getConfirmButtonClass()}`}
                        >
                            {confirmText}
                        </button>
                        <button 
                            onClick={onClose}
                            className="py-2 px-4 text-sm font-medium text-slate-400 bg-slate-700 rounded-lg hover:bg-slate-600 hover:text-white focus:ring-4 focus:outline-none focus:ring-slate-600"
                        >
                            {cancelText}
                        </button>
                    </div>
                </div>
            </div>
        </Modal>
    );
}