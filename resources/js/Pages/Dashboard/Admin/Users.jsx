import React, { useState, useEffect } from 'react';
import { Head, usePage, router, useForm, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';

const Users = () => {
    const { users, filters, plans, statuses, stats } = usePage().props;
    
    const [search, setSearch] = useState(filters.search || '');
    const [planFilter, setPlanFilter] = useState(filters.plan || '');
    const [statusFilter, setStatusFilter] = useState(filters.status || '');
    const [editingUser, setEditingUser] = useState(null);
    const [showEditModal, setShowEditModal] = useState(false);
    const [deletingUser, setDeletingUser] = useState(null);

    // Debounce search
    useEffect(() => {
        const timeoutId = setTimeout(() => {
            router.get(route('admin.users.index'), {
                search: search || null,
                plan: planFilter || null,
                status: statusFilter || null
            }, {
                preserveState: true,
                replace: true
            });
        }, 500);

        return () => clearTimeout(timeoutId);
    }, [search, planFilter, statusFilter]);

    // Form para edição
    const { data, setData, put, processing, errors } = useForm({
        plan_name: '',
        status: ''
    });

    // Abrir modal de edição
    const openEditModal = (user) => {
        setEditingUser(user);
        setData({
            plan_name: user.plan_name || 'free',
            status: user.status || 'active'
        });
        setShowEditModal(true);
    };

    // Fechar modal
    const closeEditModal = () => {
        setShowEditModal(false);
        setEditingUser(null);
    };

    // Salvar edição
    const handleSaveEdit = (e) => {
        e.preventDefault();
        
        put(route('admin.users.update', editingUser.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeEditModal();
            },
            onError: () => {
                alert('Erro ao atualizar usuário');
            }
        });
    };

    // Excluir usuário
    const handleDeleteUser = (user) => {
        if (confirm('Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.')) {
            router.delete(route('admin.users.delete', user.id), {
                preserveScroll: true,
                onSuccess: () => {
                    setDeletingUser(null);
                }
            });
        }
    };

    // Formatar data
    const formatDate = (dateString) => {
        if (!dateString) return 'Nunca acessou';
        
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    // Obter classe do badge de status
    const getStatusBadgeClass = (status) => {
        switch (status) {
            case 'active':
                return 'bg-green-100 text-green-800';
            case 'suspended':
                return 'bg-yellow-100 text-yellow-800';
            case 'inactive':
                return 'bg-orange-100 text-orange-800';
            default:
                return 'bg-slate-100 text-slate-800';
        }
    };

    // Obter classe do badge de plano
    const getPlanBadgeClass = (plan) => {
        switch (plan) {
            case 'admin':
                return 'bg-blue-100 text-blue-800';
            case 'pro':
                return 'bg-purple-100 text-purple-800';
            case 'premium':
                return 'bg-indigo-100 text-indigo-800';
            default:
                return 'bg-slate-100 text-slate-800';
        }
    };

    // Obter texto do status
    const getStatusText = (status) => {
        const statusMap = {
            'active': 'Ativo',
            'suspended': 'Suspenso',
            'inactive': 'Inativo',
            'past_due': 'Atrasado',
            'unpaid': 'Não pago',
            'incomplete': 'Incompleto',
            'trial': 'Teste'
        };
        return statusMap[status] || status;
    };

    return (
        <DashboardLayout>
            <Head>
                <title>Usuários</title>
                <meta name="description" content="Gerenciamento de usuários" />
            </Head>

            <div className="max-w-7xl mx-auto p-0 sm:p-0 md:p-6">
                {/* Header */}
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-white">Gerenciamento de Usuários</h1>
                    <p className="text-slate-400 mt-2">Controle as permissões e regras de acesso dos usuários</p>
                </div>
                
                {/* Barra de Pesquisa e Filtros */}
                <div className="bg-slate-800 rounded-xl p-5 border border-slate-700 mb-8">
                    <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div className="relative flex-1">
                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i className="fas fa-search text-slate-400"></i>
                            </div>
                            <input 
                                type="text" 
                                value={search}
                                onChange={(e) => setSearch(e.target.value)}
                                placeholder="Pesquisar usuários por nome, email..." 
                                className="pl-10 pr-4 py-3 w-full bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            />
                        </div>
                        
                        <div className="flex gap-3">
                            <select 
                                value={planFilter}
                                onChange={(e) => setPlanFilter(e.target.value)}
                                className="bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            >
                                <option value="">Todos os perfis</option>
                                {plans.map(plan => (
                                    <option key={plan} value={plan}>
                                        {plan.charAt(0).toUpperCase() + plan.slice(1)}
                                    </option>
                                ))}
                            </select>
                            
                            <select 
                                value={statusFilter}
                                onChange={(e) => setStatusFilter(e.target.value)}
                                className="bg-slate-700 border border-slate-600 text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            >
                                <option value="">Todos os status</option>
                                {statuses.map(status => (
                                    <option key={status} value={status}>
                                        {getStatusText(status)}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>
                </div>
                
                {/* Tabela de Usuários */}
                <div className="bg-slate-800 rounded-xl border border-slate-700 overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-slate-700">
                            <thead className="bg-slate-750">
                                <tr>
                                    <th scope="col" className="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                        Usuário
                                    </th>
                                    <th scope="col" className="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                        Perfil/Plano
                                    </th>
                                    <th scope="col" className="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" className="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                        Último Acesso
                                    </th>
                                    <th scope="col" className="px-6 py-4 text-left text-xs font-medium text-slate-300 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-slate-800 divide-y divide-slate-700">
                                {users.data.length > 0 ? (
                                    users.data.map((user) => (
                                        <tr key={user.id} className="hover:bg-slate-750 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center">
                                                    <div className="h-10 w-10 flex-shrink-0 bg-teal-500 rounded-full flex items-center justify-center">
                                                        <span className="font-medium text-slate-900">
                                                            {user.initials}
                                                        </span>
                                                    </div>
                                                    <div className="ml-4">
                                                        <Link 
                                                            href={route('admin.users.profile', user.id)} 
                                                            className="hover:text-teal-400 text-white block"
                                                        >
                                                            {user.name} {user.surname}
                                                        </Link>
                                                        <div className="text-sm text-slate-400">{user.email}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`role-badge px-2 py-1 text-xs font-medium rounded-full ${getPlanBadgeClass(user.plan_name)}`}>
                                                    {user.plan_name} 
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusBadgeClass(user.status)}`}>
                                                    {getStatusText(user.status)}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-slate-400">
                                                {formatDate(user.last_login_at)}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <Link 
                                                    href={route('admin.users.profile', user.id)} 
                                                    className="text-teal-400 hover:text-teal-300 mr-3"
                                                >
                                                    <i className="fas fa-eye"></i>
                                                </Link>
                                                <button 
                                                    onClick={() => openEditModal(user)}
                                                    className="text-teal-400 hover:text-teal-300 mr-3"
                                                >
                                                    <i className="fas fa-edit"></i>
                                                </button>
                                                <button 
                                                    onClick={() => handleDeleteUser(user)}
                                                    className="text-red-400 hover:text-red-300"
                                                    disabled={deletingUser === user.id}
                                                >
                                                    {deletingUser === user.id ? (
                                                        <i className="fas fa-spinner animate-spin"></i>
                                                    ) : (
                                                        <i className="fas fa-trash"></i>
                                                    )}
                                                </button>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-4 text-center text-slate-400">
                                            Nenhum usuário encontrado.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    
                    {/* Paginação */}
                    {users.data.length > 0 && (
                        <div className="bg-slate-750 px-6 py-4 flex items-center justify-between border-t border-slate-700">
                            <div className="flex-1 flex justify-between items-center">
                                <div>
                                    <p className="text-sm text-slate-400">
                                        Mostrando
                                        <span className="font-medium mx-1">{users.from}</span>
                                        a
                                        <span className="font-medium mx-1">{users.to}</span>
                                        de
                                        <span className="font-medium mx-1">{users.total}</span>
                                        resultados
                                    </p>
                                </div>
                                <div>
                                    <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        {/* Previous Page Link */}
                                        {users.current_page > 1 && (
                                            <button
                                                onClick={() => router.get(users.prev_page_url)}
                                                className="relative inline-flex items-center px-2 py-2 rounded-l-md border border-slate-600 bg-slate-700 text-sm font-medium text-slate-300 hover:bg-slate-600"
                                            >
                                                <span className="sr-only">Anterior</span>
                                                <i className="fas fa-chevron-left w-5 h-5"></i>
                                            </button>
                                        )}

                                        {/* Page Numbers */}
                                        {Array.from({ length: users.last_page }, (_, i) => i + 1).map(page => (
                                            <button
                                                key={page}
                                                onClick={() => router.get(users.path + '?page=' + page)}
                                                className={`relative inline-flex items-center px-4 py-2 border border-slate-600 text-sm font-medium ${
                                                    page === users.current_page
                                                        ? 'bg-slate-800 text-white'
                                                        : 'bg-slate-700 text-slate-300 hover:bg-slate-600'
                                                }`}
                                            >
                                                {page}
                                            </button>
                                        ))}

                                        {/* Next Page Link */}
                                        {users.current_page < users.last_page && (
                                            <button
                                                onClick={() => router.get(users.next_page_url)}
                                                className="relative inline-flex items-center px-2 py-2 rounded-r-md border border-slate-600 bg-slate-700 text-sm font-medium text-slate-300 hover:bg-slate-600"
                                            >
                                                <span className="sr-only">Próximo</span>
                                                <i className="fas fa-chevron-right w-5 h-5"></i>
                                            </button>
                                        )}
                                    </nav>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {/* Modal de Edição */}
            {showEditModal && editingUser && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div className="relative w-full max-w-md bg-slate-800 rounded-lg shadow border border-slate-700">
                        <div className="flex items-center justify-between p-4 border-b border-slate-700">
                            <h3 className="text-lg font-medium text-white">
                                Editar Usuário
                            </h3>
                            <button 
                                type="button" 
                                className="text-slate-400 bg-transparent hover:bg-slate-700 hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center"
                                onClick={closeEditModal}
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
                                    onClick={closeEditModal}
                                    className="text-slate-300 bg-slate-700 hover:bg-slate-600 focus:ring-4 focus:ring-slate-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
};

// Estilos CSS
const styles = `
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .role-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
    }
`;

// Adicionar estilos ao documento
const styleSheet = document.createElement("style");
styleSheet.innerText = styles;
document.head.appendChild(styleSheet);

export default Users;