// resources/js/Components/Workspace/Settings/EditRequestsCard.jsx
import React, { useState, useEffect } from 'react';
import { useForm } from '@inertiajs/react';

export default function EditRequestsCard({ workspace }) {
    const [pendingRequests, setPendingRequests] = useState([]);
    const [requestHistory, setRequestHistory] = useState([]);
    const [loading, setLoading] = useState(true);
    const { post } = useForm();

    useEffect(() => {
        loadEditRequests();
    }, []);

    const loadEditRequests = async () => {
        try {
            setLoading(true);
            
            // Carregar solicitações pendentes
            const pendingResponse = await fetch(`/workspace/${workspace.id}/edit-requests`);
            const pendingData = await pendingResponse.json();
            
            // Carregar histórico
            const historyResponse = await fetch(`/workspace/${workspace.id}/edit-requests/history`);
            const historyData = await historyResponse.json();

            if (pendingData.success) setPendingRequests(pendingData.data);
            if (historyData.success) setRequestHistory(historyData.data);
            
        } catch (error) {
            console.error('Erro ao carregar solicitações:', error);
        } finally {
            setLoading(false);
        }
    };

    const approveRequest = async (requestId) => {
        if (!confirm('Tem certeza que deseja aprovar esta solicitação? O usuário será adicionado como colaborador editor.')) {
            return;
        }

        try {
            await post(`/edit-requests/${requestId}/approve`);
            await loadEditRequests(); // Recarregar dados
        } catch (error) {
            console.error('Erro ao aprovar solicitação:', error);
        }
    };

    const rejectRequest = async (requestId) => {
        const reason = prompt('Digite o motivo da rejeição (opcional):');
        if (reason === null) return;

        try {
            await post(`/edit-requests/${requestId}/reject`, { reason });
            await loadEditRequests(); // Recarregar dados
        } catch (error) {
            console.error('Erro ao rejeitar solicitação:', error);
        }
    };

    const getInitials = (name) => {
        return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
    };

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    if (loading) {
        return (
            <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Solicitações de Edição Pendentes
                </h2>
                <div className="text-center py-8">
                    <i className="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                    <p className="text-gray-500 dark:text-gray-400">Carregando solicitações...</p>
                </div>
            </div>
        );
    }

    return (
        <div className="settings-card bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h2 className="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Solicitações de Edição Pendentes
            </h2>
            <p className="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Gerencie as solicitações de usuários que querem editar este workspace.
            </p>
            
            {/* Solicitações Pendentes */}
            <div className="mb-6">
                {pendingRequests.length === 0 ? (
                    <div className="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i className="fas fa-inbox text-4xl mb-4 opacity-50"></i>
                        <p>Nenhuma solicitação pendente.</p>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {pendingRequests.map((request) => (
                            <PendingRequestItem
                                key={request.id}
                                request={request}
                                onApprove={approveRequest}
                                onReject={rejectRequest}
                                getInitials={getInitials}
                                formatDate={formatDate}
                            />
                        ))}
                    </div>
                )}
            </div>

            {/* Histórico de Solicitações */}
            <div className="pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 className="text-md font-medium text-gray-900 dark:text-white mb-4">
                    Histórico de Solicitações
                </h3>
                <div className="space-y-3">
                    {requestHistory.length === 0 ? (
                        <div className="text-center py-4 text-gray-500 dark:text-gray-400">
                            <p>Nenhuma solicitação no histórico.</p>
                        </div>
                    ) : (
                        requestHistory.map((request) => (
                            <HistoryRequestItem
                                key={request.id}
                                request={request}
                                getInitials={getInitials}
                                formatDate={formatDate}
                            />
                        ))
                    )}
                </div>
            </div>
        </div>
    );
}

// Componente para item de solicitação pendente
function PendingRequestItem({ request, onApprove, onReject, getInitials, formatDate }) {
    return (
        <div className="edit-request-item bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <div className="flex items-center justify-between">
                <div className="flex-1">
                    <div className="flex items-center space-x-3 mb-2">
                        <div className="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-semibold">
                            {getInitials(request.user_name)}
                        </div>
                        <div>
                            <h4 className="font-medium text-gray-900 dark:text-white">
                                {request.user_name}
                            </h4>
                            <p className="text-sm text-gray-500 dark:text-gray-400">
                                {request.user_email}
                            </p>
                            <p className="text-xs text-gray-400">
                                Será adicionado como: <span className="font-medium">{request.role}</span>
                            </p>
                        </div>
                        <span className="badge bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">
                            Solicitação de Edição
                        </span>
                    </div>
                    
                    {request.message && (
                        <div className="bg-white dark:bg-gray-700 rounded p-3 mt-2">
                            <p className="text-sm text-gray-600 dark:text-gray-300">
                                {request.message}
                            </p>
                        </div>
                    )}
                    
                    <div className="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400 mt-2">
                        <i className="fas fa-clock"></i>
                        <span>Solicitado em: {formatDate(request.requested_at)}</span>
                    </div>
                </div>
                
                <div className="flex space-x-2 ml-4">
                    <button
                        onClick={() => onApprove(request.id)}
                        className="approve-request-btn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    >
                        <i className="fas fa-check mr-1"></i>Aprovar
                    </button>
                    <button
                        onClick={() => onReject(request.id)}
                        className="reject-request-btn bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    >
                        <i className="fas fa-times mr-1"></i>Rejeitar
                    </button>
                </div>
            </div>
        </div>
    );
}

// Componente para item do histórico
function HistoryRequestItem({ request, getInitials, formatDate }) {
    const statusBadge = request.status === 'approved' ? (
        <span className="badge bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
            Aprovado
        </span>
    ) : (
        <span className="badge bg-red-100 text-red-800 text-xs px-2 py-1 rounded">
            Rejeitado
        </span>
    );

    const approvedBy = request.approved_by ? `por ${request.approved_by.name}` : '';

    return (
        <div className="history-item bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
            <div className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                    <div className="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                        {getInitials(request.requested_by_name)}
                    </div>
                    <div>
                        <p className="text-sm font-medium text-gray-900 dark:text-white">
                            {request.requested_by_name}
                        </p>
                        <p className="text-xs text-gray-500 dark:text-gray-400">
                            {statusBadge} • {formatDate(request.updated_at)} {approvedBy}
                        </p>
                    </div>
                </div>
            </div>
            
            {request.rejected_reason && (
                <div className="mt-2 text-xs text-gray-600 dark:text-gray-400">
                    <strong>Motivo:</strong> {request.rejected_reason}
                </div>
            )}
        </div>
    );
}