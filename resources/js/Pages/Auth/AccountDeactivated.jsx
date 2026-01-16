import React, { useState } from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import SiteLayout from '@/Layouts/SiteLayout';

const AccountDeactivated = () => {
    const { user } = usePage().props;
    const [isReactivating, setIsRestoring] = useState(false);

    const handleRestoreAccount = async () => {
        if (confirm('Tem certeza que deseja reativar sua conta? Todos os seus dados serão restaurados.')) {
            setIsRestoring(true);
            try {
                await router.post(route('account.restore'), {
                    user_id: user.id
                });
                // Após reativação, redirecionar para dashboard
                router.visit(route('dashboard.home'));
            } catch (error) {
                console.error('Erro ao reativar conta:', error);
                alert('Ocorreu um erro ao reativar a conta. Tente novamente.');
            } finally {
                setIsRestoring(false);
            }
        }
    };

    const handleLogout = () => {
        router.post(route('logout'));
    };

    return (
        <SiteLayout>
            <Head>
                <title>Conta Desativada</title>
                <meta name="description" content="Sua conta está desativada. Você pode reativá-la ou excluí-la permanentemente." />
            </Head>

            <div className="min-h-screen bg-gradient-to-br from-slate-900 to-slate-800 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
                <div className="sm:mx-auto sm:w-full sm:max-w-md">
                    <div className="flex justify-center">
                        <img 
                            className="h-16 w-auto" 
                            src="/assets/images/logo.png" 
                            alt="Logo"
                            onError={(e) => {
                                e.target.style.display = 'none';
                                e.target.parentElement.innerHTML = 
                                    '<div class="text-3xl font-bold text-teal-400">Logo</div>';
                            }}
                        />
                    </div>
                    <h2 className="mt-6 text-center text-3xl font-extrabold text-white">
                        Conta Desativada
                    </h2>
                </div>

                <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
                    <div className="bg-slate-800/50 backdrop-blur-sm py-8 px-4 shadow-xl border border-slate-700 sm:rounded-lg sm:px-10">
                        {/* Informações da conta */}
                        <div className="mb-8 p-6 bg-slate-900/50 rounded-lg border border-slate-700">
                            <div className="flex items-center space-x-4 mb-4">
                                <div className="h-12 w-12 bg-teal-500 rounded-full flex items-center justify-center text-lg font-bold text-slate-900">
                                    {user.name ? user.name.charAt(0).toUpperCase() : 'U'}
                                </div>
                                <div>
                                    <h3 className="text-lg font-semibold text-white">{user.name}</h3>
                                    <p className="text-slate-400">{user.email}</p>
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p className="text-slate-500">Desativada em</p>
                                    <p className="text-slate-300">{new Date(user.deleted_at).toLocaleDateString('pt-BR')}</p>
                                </div>
                                <div>
                                    <p className="text-slate-500">Dias restantes</p>
                                    <p className="text-slate-300">{user.days_remaining} dias</p>
                                </div>
                            </div>
                        </div>

                        {/* Aviso */}
                        <div className="mb-8 p-6 bg-yellow-900/20 border border-yellow-700 rounded-lg">
                            <div className="flex items-start">
                                <div className="flex-shrink-0">
                                    <i className="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                                </div>
                                <div className="ml-4">
                                    <h3 className="text-lg font-medium text-yellow-200">Atenção</h3>
                                    <div className="mt-2 text-yellow-300 text-sm">
                                        <p>
                                            Sua conta foi desativada e está no período de recuperação de 30 dias. 
                                            Após esse período, a conta será excluída permanentemente.
                                        </p>
                                        <p className="mt-2">
                                            Você tem {user.days_remaining} dias restantes para reativar sua conta.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Opções */}
                        <div className="space-y-4">
                            <button
                                onClick={handleRestoreAccount}
                                disabled={isReactivating}
                                className="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 cursor-pointer"
                            >
                                {isReactivating ? (
                                    <>
                                        <i className="fas fa-spinner fa-spin mr-2"></i>
                                        Reativando...
                                    </>
                                ) : (
                                    <>
                                        <i className="fas fa-redo mr-2"></i>
                                        Reativar Minha Conta
                                    </>
                                )}
                            </button>

                            {/* <button
                                onClick={handlePermanentlyDelete}
                                disabled={isDeleting}
                                className="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                            >
                                {isDeleting ? (
                                    <>
                                        <i className="fas fa-spinner fa-spin mr-2"></i>
                                        Excluindo...
                                    </>
                                ) : (
                                    <>
                                        <i className="fas fa-trash-alt mr-2"></i>
                                        Excluir Conta Permanentemente
                                    </>
                                )}
                            </button> */}

                            <button
                                onClick={handleLogout}
                                className="w-full flex justify-center py-3 px-4 border border-slate-600 rounded-lg shadow-sm text-lg font-medium text-slate-300 hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors duration-200 cursor-pointer"
                            >
                                <i className="fas fa-sign-out-alt mr-2"></i>
                                Sair
                            </button>
                        </div>

                        {/* Informações adicionais */}
                        <div className="mt-8 text-center text-sm text-slate-500">
                            <p>
                                Ao reativar sua conta, todos os seus dados serão restaurados e você poderá acessar o sistema normalmente.
                            </p>
                            <p className="mt-2">
                                Ao excluir permanentemente, todos os dados serão removidos e essa ação não poderá ser desfeita.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </SiteLayout>
    );
};

export default AccountDeactivated;