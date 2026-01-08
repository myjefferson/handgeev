import React from 'react';
import { Link } from '@inertiajs/react';

const ModalDeleteAccount = ({ show, onClose, form, onSubmit, isLoading, user }) => {
    if (!show) return null;

    const hasActiveSubscription = user.has_active_subscription || user.is_on_grace_period;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
            <div className="relative p-4 w-full max-w-md">
                <div className="relative bg-slate-800 rounded-2xl shadow-lg border border-slate-700">
                    {/* Modal header */}
                    <div className="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-slate-700">
                        <h3 className="text-lg font-semibold text-white">
                            Deletar Conta
                        </h3>
                        <button
                            type="button"
                            onClick={onClose}
                            className="text-gray-400 bg-transparent hover:bg-slate-700 hover:text-white rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        >
                            <i className="fas fa-times"></i>
                            <span className="sr-only">Fechar modal</span>
                        </button>
                    </div>
                    
                    {/* Modal body */}
                    <form onSubmit={onSubmit}>
                        <div className="p-4 md:p-5">
                            <div className="mb-4">
                                <div className="flex items-center mb-4">
                                    <div className="w-10 h-10 bg-red-500/20 rounded-full flex items-center justify-center mr-3">
                                        <i className="fas fa-exclamation-triangle text-red-400"></i>
                                    </div>
                                    <div>
                                        <h4 className="text-white font-medium">Atenção!</h4>
                                        <p className="text-gray-400 text-sm">Esta ação tem consequências permanentes.</p>
                                    </div>
                                </div>
                                
                                {/* Active subscription verification */}
                                {hasActiveSubscription ? (
                                    <div className="bg-amber-500/10 border border-amber-500/20 rounded-lg p-4 mb-4">
                                        <div className="flex items-center mb-3">
                                            <i className="fas fa-exclamation-circle text-amber-400 mr-2 text-lg"></i>
                                            <span className="text-amber-400 font-medium text-lg">Assinatura Ativa Detectada</span>
                                        </div>
                                        
                                        <div className="space-y-3">
                                            <p className="text-amber-300 text-sm">
                                                <strong>Você possui uma assinatura ativa ou em período de cortesia.</strong>
                                            </p>
                                            
                                            <div className="bg-slate-700/50 rounded-lg p-3 border border-slate-600">
                                                <p className="text-amber-200 text-sm mb-3">
                                                    Para deletar sua conta, você precisa primeiro cancelar sua assinatura e aguardar a expiração do plano.
                                                </p>
                                                
                                                <div className="text-xs text-amber-300 space-y-1">
                                                    <div className="flex items-start">
                                                        <i className="fas fa-chevron-right text-amber-400 mt-1 mr-2 text-xs"></i>
                                                        <span>Cancelar assinatura</span>
                                                    </div>
                                                    <div className="flex items-start">
                                                        <i className="fas fa-chevron-right text-amber-400 mt-1 mr-2 text-xs"></i>
                                                        <span>Aguardar expiração do plano</span>
                                                    </div>
                                                    <div className="flex items-start">
                                                        <i className="fas fa-chevron-right text-amber-400 mt-1 mr-2 text-xs"></i>
                                                        <span>Deletar conta</span>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Subscription management buttons */}
                                            <div className="flex flex-col space-y-2 mt-4">
                                                <Link
                                                    href={route('billing.show')}
                                                    className="w-full bg-amber-600 hover:bg-amber-500 text-white font-medium py-2 px-4 rounded-xl transition-colors duration-200 flex items-center justify-center"
                                                >
                                                    <i className="fas fa-cog mr-2"></i>
                                                    Gerenciar Minha Assinatura
                                                </Link>
                                                
                                                <Link
                                                    href={route('subscription.pricing')}
                                                    className="w-full bg-slate-700 hover:bg-slate-600 text-white font-medium py-2 px-4 rounded-xl transition-colors duration-200 border border-slate-600 flex items-center justify-center"
                                                >
                                                    <i className="fas fa-credit-card mr-2"></i>
                                                    Ver Planos de Assinatura
                                                </Link>
                                            </div>

                                            <div className="text-xs text-amber-400 mt-3 p-2 bg-amber-500/5 rounded-lg border border-amber-500/10">
                                                <i className="fas fa-info-circle mr-1"></i>
                                                Após o cancelamento e expiração do plano, você poderá deletar sua conta permanentemente.
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <p className="text-gray-300 text-sm mb-4">
                                        Para confirmar que você realmente deseja deletar sua conta, por favor digite sua senha abaixo.
                                        Sua conta será marcada para exclusão e removida permanentemente após 30 dias.
                                    </p>
                                )}

                                {!hasActiveSubscription && (
                                    <div className="space-y-3">
                                        <div>
                                            <label htmlFor="delete_password" className="block text-sm font-medium text-gray-400 mb-2">
                                                Sua Senha
                                            </label>
                                            <input
                                                type="password"
                                                id="delete_password"
                                                value={form.data.password}
                                                onChange={(e) => form.setData('password', e.target.value)}
                                                required
                                                className="w-full bg-slate-700 border border-slate-600 rounded-xl py-3 px-4 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent transition-colors"
                                                placeholder="Digite sua senha atual"
                                            />
                                            {form.errors.password && (
                                                <span className="text-red-400 text-sm mt-1">{form.errors.password}</span>
                                            )}
                                        </div>

                                        <div className="flex items-start p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                                            <div className="flex-shrink-0 mt-0.5">
                                                <i className="fas fa-info-circle text-red-400"></i>
                                            </div>
                                            <div className="ml-3">
                                                <p className="text-red-400 text-sm">
                                                    <strong>Você perderá permanentemente:</strong>
                                                </p>
                                                <ul className="text-red-300 text-sm mt-1 space-y-1">
                                                    <li className="flex items-center">
                                                        <i className="fas fa-times mr-2 text-xs"></i>
                                                        Todas as suas workspaces
                                                    </li>
                                                    <li className="flex items-center">
                                                        <i className="fas fa-times mr-2 text-xs"></i>
                                                        Todos os tópicos e campos
                                                    </li>
                                                    <li className="flex items-center">
                                                        <i className="fas fa-times mr-2 text-xs"></i>
                                                        Histórico de atividades
                                                    </li>
                                                    <li className="flex items-center">
                                                        <i className="fas fa-times mr-2 text-xs"></i>
                                                        Configurações pessoais
                                                    </li>
                                                </ul>
                                                <div className="mt-2 pt-2 border-t border-red-500/20">
                                                    <p className="text-amber-300 text-sm">
                                                        <strong>Período de recuperação:</strong> 30 dias
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                            
                            <div className="flex flex-col sm:flex-row gap-3">
                                {!hasActiveSubscription && (
                                    <button
                                        type="submit"
                                        disabled={isLoading}
                                        className="flex-1 bg-red-600 hover:bg-red-500 text-white font-medium py-3 px-4 rounded-xl transition-colors duration-200 flex items-center justify-center disabled:opacity-50"
                                    >
                                        <i className="fas fa-trash mr-2"></i>
                                        {isLoading ? 'Processando...' : 'Confirmar Deleção'}
                                    </button>
                                )}
                                <button
                                    type="button"
                                    onClick={onClose}
                                    className="flex-1 bg-slate-700 hover:bg-slate-600 text-white font-medium py-3 px-4 rounded-xl transition-colors duration-200 border border-slate-600 flex items-center justify-center"
                                >
                                    <i className="fas fa-times mr-2"></i>
                                    {hasActiveSubscription ? 'Entendido' : 'Cancelar'}
                                </button>
                            </div>

                            {/* Additional info for users with subscription */}
                            {hasActiveSubscription && (
                                <div className="mt-4 text-center">
                                    <p className="text-xs text-gray-400">
                                        Precisa de ajuda? 
                                        <Link href={route('help.center')} className="text-cyan-400 hover:text-cyan-300 underline ml-1">
                                            Entre em contato com o suporte
                                        </Link>
                                    </p>
                                </div>
                            )}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
};

export default ModalDeleteAccount;