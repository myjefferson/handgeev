import React, { useState } from 'react';
import { Head, usePage, router, useForm, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';

const UserProfile = () => {
    const { 
        user, 
        stats, 
        recentWorkspaces, 
        activities, 
        planLimits, 
        currentUsage, 
        workspaces 
    } = usePage().props;

    const [activeTab, setActiveTab] = useState('overview');
    const [showPasswordModal, setShowPasswordModal] = useState(false);
    const [newPassword, setNewPassword] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    // Form para informações básicas
    const { data: basicInfoData, setData: setBasicInfoData, put: updateBasicInfo, processing: basicInfoProcessing, errors: basicInfoErrors } = useForm({
        name: user.name,
        surname: user.surname,
        email: user.email,
        phone: user.phone || ''
    });

    // Form para plano e status
    const { data: settingsData, setData: setSettingsData, put: updateSettings, processing: settingsProcessing } = useForm({
        plan_name: user.plan_name,
        status: user.status
    });

    // Funções auxiliares
    const formatDate = (dateString) => {
        if (!dateString) return 'Nunca';
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    const getStatusBadgeClass = (status) => {
        switch (status) {
            case 'active': return 'bg-green-500/20 text-green-400 border border-green-500/30';
            case 'suspended': return 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30';
            case 'inactive': return 'bg-orange-500/20 text-orange-400 border border-orange-500/30';
            default: return 'bg-slate-500/20 text-slate-400 border border-slate-500/30';
        }
    };

    const getStatusText = (status) => {
        const statusMap = {
            'active': 'Ativo',
            'suspended': 'Suspenso',
            'inactive': 'Inativo',
            'past_due': 'Pagamento Pendente',
            'unpaid': 'Não Pago',
            'incomplete': 'Incompleto',
            'trial': 'Trial'
        };
        return statusMap[status] || status;
    };

    const getResponseCodeClass = (code) => {
        if (code >= 200 && code < 300) return 'bg-green-500/20 text-green-400';
        if (code >= 400) return 'bg-red-500/20 text-red-400';
        return 'bg-yellow-500/20 text-yellow-400';
    };

    // Ações
    const handleResetPassword = async () => {
        if (!confirm('Tem certeza que deseja resetar a senha deste usuário?')) return;

        setIsLoading(true);
        try {
            const response = await fetch(route('admin.users.reset-password', user.id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();
            
            if (data.success) {
                setNewPassword(data.new_password);
                setShowPasswordModal(true);
            } else {
                alert('Erro: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erro ao resetar senha');
        } finally {
            setIsLoading(false);
        }
    };

    const handleToggleStatus = async (action) => {
        setIsLoading(true);
        try {
            const response = await fetch(route('admin.users.toggle-status', user.id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ action })
            });

            const data = await response.json();
            
            if (data.success) {
                alert(data.message);
                router.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Erro ao alterar status');
        } finally {
            setIsLoading(false);
        }
    };

    const handleUpdateBasicInfo = (e) => {
        e.preventDefault();
        updateBasicInfo(route('admin.users.update-profile', user.id), {
            preserveScroll: true,
            onSuccess: () => {
                alert('Informações atualizadas com sucesso!');
                router.reload();
            },
            onError: () => {
                alert('Erro ao atualizar informações');
            }
        });
    };

    const handleUpdatePlan = () => {
        updateSettings(route('admin.users.update-profile', user.id), {
            preserveScroll: true,
            onSuccess: () => {
                alert('Plano atualizado com sucesso!');
                router.reload();
            },
            onError: () => {
                alert('Erro ao atualizar plano');
            }
        });
    };

    const handleUpdateStatus = () => {
        updateSettings(route('admin.users.update-profile', user.id), {
            preserveScroll: true,
            onSuccess: () => {
                alert('Status atualizado com sucesso!');
                router.reload();
            },
            onError: () => {
                alert('Erro ao atualizar status');
            }
        });
    };

    const calculateProgressWidth = (current, max) => {
        if (max === 0) return 100;
        return Math.min(100, (current / max) * 100);
    };

    return (
        <DashboardLayout>
            <Head>
                <title>Perfil do Usuário</title>
                <meta name="description" content="Detalhes e gestão do usuário" />
            </Head>

            <div className="max-w-7xl mx-auto p-0 sm:p-0 md:p-6">
                {/* Header do Perfil */}
                <Link 
                    href={route('admin.users')}
                    className="block w-max text-sm text-gray-300 hover:text-teal-400 transition-colors mb-8"
                >
                    <i className="fas fa-arrow-left mr-1"></i> Voltar
                </Link>
                
                <div className="profile-header rounded-xl p-6 mb-8">
                    <div className="flex flex-col md:flex-row md:items-center justify-between">
                        <div className="flex items-center space-x-4 mb-4 md:mb-0">
                            <div className="h-20 w-20 bg-teal-500 rounded-full flex items-center justify-center text-2xl font-bold text-slate-900">
                                {user.initials}
                            </div>
                            <div>
                                <h1 className="text-2xl font-bold text-white">{user.name} {user.surname}</h1>
                                <p className="text-slate-400">{user.email}</p>
                                <div className="flex items-center space-x-2 mt-2">
                                    <span className={`badge-status ${getStatusBadgeClass(user.status)}`}>
                                        {getStatusText(user.status)}
                                    </span>
                                    <span className="badge-status bg-blue-500/20 text-blue-400 border border-blue-500/30">
                                        {user.plan_name ? user.plan_name.charAt(0).toUpperCase() + user.plan_name.slice(1) : 'Free'}
                                    </span>
                                    {user.email_verified_at ? (
                                        <span className="badge-status bg-green-500/20 text-green-400 border border-green-500/30">
                                            Email Verificado
                                        </span>
                                    ) : (
                                        <span className="badge-status bg-red-500/20 text-red-400 border border-red-500/30">
                                            Email Não Verificado
                                        </span>
                                    )}
                                </div>
                            </div>
                        </div>
                        <div className="flex space-x-3">
                            <button 
                                onClick={handleResetPassword}
                                disabled={isLoading}
                                className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50"
                            >
                                {isLoading ? (
                                    <i className="fas fa-spinner animate-spin mr-2"></i>
                                ) : (
                                    <i className="fas fa-key mr-2"></i>
                                )}
                                Resetar Senha
                            </button>
                            {user.status === 'active' ? (
                                <button 
                                    onClick={() => handleToggleStatus('suspend')}
                                    disabled={isLoading}
                                    className="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors disabled:opacity-50"
                                >
                                    <i className="fas fa-pause mr-2"></i>Suspender
                                </button>
                            ) : (
                                <button 
                                    onClick={() => handleToggleStatus('activate')}
                                    disabled={isLoading}
                                    className="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors disabled:opacity-50"
                                >
                                    <i className="fas fa-play mr-2"></i>Ativar
                                </button>
                            )}
                        </div>
                    </div>
                </div>

                {/* Navegação por Tabs */}
                <div className="bg-slate-800 rounded-xl border border-slate-700 mb-8">
                    <div className="border-b border-slate-700">
                        <nav className="flex flex-col sm:flex-row space-x-8 px-6" aria-label="Tabs">
                            {[
                                { id: 'overview', icon: 'chart-bar', label: 'Visão Geral' },
                                { id: 'activity', icon: 'history', label: 'Atividades' },
                                { id: 'workspaces', icon: 'folder', label: 'Workspaces' },
                                { id: 'settings', icon: 'cog', label: 'Configurações' }
                            ].map(tab => (
                                <button 
                                    key={tab.id}
                                    onClick={() => setActiveTab(tab.id)}
                                    className={`tab-button py-4 px-1 border-b-2 font-medium text-sm transition-colors ${
                                        activeTab === tab.id 
                                            ? 'border-blue-500 text-white' 
                                            : 'border-transparent text-slate-400 hover:text-slate-300'
                                    }`}
                                >
                                    <i className={`fas fa-${tab.icon} mr-2`}></i>{tab.label}
                                </button>
                            ))}
                        </nav>
                    </div>

                    {/* Conteúdo das Tabs */}
                    <div className="p-6">
                        {/* Tab: Visão Geral */}
                        {activeTab === 'overview' && (
                            <div className="space-y-8">
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    {/* Workspaces Card */}
                                    <div className="stat-card rounded-lg p-6">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <p className="text-slate-400 text-sm">Workspaces</p>
                                                <p className="text-2xl font-bold text-white">{stats.workspaces_count}</p>
                                            </div>
                                            <div className="p-3 bg-blue-500/20 rounded-lg">
                                                <i className="fas fa-folder text-blue-400"></i>
                                            </div>
                                        </div>
                                        <div className="mt-4">
                                            <div className="flex justify-between text-sm text-slate-400 mb-1">
                                                <span>Uso</span>
                                                <span>{currentUsage.workspaces}/{planLimits.workspaces === 0 ? '∞' : planLimits.workspaces}</span>
                                            </div>
                                            <div className="plan-progress bg-slate-700">
                                                <div 
                                                    className="bg-blue-500 h-full rounded-full" 
                                                    style={{ width: `${calculateProgressWidth(currentUsage.workspaces, planLimits.workspaces)}%` }}
                                                ></div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Tópicos Card */}
                                    <div className="stat-card rounded-lg p-6">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <p className="text-slate-400 text-sm">Tópicos</p>
                                                <p className="text-2xl font-bold text-white">{stats.topics_count}</p>
                                            </div>
                                            <div className="p-3 bg-green-500/20 rounded-lg">
                                                <i className="fas fa-file-alt text-green-400"></i>
                                            </div>
                                        </div>
                                        <div className="mt-4">
                                            <div className="flex justify-between text-sm text-slate-400 mb-1">
                                                <span>Uso</span>
                                                <span>{currentUsage.topics}/{planLimits.topics === 0 ? '∞' : planLimits.topics}</span>
                                            </div>
                                            <div className="plan-progress bg-slate-700">
                                                <div 
                                                    className="bg-green-500 h-full rounded-full" 
                                                    style={{ width: `${calculateProgressWidth(currentUsage.topics, planLimits.topics)}%` }}
                                                ></div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Campos Card */}
                                    <div className="stat-card rounded-lg p-6">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <p className="text-slate-400 text-sm">Campos</p>
                                                <p className="text-2xl font-bold text-white">{stats.fields_count}</p>
                                            </div>
                                            <div className="p-3 bg-purple-500/20 rounded-lg">
                                                <i className="fas fa-tags text-purple-400"></i>
                                            </div>
                                        </div>
                                        <div className="mt-4">
                                            <div className="flex justify-between text-sm text-slate-400 mb-1">
                                                <span>Uso</span>
                                                <span>{currentUsage.fields}/{planLimits.max_fields === 0 ? '∞' : planLimits.max_fields}</span>
                                            </div>
                                            <div className="plan-progress bg-slate-700">
                                                <div 
                                                    className="bg-purple-500 h-full rounded-full" 
                                                    style={{ width: `${calculateProgressWidth(currentUsage.fields, planLimits.max_fields)}%` }}
                                                ></div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Colaborações Card */}
                                    <div className="stat-card rounded-lg p-6">
                                        <div className="flex items-center justify-between">
                                            <div>
                                                <p className="text-slate-400 text-sm">Colaborações</p>
                                                <p className="text-2xl font-bold text-white">{stats.collaborations_count}</p>
                                            </div>
                                            <div className="p-3 bg-orange-500/20 rounded-lg">
                                                <i className="fas fa-users text-orange-400"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Informações do Plano e Workspaces Recentes */}
                                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <div className="stat-card rounded-lg p-6">
                                        <h3 className="text-lg font-semibold text-white mb-4">Informações do Plano</h3>
                                        <div className="space-y-3">
                                            <div className="flex justify-between">
                                                <span className="text-slate-400">Plano Atual:</span>
                                                <span className="text-white font-medium">{user.plan_name ? user.plan_name.charAt(0).toUpperCase() + user.plan_name.slice(1) : 'Free'}</span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-slate-400">Exportação:</span>
                                                <span className="text-white font-medium">
                                                    {planLimits.can_export ? (
                                                        <><i className="fas fa-check text-green-400"></i> Permitido</>
                                                    ) : (
                                                        <><i className="fas fa-times text-red-400"></i> Não Permitido</>
                                                    )}
                                                </span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-slate-400">API:</span>
                                                <span className="text-white font-medium">
                                                    {planLimits.can_use_api ? (
                                                        <><i className="fas fa-check text-green-400"></i> Permitido</>
                                                    ) : (
                                                        <><i className="fas fa-times text-red-400"></i> Não Permitido</>
                                                    )}
                                                </span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-slate-400">Membro desde:</span>
                                                <span className="text-white font-medium">
                                                    {new Date(user.created_at).toLocaleDateString('pt-BR')}
                                                </span>
                                            </div>
                                            <div className="flex justify-between">
                                                <span className="text-slate-400">Último login:</span>
                                                <span className="text-white font-medium">
                                                    {formatDate(user.last_login_at)}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Workspaces Recentes */}
                                    <div className="stat-card rounded-lg p-6">
                                        <h3 className="text-lg font-semibold text-white mb-4">Workspaces Recentes</h3>
                                        <div className="space-y-3">
                                            {recentWorkspaces.length > 0 ? (
                                                recentWorkspaces.map(workspace => (
                                                    <div key={workspace.id} className="workspace-item rounded-lg p-4">
                                                        <div className="flex justify-between items-start">
                                                            <div>
                                                                <h4 className="font-medium text-white">{workspace.name}</h4>
                                                                <p className="text-slate-400 text-sm mt-1">
                                                                    {workspace.topics_count} tópicos • 
                                                                    {workspace.fields_count} campos
                                                                </p>
                                                            </div>
                                                            <span className="text-xs text-slate-400">
                                                                {new Date(workspace.updated_at).toLocaleDateString('pt-BR')}
                                                            </span>
                                                        </div>
                                                    </div>
                                                ))
                                            ) : (
                                                <p className="text-slate-400 text-center py-4">Nenhum workspace encontrado</p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Tab: Atividades */}
                        {activeTab === 'activity' && (
                            <div>
                                <h3 className="text-lg font-semibold text-white mb-4">Histórico de Atividades</h3>
                                <div className="space-y-3">
                                    {activities.length > 0 ? (
                                        activities.map((activity, index) => (
                                            <div key={index} className="activity-item rounded-lg p-4 bg-slate-800/50">
                                                <div className="flex justify-between items-start">
                                                    <div>
                                                        <div className="flex items-center space-x-2">
                                                            <span className="text-sm font-medium text-white">
                                                                {activity.method} {activity.endpoint}
                                                            </span>
                                                            <span className={`text-xs px-2 py-1 rounded-full ${getResponseCodeClass(activity.response_code)}`}>
                                                                {activity.response_code}
                                                            </span>
                                                        </div>
                                                        <p className="text-slate-400 text-sm mt-1">
                                                            Tempo de resposta: {activity.response_time}ms
                                                        </p>
                                                    </div>
                                                    <span className="text-xs text-slate-400">
                                                        {formatDate(activity.created_at)}
                                                    </span>
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <p className="text-slate-400 text-center py-8">Nenhuma atividade registrada</p>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Tab: Workspaces */}
                        {activeTab === 'workspaces' && (
                            <div>
                                <div className="flex justify-between items-center mb-6">
                                    <h3 className="text-lg font-semibold text-white">Workspaces do Usuário</h3>
                                    <button 
                                        onClick={() => alert('Funcionalidade de inativar todos os workspaces será implementada aqui')}
                                        className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors"
                                    >
                                        <i className="fas fa-ban mr-2"></i>Inativar Todos
                                    </button>
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    {workspaces.length > 0 ? (
                                        workspaces.map(workspace => (
                                            <div key={workspace.id} className="workspace-item rounded-lg p-6">
                                                <div className="flex justify-between items-start mb-4">
                                                    <h4 className="font-medium text-white">{workspace.name}</h4>
                                                    <span className={`text-xs px-2 py-1 rounded-full ${
                                                        workspace.is_active 
                                                            ? 'bg-green-500/20 text-green-400' 
                                                            : 'bg-red-500/20 text-red-400'
                                                    }`}>
                                                        {workspace.is_active ? 'Ativo' : 'Inativo'}
                                                    </span>
                                                </div>
                                                <div className="space-y-2 text-sm text-slate-400">
                                                    <div className="flex justify-between">
                                                        <span>Tópicos:</span>
                                                        <span className="text-white">{workspace.topics_count}</span>
                                                    </div>
                                                    <div className="flex justify-between">
                                                        <span>Campos:</span>
                                                        <span className="text-white">{workspace.fields_count}</span>
                                                    </div>
                                                    <div className="flex justify-between">
                                                        <span>Criado em:</span>
                                                        <span className="text-white">
                                                            {new Date(workspace.created_at).toLocaleDateString('pt-BR')}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div className="mt-4 flex space-x-2">
                                                    <button 
                                                        onClick={() => alert('Funcionalidade de toggle workspace será implementada aqui')}
                                                        className={`flex-1 px-3 py-2 text-sm text-white rounded transition-colors ${
                                                            workspace.is_active 
                                                                ? 'bg-yellow-600 hover:bg-yellow-700' 
                                                                : 'bg-green-600 hover:bg-green-700'
                                                        }`}
                                                    >
                                                        {workspace.is_active ? 'Inativar' : 'Ativar'}
                                                    </button>
                                                </div>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="col-span-full">
                                            <p className="text-slate-400 text-center py-8">Nenhum workspace encontrado</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Tab: Configurações */}
                        {activeTab === 'settings' && (
                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                {/* Informações Básicas */}
                                <div className="stat-card rounded-lg p-6">
                                    <h3 className="text-lg font-semibold text-white mb-4">Informações Básicas</h3>
                                    <form onSubmit={handleUpdateBasicInfo}>
                                        <div className="space-y-4">
                                            <div>
                                                <label className="block text-sm font-medium text-slate-400 mb-2">Nome</label>
                                                <input 
                                                    type="text" 
                                                    value={basicInfoData.name}
                                                    onChange={e => setBasicInfoData('name', e.target.value)}
                                                    className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                />
                                                {basicInfoErrors.name && (
                                                    <p className="text-red-400 text-xs mt-1">{basicInfoErrors.name}</p>
                                                )}
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-slate-400 mb-2">Sobrenome</label>
                                                <input 
                                                    type="text" 
                                                    value={basicInfoData.surname}
                                                    onChange={e => setBasicInfoData('surname', e.target.value)}
                                                    className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                />
                                                {basicInfoErrors.surname && (
                                                    <p className="text-red-400 text-xs mt-1">{basicInfoErrors.surname}</p>
                                                )}
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-slate-400 mb-2">Email</label>
                                                <input 
                                                    type="email" 
                                                    value={basicInfoData.email}
                                                    onChange={e => setBasicInfoData('email', e.target.value)}
                                                    className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                />
                                                {basicInfoErrors.email && (
                                                    <p className="text-red-400 text-xs mt-1">{basicInfoErrors.email}</p>
                                                )}
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-slate-400 mb-2">Telefone</label>
                                                <input 
                                                    type="text" 
                                                    value={basicInfoData.phone}
                                                    onChange={e => setBasicInfoData('phone', e.target.value)}
                                                    className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                />
                                                {basicInfoErrors.phone && (
                                                    <p className="text-red-400 text-xs mt-1">{basicInfoErrors.phone}</p>
                                                )}
                                            </div>
                                            <button 
                                                type="submit"
                                                disabled={basicInfoProcessing}
                                                className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors disabled:opacity-50"
                                            >
                                                {basicInfoProcessing ? (
                                                    <>
                                                        <i className="fas fa-spinner animate-spin mr-2"></i>
                                                        Atualizando...
                                                    </>
                                                ) : (
                                                    'Atualizar Informações'
                                                )}
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {/* Gestão de Plano e Status */}
                                <div className="space-y-6">
                                    {/* Alterar Plano */}
                                    <div className="stat-card rounded-lg p-6">
                                        <h3 className="text-lg font-semibold text-white mb-4">Alterar Plano</h3>
                                        <div className="space-y-3">
                                            <select 
                                                value={settingsData.plan_name}
                                                onChange={e => setSettingsData('plan_name', e.target.value)}
                                                className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                            >
                                                <option value="free">Free</option>
                                                <option value="start">Start</option>
                                                <option value="pro">Pro</option>
                                                <option value="premium">Premium</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                            <button 
                                                onClick={handleUpdatePlan}
                                                disabled={settingsProcessing}
                                                className="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg transition-colors disabled:opacity-50"
                                            >
                                                {settingsProcessing ? (
                                                    <>
                                                        <i className="fas fa-spinner animate-spin mr-2"></i>
                                                        Alterando...
                                                    </>
                                                ) : (
                                                    'Alterar Plano'
                                                )}
                                            </button>
                                        </div>
                                    </div>

                                    {/* Alterar Status */}
                                    <div className="stat-card rounded-lg p-6">
                                        <h3 className="text-lg font-semibold text-white mb-4">Alterar Status</h3>
                                        <div className="space-y-3">
                                            <select 
                                                value={settingsData.status}
                                                onChange={e => setSettingsData('status', e.target.value)}
                                                className="w-full bg-slate-700 border border-slate-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                                            >
                                                <option value="active">Ativo</option>
                                                <option value="inactive">Inativo</option>
                                                <option value="suspended">Suspenso</option>
                                                <option value="past_due">Pagamento Pendente</option>
                                                <option value="unpaid">Não Pago</option>
                                                <option value="incomplete">Incompleto</option>
                                                <option value="trial">Trial</option>
                                            </select>
                                            <button 
                                                onClick={handleUpdateStatus}
                                                disabled={settingsProcessing}
                                                className="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg transition-colors disabled:opacity-50"
                                            >
                                                {settingsProcessing ? (
                                                    <>
                                                        <i className="fas fa-spinner animate-spin mr-2"></i>
                                                        Alterando...
                                                    </>
                                                ) : (
                                                    'Alterar Status'
                                                )}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Modal de Senha Resetada */}
            {showPasswordModal && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div className="bg-slate-800 rounded-xl p-6 max-w-md w-full mx-4">
                        <h3 className="text-lg font-semibold text-white mb-4">Senha Resetada</h3>
                        <p className="text-slate-400 mb-4">A nova senha do usuário é:</p>
                        <div className="bg-slate-700 rounded-lg p-4 mb-4">
                            <code className="text-white font-mono text-lg">{newPassword}</code>
                        </div>
                        <p className="text-sm text-slate-400 mb-4">
                            Esta senha será mostrada apenas uma vez. Salve-a em um local seguro.
                        </p>
                        <button 
                            onClick={() => setShowPasswordModal(false)}
                            className="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors"
                        >
                            Fechar
                        </button>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
};

// Estilos CSS
const styles = `
    .profile-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-bottom: 1px solid #334155;
    }
    
    .stat-card {
        background: #1e293b;
        border: 1px solid #334155;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
    }
    
    .activity-item {
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .activity-item:hover {
        border-left-color: #3b82f6;
        background: #1e293b;
    }
    
    .badge-status {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
    }
    
    .plan-progress {
        height: 8px;
        border-radius: 4px;
        overflow: hidden;
    }
    
    .workspace-item {
        border: 1px solid #334155;
        transition: all 0.3s ease;
    }
    
    .workspace-item:hover {
        border-color: #3b82f6;
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;

// Adicionar estilos ao documento
const styleSheet = document.createElement("style");
styleSheet.innerText = styles;
document.head.appendChild(styleSheet);

export default UserProfile;