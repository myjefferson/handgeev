import React from 'react';
import Modal from '@/Components/Modals/Modal';

const ModalChangeEmail = ({ show, onClose, form, onSubmit, isLoading, user }) => {
    if (!show) return null;

    return (
        <Modal show={show} onClose={onClose} maxWidth="md">
            <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div className="relative w-full max-w-md">
                    <div className="relative bg-slate-800 rounded-2xl shadow-lg border border-slate-700">
                        {/* Modal header */}
                        <div className="flex items-center justify-between p-6 border-b border-slate-700">
                            <h3 className="text-xl font-semibold text-white">
                                <i className="fas fa-envelope mr-2 text-teal-400"></i>
                                Alterar E-mail
                            </h3>
                            <button
                                type="button"
                                onClick={onClose}
                                className="text-slate-400 bg-transparent hover:bg-slate-700 hover:text-white rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                            >
                                <i className="fas fa-times"></i>
                                <span className="sr-only">Fechar modal</span>
                            </button>
                        </div>
                        
                        {/* Modal body */}
                        <div className="p-6 space-y-4">
                            {/* Warning if email not confirmed */}
                            {!user.email_verified_at && (
                                <div className="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4">
                                    <div className="flex items-center text-amber-300 text-sm">
                                        <i className="fas fa-exclamation-triangle mr-2"></i>
                                        <span>Seu email atual não está confirmado. Recomendamos confirmá-lo antes de alterar.</span>
                                    </div>
                                </div>
                            )}
                            
                            <p className="text-slate-300 text-sm">
                                Para alterar seu e-mail, digite o novo endereço abaixo. Enviaremos um link de confirmação para o novo email.
                            </p>
                            
                            <form id="email-change-form" onSubmit={onSubmit}>
                                <div className="mb-4">
                                    <label htmlFor="new_email" className="block text-sm font-medium text-gray-400 mb-2">
                                        Novo E-mail
                                    </label>
                                    <input
                                        type="email"
                                        id="new_email"
                                        value={form.data.email}
                                        onChange={(e) => form.setData('email', e.target.value)}
                                        className={`w-full bg-slate-700 border ${
                                            form.errors.email ? 'border-red-500' : 'border-slate-600'
                                        } rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors`}
                                        placeholder="seu.novo.email@exemplo.com"
                                        required
                                    />
                                    {form.errors.email && (
                                        <span className="text-red-400 text-sm mt-1">{form.errors.email}</span>
                                    )}
                                </div>
                                
                                <div className="mb-4">
                                    <label htmlFor="current_password" className="block text-sm font-medium text-gray-400 mb-2">
                                        Senha Atual
                                    </label>
                                    <input
                                        type="password"
                                        id="current_password"
                                        value={form.data.current_password}
                                        onChange={(e) => form.setData('current_password', e.target.value)}
                                        className={`w-full bg-slate-700 border ${
                                            form.errors.current_password ? 'border-red-500' : 'border-slate-600'
                                        } rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-400 focus:border-transparent transition-colors`}
                                        placeholder="Digite sua senha atual"
                                        required
                                    />
                                    {form.errors.current_password && (
                                        <span className="text-red-400 text-sm mt-1">{form.errors.current_password}</span>
                                    )}
                                </div>
                            </form>
                        </div>
                        
                        {/* Modal footer */}
                        <div className="flex items-center justify-end p-6 space-x-3 border-t border-slate-700">
                            <button
                                type="button"
                                onClick={onClose}
                                className="px-4 py-2 text-sm font-medium text-slate-300 bg-slate-700 hover:bg-slate-600 rounded-xl transition-colors duration-200"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                form="email-change-form"
                                disabled={isLoading}
                                className="px-4 py-2 text-sm font-medium text-white bg-teal-600 hover:bg-teal-500 rounded-xl transition-colors duration-200 flex items-center space-x-2 disabled:opacity-50"
                            >
                                <i className="fas fa-paper-plane"></i>
                                <span>{isLoading ? 'Enviando...' : 'Enviar Link'}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    );
};

export default ModalChangeEmail;