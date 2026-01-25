// components/ExecutionStats.jsx
import React from 'react';

const ExecutionStats = ({ stats }) => {
  // Calcular taxas de sucesso
  const totalExecutions = stats.success + stats.errors;
  const successRate = totalExecutions > 0 ? ((stats.success / totalExecutions) * 100).toFixed(1) : 0;
  const errorRate = totalExecutions > 0 ? ((stats.errors / totalExecutions) * 100).toFixed(1) : 0;

  return (
    <div className="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
      <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-6">
        <i className="fas fa-chart-bar mr-2 text-teal-500"></i>
        Estatísticas de Execução
      </h3>
      
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {/* Total de Conexões */}
        <div className="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between mb-2">
            <div className="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
              <i className="fas fa-plug text-blue-500 text-lg"></i>
            </div>
            <div className="text-2xl font-bold text-gray-900 dark:text-white">
              {stats.total || 0}
            </div>
          </div>
          <div className="text-sm font-medium text-gray-700 dark:text-gray-300">
            Total de Conexões
          </div>
          <div className="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {stats.active || 0} ativas • {stats.inactive || 0} inativas
          </div>
        </div>

        {/* Conexões Ativas */}
        <div className="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between mb-2">
            <div className="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
              <i className="fas fa-check-circle text-green-500 text-lg"></i>
            </div>
            <div className="text-2xl font-bold text-gray-900 dark:text-white">
              {stats.active || 0}
            </div>
          </div>
          <div className="text-sm font-medium text-gray-700 dark:text-gray-300">
            Conexões Ativas
          </div>
          <div className="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {stats.total ? `${((stats.active / stats.total) * 100).toFixed(0)}% do total` : 'Nenhuma conexão'}
          </div>
        </div>

        {/* Execuções com Sucesso */}
        <div className="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between mb-2">
            <div className="p-2 bg-teal-100 dark:bg-teal-900 rounded-lg">
              <i className="fas fa-rocket text-teal-500 text-lg"></i>
            </div>
            <div className="text-2xl font-bold text-green-600 dark:text-green-400">
              {stats.success || 0}
            </div>
          </div>
          <div className="text-sm font-medium text-gray-700 dark:text-gray-300">
            Execuções Bem-sucedidas
          </div>
          <div className="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {successRate}% de sucesso
          </div>
        </div>

        {/* Execuções com Erro */}
        <div className="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm border border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between mb-2">
            <div className="p-2 bg-red-100 dark:bg-red-900 rounded-lg">
              <i className="fas fa-exclamation-triangle text-red-500 text-lg"></i>
            </div>
            <div className="text-2xl font-bold text-red-600 dark:text-red-400">
              {stats.errors || 0}
            </div>
          </div>
          <div className="text-sm font-medium text-gray-700 dark:text-gray-300">
            Execuções com Erro
          </div>
          <div className="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {errorRate}% de erros
          </div>
        </div>
      </div>

      {/* Gráfico de Barras (simplificado) */}
      {totalExecutions > 0 && (
        <div className="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
          <div className="flex items-center justify-between mb-2">
            <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
              Distribuição de Execuções
            </span>
            <span className="text-xs text-gray-500 dark:text-gray-400">
              Últimas 30 execuções
            </span>
          </div>
          <div className="flex items-end h-8 space-x-1">
            {/* Barra de sucesso */}
            <div 
              className="flex-1 bg-green-500 rounded-t transition-all duration-300"
              style={{ height: `${successRate}%` }}
              title={`${successRate}% sucesso`}
            ></div>
            
            {/* Barra de erro */}
            <div 
              className="flex-1 bg-red-500 rounded-t transition-all duration-300"
              style={{ height: `${errorRate}%` }}
              title={`${errorRate}% erros`}
            ></div>
            
            {/* Barra de pendente */}
            <div 
              className="flex-1 bg-yellow-500 rounded-t transition-all duration-300"
              style={{ height: `${100 - successRate - errorRate}%` }}
              title={`${(100 - successRate - errorRate).toFixed(1)}% pendentes`}
            ></div>
          </div>
          
          <div className="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
            <div className="flex items-center">
              <div className="w-3 h-3 bg-green-500 rounded mr-1"></div>
              <span>Sucesso</span>
            </div>
            <div className="flex items-center">
              <div className="w-3 h-3 bg-red-500 rounded mr-1"></div>
              <span>Erro</span>
            </div>
            <div className="flex items-center">
              <div className="w-3 h-3 bg-yellow-500 rounded mr-1"></div>
              <span>Pendente</span>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

// Propriedades padrão
ExecutionStats.defaultProps = {
  stats: {
    total: 0,
    active: 0,
    success: 0,
    errors: 0,
    inactive: 0,
  },
};

export default ExecutionStats;