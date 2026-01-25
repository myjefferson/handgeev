// StudioInputConnectionsTab.jsx
import React, { useState, useEffect } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';

// Componente simplificado para status
const StatusBadge = ({ status, type = 'default' }) => {
  const getStatusConfig = () => {
    if (type === 'connection') {
      switch (status) {
        case 'active': return { label: 'Ativa', color: 'green', icon: 'fa-check-circle' };
        case 'inactive': return { label: 'Inativa', color: 'red', icon: 'fa-ban' };
        default: return { label: 'Desconhecido', color: 'gray', icon: 'fa-question-circle' };
      }
    }
    
    switch (status) {
      case 'success': return { label: 'Sucesso', color: 'green', icon: 'fa-check-circle' };
      case 'error': return { label: 'Erro', color: 'red', icon: 'fa-exclamation-triangle' };
      case 'pending': return { label: 'Pendente', color: 'yellow', icon: 'fa-clock' };
      case 'running': return { label: 'Executando', color: 'blue', icon: 'fa-spinner fa-spin' };
      case 'rest_api': return { label: 'API REST', color: 'blue', icon: 'fa-cloud' };
      case 'webhook': return { label: 'Webhook', color: 'purple', icon: 'fa-broadcast-tower' };
      default: return { label: status, color: 'gray', icon: 'fa-plug' };
    }
  };

  const config = getStatusConfig();
  
  return (
    <span className={`inline-flex items-center px-2 py-1 rounded text-xs font-medium ${
      config.color === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
      config.color === 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
      config.color === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
      config.color === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
      config.color === 'purple' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' :
      'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
    }`}>
      <i className={`fas ${config.icon} mr-1 ${status === 'running' ? 'animate-spin' : ''}`}></i>
      {config.label}
    </span>
  );
};

// Componente de estatísticas simplificado
const StatsDisplay = ({ stats }) => (
  <div className="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
    <div className="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
      <div className="text-xl font-bold text-gray-900 dark:text-white">{stats.total || 0}</div>
      <div className="text-sm text-gray-600 dark:text-gray-400">Total</div>
    </div>
    <div className="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
      <div className="text-xl font-bold text-green-600 dark:text-green-400">{stats.active || 0}</div>
      <div className="text-sm text-gray-600 dark:text-gray-400">Ativas</div>
    </div>
    <div className="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
      <div className="text-xl font-bold text-teal-600 dark:text-teal-400">{stats.success || 0}</div>
      <div className="text-sm text-gray-600 dark:text-gray-400">Sucesso</div>
    </div>
    <div className="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
      <div className="text-xl font-bold text-red-600 dark:text-red-400">{stats.errors || 0}</div>
      <div className="text-sm text-gray-600 dark:text-gray-400">Erros</div>
    </div>
  </div>
);

