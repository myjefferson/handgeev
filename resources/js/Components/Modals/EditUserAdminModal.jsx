import React from 'react';
import Modal from '@/Components/Modals/Modal';

const EditUserAdminModal = ({ 
    show, 
    onClose,
    handleSaveEdit,
    editingUser,
    plans,
    errors,
    data,
    statuses,
    getStatusText,
    processing
 }) => {
    if (!show) return null;

    return (
        <Modal show={show} onClose={onClose} maxWidth="md">
            <div className="fixed inset-0 z-50 flex items-center justify-center">
                <div className="relative w-full max-w-md bg-slate-800 rounded-lg shadow border border-slate-700">
                    <div className="flex items-center justify-between p-4 border-b border-slate-700">
                        <h3 className="text-lg font-medium text-white">
                            Editar Usuário
                        </h3>
                        <button 
                            type="button" 
                            className="text-slate-400 bg-transparent hover:bg-slate-700 hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center"
                            onClick={onClose}
                        >
                            <i className="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <form onSubmit={handleSaveEdit}>
                        <div className="p-6 space-y-4">
                            <div>
                                <label className="block mb-2 text-sm font-medium text-slate-300">
                                    Nome
                                </label>
                                <input 
                                    type="text" 
                                    value={`${editingUser.name} ${editingUser.surname}`}
                                    className="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed"
                                    disabled
                                />
                            </div>
                            
                            <div>
                                <label className="block mb-2 text-sm font-medium text-slate-300">
                                    Email
                                </label>
                                <input 
                                    type="email" 
                                    value={editingUser.email}
                                    className="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed"
                                    disabled
                                />
                            </div>
                            
                            <div>
                                <label className="block mb-2 text-sm font-medium text-slate-300">
                                    Plano
                                </label>
                                <select 
                                    value={data.plan_name}
                                    onChange={(e) => setData('plan_name', e.target.value)}
                                    className="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg block w-full p-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                >
                                    {plans.map(plan => (
                                        <option key={plan} value={plan}>
                                            {plan.charAt(0).toUpperCase() + plan.slice(1)}
                                        </option>
                                    ))}
                                </select>
                                {errors.plan_name && (
                                    <p className="text-red-400 text-xs mt-1">{errors.plan_name}</p>
                                )}
                            </div>
                            
                            <div>
                                <label className="block mb-2 text-sm font-medium text-slate-300">
                                    Status
                                </label>
                                <select 
                                    value={data.status}
                                    onChange={(e) => setData('status', e.target.value)}
                                    className="bg-slate-700 border border-slate-600 text-white text-sm rounded-lg block w-full p-2.5 focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                >
                                    {statuses.map(status => (
                                        <option key={status} value={status}>
                                            {getStatusText(status)}
                                        </option>
                                    ))}
                                </select>
                                {errors.status && (
                                    <p className="text-red-400 text-xs mt-1">{errors.status}</p>
                                )}
                            </div>
                        </div>
                        
                        <div className="flex items-center p-6 space-x-3 border-t border-slate-700 rounded-b">
                            <button
                                type="submit"
                                disabled={processing}
                                className="text-white bg-teal-600 hover:bg-teal-700 focus:ring-4 focus:ring-teal-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {processing ? (
                                    <>
                                        <i className="fas fa-spinner animate-spin mr-2"></i>
                                        Salvando...
                                    </>
                                ) : (
                                    'Salvar Alterações'
                                )}
                            </button>
                            <button
                                type="button"
                                onClick={onClose}
                                className="text-slate-300 bg-slate-700 hover:bg-slate-600 focus:ring-4 focus:ring-slate-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                            >
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Modal>
    );
};

export default EditUserAdminModal;