// components/ConnectionStatusBadge.jsx
import React from 'react';

const ConnectionStatusBadge = ({ status, type = 'connection', size = 'medium', showIcon = true }) => {
  // Mapeamento de status para conexões
  const connectionConfigs = {
    active: {
      label: 'Ativa',
      icon: 'fa-check-circle',
      color: 'green',
      bgClass: 'bg-green-100 dark:bg-green-900',
      textClass: 'text-green-800 dark:text-green-200',
      iconClass: 'text-green-500',
      description: 'Conexão ativa e pronta para uso',
    },
    inactive: {
      label: 'Inativa',
      icon: 'fa-ban',
      color: 'red',
      bgClass: 'bg-red-100 dark:bg-red-900',
      textClass: 'text-red-800 dark:text-red-200',
      iconClass: 'text-red-500',
      description: 'Conexão desativada temporariamente',
    },
    pending: {
      label: 'Pendente',
      icon: 'fa-clock',
      color: 'yellow',
      bgClass: 'bg-yellow-100 dark:bg-yellow-900',
      textClass: 'text-yellow-800 dark:text-yellow-200',
      iconClass: 'text-yellow-500',
      description: 'Aguardando configuração completa',
    },
    error: {
      label: 'Erro',
      icon: 'fa-exclamation-circle',
      color: 'red',
      bgClass: 'bg-red-100 dark:bg-red-900',
      textClass: 'text-red-800 dark:text-red-200',
      iconClass: 'text-red-500',
      description: 'Erro na configuração da conexão',
    },
    draft: {
      label: 'Rascunho',
      icon: 'fa-edit',
      color: 'gray',
      bgClass: 'bg-gray-100 dark:bg-gray-900',
      textClass: 'text-gray-800 dark:text-gray-200',
      iconClass: 'text-gray-500',
      description: 'Conexão em modo rascunho',
    },
  };

  // Mapeamento de status para execuções
  const executionConfigs = {
    success: {
      label: 'Sucesso',
      icon: 'fa-check-circle',
      color: 'green',
      bgClass: 'bg-green-100 dark:bg-green-900',
      textClass: 'text-green-800 dark:text-green-200',
      iconClass: 'text-green-500',
      description: 'Execução concluída com sucesso',
    },
    error: {
      label: 'Erro',
      icon: 'fa-exclamation-triangle',
      color: 'red',
      bgClass: 'bg-red-100 dark:bg-red-900',
      textClass: 'text-red-800 dark:text-red-200',
      iconClass: 'text-red-500',
      description: 'Erro na execução da conexão',
    },
    pending: {
      label: 'Pendente',
      icon: 'fa-clock',
      color: 'yellow',
      bgClass: 'bg-yellow-100 dark:bg-yellow-900',
      textClass: 'text-yellow-800 dark:text-yellow-200',
      iconClass: 'text-yellow-500',
      description: 'Aguardando execução',
    },
    running: {
      label: 'Executando',
      icon: 'fa-spinner fa-spin',
      color: 'blue',
      bgClass: 'bg-blue-100 dark:bg-blue-900',
      textClass: 'text-blue-800 dark:text-blue-200',
      iconClass: 'text-blue-500',
      description: 'Conexão em execução',
    },
    timeout: {
      label: 'Timeout',
      icon: 'fa-hourglass-end',
      color: 'orange',
      bgClass: 'bg-orange-100 dark:bg-orange-900',
      textClass: 'text-orange-800 dark:text-orange-200',
      iconClass: 'text-orange-500',
      description: 'Tempo de execução excedido',
    },
    skipped: {
      label: 'Pulado',
      icon: 'fa-forward',
      color: 'gray',
      bgClass: 'bg-gray-100 dark:bg-gray-900',
      textClass: 'text-gray-800 dark:text-gray-200',
      iconClass: 'text-gray-500',
      description: 'Execução pulada',
    },
  };

  // Mapeamento de status para fontes de dados
  const sourceConfigs = {
    rest_api: {
      label: 'API REST',
      icon: 'fa-cloud',
      color: 'blue',
      bgClass: 'bg-blue-100 dark:bg-blue-900',
      textClass: 'text-blue-800 dark:text-blue-200',
      iconClass: 'text-blue-500',
      description: 'Conexão com API REST',
    },
    webhook: {
      label: 'Webhook',
      icon: 'fa-broadcast-tower',
      color: 'purple',
      bgClass: 'bg-purple-100 dark:bg-purple-900',
      textClass: 'text-purple-800 dark:text-purple-200',
      iconClass: 'text-purple-500',
      description: 'Recebimento por webhook',
    },
    csv: {
      label: 'CSV',
      icon: 'fa-file-csv',
      color: 'green',
      bgClass: 'bg-green-100 dark:bg-green-900',
      textClass: 'text-green-800 dark:text-green-200',
      iconClass: 'text-green-500',
      description: 'Importação de arquivo CSV',
    },
    excel: {
      label: 'Excel',
      icon: 'fa-file-excel',
      color: 'green',
      bgClass: 'bg-green-100 dark:bg-green-900',
      textClass: 'text-green-800 dark:text-green-200',
      iconClass: 'text-green-500',
      description: 'Importação de arquivo Excel',
    },
    form: {
      label: 'Formulário',
      icon: 'fa-window-restore',
      color: 'orange',
      bgClass: 'bg-orange-100 dark:bg-orange-900',
      textClass: 'text-orange-800 dark:text-orange-200',
      iconClass: 'text-orange-500',
      description: 'Envio por formulário externo',
    },
  };

  // Seleciona o mapeamento correto baseado no tipo
  const configs = type === 'execution' ? executionConfigs : 
                  type === 'source' ? sourceConfigs : 
                  connectionConfigs;

  // Obtém a configuração para o status específico
  const config = configs[status] || {
    label: 'Desconhecido',
    icon: 'fa-question-circle',
    color: 'gray',
    bgClass: 'bg-gray-100 dark:bg-gray-900',
    textClass: 'text-gray-800 dark:text-gray-200',
    iconClass: 'text-gray-500',
    description: 'Status não reconhecido',
  };

  // Tamanhos do badge
  const sizeClasses = {
    small: 'px-2 py-0.5 text-xs',
    medium: 'px-3 py-1 text-sm',
    large: 'px-4 py-2 text-base',
  };

  const sizeClass = sizeClasses[size] || sizeClasses.medium;

  return (
    <div className="inline-flex items-center">
      <span
        className={`inline-flex items-center ${sizeClass} rounded-full font-medium ${config.bgClass} ${config.textClass}`}
        title={config.description}
      >
        {showIcon && (
          <i className={`fas ${config.icon} ${config.iconClass} mr-1.5 ${status === 'running' ? 'animate-spin' : ''}`}></i>
        )}
        {config.label}
      </span>
    </div>
  );
};

export default ConnectionStatusBadge;