const StudioInputConnectionsTab = ({ activeTab, workspace }) => {
    // 🟢 Agora acessamos 'connections' em vez de 'inputConnections'
    const { connections = [], flash, sourceTypes = {}, transformations = {} } = usePage().props;
    const [filter, setFilter] = useState('all');
    const [search, setSearch] = useState('');

    // Debug para verificar os dados recebidos
    useEffect(() => {
        console.log('Conexões recebidas:', connections);
        console.log('Tipos de fonte:', sourceTypes);
        console.log('Transformações:', transformations);
    }, [connections]);

    // Filtrar conexões
    const filteredConnections = connections.filter(conn => {
        if (filter === 'active' && !conn.is_active) return false;
        if (filter === 'inactive' && conn.is_active) return false;
        if (search && !conn.name?.toLowerCase().includes(search.toLowerCase())) return false;
        return true;
    });

    // Calcular estatísticas
    const stats = {
        total: connections.length || 0,
        active: connections.filter(c => c.is_active).length || 0,
        success: connections.reduce((sum, c) => sum + (c.logs?.filter(l => l.status === 'success').length || 0), 0) || 0,
        errors: connections.reduce((sum, c) => sum + (c.logs?.filter(l => l.status === 'error').length || 0), 0) || 0,
        inactive: connections.filter(c => !c.is_active).length || 0,
    };

    // Função para executar conexão
    const executeConnection = async (connectionId) => {
        try {
            const response = await axios.post(`/workspaces/${workspace.id}/input-connections/${connectionId}/execute`);
            if (response.data.success) {
                alert('Conexão executada com sucesso!');
                window.location.reload();
            } else {
                alert('Erro: ' + response.data.message);
            }
        } catch (error) {
            alert('Erro ao executar conexão: ' + error.message);
        }
    };

    if (activeTab !== 'input-connections') return null;

    return (
        <div className="space-y-6 animate-fadeIn">
            <Head title={`Conexões de Entrada - ${workspace.title}`} />

            {/* Header */}
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
                <div className="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
                    <div>
                        <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
                            <i className="fas fa-plug mr-2 text-green-500"></i>
                            Conexões de Entrada
                        </h1>
                        <p className="text-sm text-gray-600 dark:text-gray-400">
                            Configure integrações para enriquecer tópicos com dados externos
                        </p>
                    </div>
                    <div className="flex flex-wrap gap-2">
                        <Link
                            href={route('workspaces.input-connections.create', workspace.id)}
                            className="inline-flex items-center px-4 py-2 bg-teal-600 text-white font-medium rounded-lg hover:bg-teal-700 transition-colors"
                        >
                            <i className="fas fa-plus mr-2"></i>
                            Nova Conexão
                        </Link>
                    </div>
                </div>

                {/* Estatísticas */}
                <StatsDisplay stats={stats} />

                {/* Filtros e busca */}
                <div className="flex flex-col md:flex-row md:items-center justify-between mt-6 space-y-4 md:space-y-0">
                    <div className="flex flex-wrap gap-2">
                        <button
                            onClick={() => setFilter('all')}
                            className={`px-3 py-1 rounded-lg text-sm font-medium transition-colors ${
                                filter === 'all'
                                    ? 'bg-teal-600 text-white'
                                    : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                            }`}
                        >
                            Todas ({stats.total})
                        </button>
                        <button
                            onClick={() => setFilter('active')}
                            className={`px-3 py-1 rounded-lg text-sm font-medium transition-colors ${
                                filter === 'active'
                                    ? 'bg-green-600 text-white'
                                    : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                            }`}
                        >
                            Ativas ({stats.active})
                        </button>
                        <button
                            onClick={() => setFilter('inactive')}
                            className={`px-3 py-1 rounded-lg text-sm font-medium transition-colors ${
                                filter === 'inactive'
                                    ? 'bg-red-600 text-white'
                                    : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                            }`}
                        >
                            Inativas ({stats.inactive})
                        </button>
                    </div>
                    
                    <div className="relative">
                        <input
                            type="text"
                            placeholder="Buscar conexões por nome..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-teal-500 focus:border-transparent w-full md:w-64"
                        />
                        <i className="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>

            {/* Lista de Conexões */}
            <div className="grid grid-cols-1 gap-4">
                {filteredConnections.length > 0 ? (
                    filteredConnections.map(connection => {
                        const lastLog = connection.logs?.[0];
                        const lastStatus = lastLog?.status || 'pending';
                        
                        return (
                            <div key={connection.id} className="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-md transition-shadow p-6">
                                <div className="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                    {/* Informações */}
                                    <div className="flex-1">
                                        <div className="flex items-center gap-3 mb-3">
                                            <StatusBadge 
                                                status={connection.source?.source_type || 'rest_api'} 
                                            />
                                            <div>
                                                <h3 className="font-semibold text-gray-900 dark:text-white">
                                                    {connection.name}
                                                </h3>
                                                <p className="text-sm text-gray-600 dark:text-gray-400">
                                                    {connection.description || 'Sem descrição'}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div className="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400 mb-3">
                                            <div className="flex items-center">
                                                <i className="fas fa-layer-group mr-2"></i>
                                                <span>Estrutura: {connection.structure?.name || 'Sem estrutura'}</span>
                                            </div>
                                            
                                            {connection.topic && (
                                                <div className="flex items-center">
                                                    <i className="fas fa-file-alt mr-2"></i>
                                                    <span>Tópico: {connection.topic.title}</span>
                                                </div>
                                            )}
                                            
                                            <div className="flex items-center">
                                                <i className="fas fa-exchange-alt mr-2"></i>
                                                <span>{connection.mappings?.length || 0} campos mapeados</span>
                                            </div>
                                            
                                            <StatusBadge 
                                                status={connection.is_active ? 'active' : 'inactive'} 
                                                type="connection"
                                            />
                                        </div>

                                        {/* Última execução */}
                                        {lastLog && (
                                            <div className="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                                <i className="fas fa-history"></i>
                                                <span>Última execução: {new Date(lastLog.executed_at).toLocaleDateString()}</span>
                                                <StatusBadge status={lastStatus} />
                                            </div>
                                        )}
                                    </div>
                                    
                                    {/* Ações */}
                                    <div className="flex flex-col sm:flex-row gap-2">
                                        <Link 
                                            href={route('workspaces.input-connections.edit', [workspace.id, connection.id])}
                                            className="px-3 py-2 text-sm bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors text-center"
                                        >
                                            <i className="fas fa-edit mr-1"></i>Editar
                                        </Link>
                                        
                                        <Link 
                                            href={route('workspaces.input-connections.logs', [workspace.id, connection.id])}
                                            className="px-3 py-2 text-sm bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-800 transition-colors text-center"
                                        >
                                            <i className="fas fa-history mr-1"></i>Logs
                                        </Link>
                                        
                                        <button 
                                            onClick={() => executeConnection(connection.id)}
                                            disabled={!connection.is_active}
                                            className={`px-3 py-2 text-sm rounded-lg transition-colors ${
                                                connection.is_active
                                                    ? 'bg-green-600 text-white hover:bg-green-700'
                                                    : 'bg-gray-200 text-gray-400 dark:bg-gray-700 dark:text-gray-500 cursor-not-allowed'
                                            }`}
                                        >
                                            <i className="fas fa-play mr-1"></i>Executar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        );
                    })
                ) : (
                    <div className="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow">
                        <i className="fas fa-plug text-4xl text-gray-400 mb-4"></i>
                        <h3 className="text-lg font-medium text-gray-900 dark:text-white mb-2">
                            {search ? 'Nenhuma conexão encontrada' : 'Nenhuma conexão configurada'}
                        </h3>
                        <p className="text-gray-600 dark:text-gray-400 mb-6">
                            {search 
                                ? 'Tente buscar com outros termos ou ajuste os filtros'
                                : 'Comece criando sua primeira conexão de entrada'}
                        </p>
                        <Link
                            href={route('workspaces.input-connections.create', workspace.id)}
                            className="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700"
                        >
                            <i className="fas fa-plus mr-2"></i>
                            Criar Primeira Conexão
                        </Link>
                    </div>
                )}
            </div>

            {/* Informações úteis */}
            {connections.length > 0 && (
                <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-6">
                    <h4 className="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-3">
                        <i className="fas fa-info-circle mr-2"></i>
                        Como funciona
                    </h4>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div className="space-y-2">
                            <p className="text-sm text-blue-700 dark:text-blue-400">
                                <strong>Execução Automática:</strong> Quando um campo trigger é alterado, a conexão é executada automaticamente.
                            </p>
                            <p className="text-sm text-blue-700 dark:text-blue-400">
                                <strong>Execução Manual:</strong> Clique em "Executar" para rodar uma conexão manualmente.
                            </p>
                        </div>
                        <div className="space-y-2">
                            <p className="text-sm text-blue-700 dark:text-blue-400">
                                <strong>Mapeamento:</strong> Dados da API externa são mapeados para campos da sua estrutura.
                            </p>
                            <p className="text-sm text-blue-700 dark:text-blue-400">
                                <strong>Logs:</strong> Acompanhe todas as execuções e identifique erros facilmente.
                            </p>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default StudioInputConnectionsTab;