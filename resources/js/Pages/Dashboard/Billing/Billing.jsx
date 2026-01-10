import React, { useState, useEffect } from 'react';
import { Head, usePage, useForm, router, Link } from '@inertiajs/react';
import DashboardLayout from '@/Layouts/DashboardLayout';
import Alert from '@/Components/Alerts/Alert';

const Billing = ({ 
    planInfo,
    paymentMethod, 
    invoices, 
    upcomingInvoice, 
    subscriptionHistory 
}) => {
    const { auth, csrf_token, flash, billing } = usePage().props;

    const [isProcessing, setIsProcessing] = useState(false);
    const [stripe, setStripe] = useState(null);
    const [elements, setElements] = useState(null);
    const [cardElement, setCardElement] = useState(null);

    // Forms
    const resumeForm = useForm({});
    const cancelForm = useForm({});
    const removePaymentMethodForm = useForm({
        payment_method_id: paymentMethod?.id || ''
    });

    // Inicializar Stripe
    useEffect(() => {
        if (typeof window !== 'undefined' && window.Stripe) {
            const stripeInstance = window.Stripe(process.env.MIX_STRIPE_KEY);
            const elementsInstance = stripeInstance.elements();
            
            const cardElementInstance = elementsInstance.create('card', {
                style: {
                    base: {
                        color: '#ffffff',
                        fontFamily: '"Inter", sans-serif',
                        fontSmoothing: 'antialiased',
                        fontSize: '16px',
                        '::placeholder': {
                            color: '#94a3b8'
                        }
                    },
                    invalid: {
                        color: '#ef4444',
                        iconColor: '#ef4444'
                    }
                }
            });

            setStripe(stripeInstance);
            setElements(elementsInstance);
            setCardElement(cardElementInstance);
        }
    }, []);

    // Montar elemento do cartão
    useEffect(() => {
        if (cardElement) {
            cardElement.mount('#card-element');
        }

        return () => {
            if (cardElement) {
                cardElement.unmount();
            }
        };
    }, [cardElement]);

    // Handlers
    const handleResumeSubscription = () => {
        if (confirm('Deseja reativar sua assinatura?')) {
            resumeForm.post(route('billing.resume'));
        }
    };

    const handleCancelSubscription = () => {
        if (confirm('Tem certeza que deseja cancelar sua assinatura? Você perderá acesso aos recursos premium no final do período.')) {
            cancelForm.post(route('billing.cancel'));
        }
    };

    const handleRemovePaymentMethod = (paymentMethodId) => {
        if (confirm('Remover este método de pagamento?')) {
            removePaymentMethodForm.setData('payment_method_id', paymentMethodId);
            removePaymentMethodForm.post(route('billing.payment-method.remove'));
        }
    };

    const handleAddPaymentMethod = async () => {
        if (!stripe || !cardElement) return;

        setIsProcessing(true);
        
        try {
            // Obter client secret do servidor
            const response = await fetch(route('billing.setup-intent'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf_token
                }
            });
            
            const { client_secret: clientSecret } = await response.json();

            const { setupIntent, error } = await stripe.confirmCardSetup(clientSecret, {
                payment_method: {
                    card: cardElement,
                }
            });

            if (error) {
                alert('Erro: ' + error.message);
            } else {
                // Enviar payment_method para o servidor
                router.post(route('billing.payment-method.add'), {
                    payment_method: setupIntent.payment_method
                });
            }
        } catch (error) {
            alert('Erro ao processar cartão: ' + error.message);
        } finally {
            setIsProcessing(false);
        }
    };

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(amount / 100);
    };

    const formatDate = (dateString) => {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString('pt-BR');
    };

    const formatDateTime = (timestamp) => {
        if (!timestamp) return 'N/A';
        return new Date(timestamp * 1000).toLocaleDateString('pt-BR');
    };

    return (
        <DashboardLayout>
            <Head title="Gerenciar Assinatura - HandGeev" />

            <div className="min-h-screen bg-slate-900">
                <div className="max-w-7xl mx-auto p-0 sm:p-0 md:p-6">
                    
                    {/* Header */}
                    <div className="mb-8">
                        <h1 className="text-3xl font-bold text-white">Gerenciar Assinatura</h1>
                        <p className="text-gray-400 mt-2">Controle seu plano, pagamentos e faturas</p>
                    </div>

                    {/* Alertas */}
                    <Alert />

                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        {/* Coluna Principal */}
                        <div className="lg:col-span-2 space-y-6">
                            
                            {/* Plano Atual */}
                            <div className="bg-slate-800 rounded-2xl p-6">
                                <div className="flex items-center justify-between mb-4">
                                    <h2 className="text-xl font-semibold text-white">Plano Atual</h2>
                                    <div className="flex items-center space-x-2">
                                        {planInfo.has_subscription ? (
                                            <span className="px-3 py-1 bg-green-500/10 text-green-400 text-sm font-medium rounded-full">
                                                Ativo
                                            </span>
                                        ) : (
                                            <span className="px-3 py-1 bg-gray-500/10 text-gray-400 text-sm font-medium rounded-full">
                                                Free
                                            </span>
                                        )}
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p className="text-gray-400">Plano</p>
                                        <p className="text-white text-lg font-semibold capitalize">
                                            {planInfo.friendly_name || 'Free'}
                                        </p>
                                    </div>
                                    
                                    {planInfo.has_subscription && (
                                        <div>
                                            <p className="text-gray-400">Próxima cobrança</p>
                                            <p className="text-white text-lg font-semibold">
                                                {formatDate(planInfo.current_period_end)}
                                            </p>
                                        </div>
                                    )}
                                </div>

                                {/* Ações do Plano */}
                                {planInfo.cancel_at_period_end && (
                                    <div className="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                                        <div className="flex items-center">
                                            <i className="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                                            <p className="text-yellow-400 text-sm">
                                                Sua assinatura está programada para cancelamento em{' '}
                                                <strong>{formatDate(planInfo.current_period_end)}</strong>.
                                                Você continuará com acesso ao plano {planInfo.friendly_name} até esta data.
                                            </p>
                                        </div>
                                    </div>
                                )}

                                {planInfo.has_subscription ? (
                                    <div className="mt-6 flex flex-wrap gap-3">
                                        {(planInfo.cancel_at_period_end || planInfo.on_grace_period) ? (
                                            planInfo.current_period_end && new Date(planInfo.current_period_end) > new Date() && (
                                                <button 
                                                    onClick={handleResumeSubscription}
                                                    disabled={resumeForm.processing}
                                                    className="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
                                                >
                                                    {resumeForm.processing ? 'Processando...' : 'Reativar Assinatura'}
                                                </button>
                                            )
                                        ) : (
                                            <button 
                                                onClick={handleCancelSubscription}
                                                disabled={cancelForm.processing}
                                                className="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
                                            >
                                                {cancelForm.processing ? 'Processando...' : 'Cancelar Assinatura'}
                                            </button>
                                        )}
                                        {
                                            ['start', 'pro', 'premium'].includes(auth.user.plan.name) &&
                                            <Link 
                                                href={route('subscription.pricing')}
                                                className="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                                            >
                                                <i class="fas fa-crown text-white w-3 h-3 mr-2 p-0"></i> Veja outros planos
                                            </Link>
                                        }
                                        
                                    </div>
                                ) : (
                                    <div className="mt-4">
                                        <Link 
                                            href={route('subscription.pricing')}
                                            className="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                                        >
                                            Fazer Upgrade
                                        </Link>
                                    </div>
                                )}
                            </div>
                            
                            {/* Próxima Fatura */}
                            {upcomingInvoice && (
                                <div className="bg-slate-800 rounded-2xl p-6">
                                    <h2 className="text-xl font-semibold text-white mb-4">Próxima Fatura</h2>
                                    
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                                        <div>
                                            <p className="text-gray-400">Data</p>
                                            <p className="text-white font-semibold">
                                                {formatDateTime(upcomingInvoice.next_payment_attempt)}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-gray-400">Valor</p>
                                            <p className="text-white font-semibold">
                                                {formatCurrency(upcomingInvoice.amount_due)}
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-gray-400">Status</p>
                                            <p className="text-white font-semibold capitalize">
                                                {upcomingInvoice.status}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Histórico de Assinaturas */}
                            {subscriptionHistory && subscriptionHistory.length > 0 && (
                                <div className="bg-slate-800 rounded-2xl p-6">
                                    <h2 className="text-xl font-semibold text-white mb-4">Histórico de Assinaturas</h2>
                                    
                                    <div className="overflow-x-auto">
                                        <table className="w-full">
                                            <thead>
                                                <tr className="border-b border-slate-700">
                                                    <th className="text-left py-2 text-gray-400 font-medium">Plano</th>
                                                    <th className="text-left py-2 text-gray-400 font-medium">Status</th>
                                                    <th className="text-left py-2 text-gray-400 font-medium">Período</th>
                                                    <th className="text-left py-2 text-gray-400 font-medium">Data</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {subscriptionHistory.map((history, index) => (
                                                    <tr key={index} className="border-b border-slate-700/50">
                                                        <td className="py-3 text-white capitalize">
                                                            {history.plan_name}
                                                        </td>
                                                        <td className="py-3">
                                                            <span className={`px-2 py-1 text-xs rounded-full ${
                                                                history.status === 'active' 
                                                                    ? 'bg-green-500/10 text-green-400' 
                                                                    : 'bg-gray-500/10 text-gray-400'
                                                            }`}>
                                                                {history.status}
                                                            </span>
                                                        </td>
                                                        <td className="py-3 text-gray-400 text-sm">
                                                            {history.period_start && history.period_end ? (
                                                                `${formatDate(history.period_start)} - ${formatDate(history.period_end)}`
                                                            ) : (
                                                                'N/A'
                                                            )}
                                                        </td>
                                                        <td className="py-3 text-gray-400 text-sm">
                                                            {formatDate(history.created_at)}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Coluna Lateral */}
                        <div className="space-y-6">
                            
                            {/* Método de Pagamento */}
                            <div className="bg-slate-800 rounded-2xl p-6">
                                <h2 className="text-xl font-semibold text-white mb-4">Método de Pagamento</h2>
                                
                                {paymentMethod ? (
                                    <div className="space-y-3">
                                        <div className="flex items-center justify-between p-3 bg-slate-700/50 rounded-lg">
                                            <div className="flex items-center">
                                                <i className="fas fa-credit-card text-teal-400 mr-3"></i>
                                                <div>
                                                    <p className="text-white font-medium">
                                                        **** **** **** {paymentMethod.card.last4}
                                                    </p>
                                                    <p className="text-gray-400 text-xs">
                                                        {paymentMethod.card.brand.charAt(0).toUpperCase() + paymentMethod.card.brand.slice(1)} • 
                                                        Expira {paymentMethod.card.exp_month}/{paymentMethod.card.exp_year}
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="flex items-center space-x-2">
                                                {billing.has_default_payment_method && (
                                                    <span className="px-2 py-1 bg-green-500/10 text-green-400 text-xs rounded-full">
                                                        Padrão
                                                    </span>
                                                )}
                                                <button 
                                                    onClick={() => handleRemovePaymentMethod(paymentMethod.id)}
                                                    disabled={removePaymentMethodForm.processing}
                                                    className="text-red-400 hover:text-red-300 disabled:opacity-50"
                                                >
                                                    <i className="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="text-center py-4">
                                        <i className="fas fa-credit-card text-gray-400 text-2xl mb-2"></i>
                                        <p className="text-gray-400">Nenhum método de pagamento cadastrado.</p>
                                    </div>
                                )}

                                {/* Adicionar Novo Cartão */}
                                <div className="mt-4 pt-4 border-t border-slate-700">
                                    <h3 className="text-white font-medium mb-3">Adicionar Cartão</h3>
                                    <div id="card-element" className="p-3 border border-slate-600 rounded-lg bg-slate-700/50"></div>
                                    <button 
                                        id="card-button"
                                        onClick={handleAddPaymentMethod}
                                        disabled={isProcessing}
                                        className="w-full mt-3 bg-teal-500 hover:bg-teal-600 text-white py-2 rounded-lg font-medium transition-colors disabled:opacity-50"
                                    >
                                        {isProcessing ? 'Processando...' : 'Adicionar Cartão'}
                                    </button>
                                </div>
                            </div>

                            {/* Faturas Recentes */}
                            <div className="bg-slate-800 rounded-2xl p-6">
                                <h2 className="text-xl font-semibold text-white mb-4">Faturas Recentes</h2>
                                
                                {invoices && invoices.length > 0 ? (
                                    <div className="space-y-3">
                                        {invoices.slice(0, 5).map((invoice) => (
                                            <div key={invoice.id} className="flex items-center justify-between py-2 border-b border-slate-700/30 last:border-0">
                                                <div>
                                                    <p className="text-white text-sm font-medium">
                                                        {formatDateTime(invoice.created)}
                                                    </p>
                                                    <p className="text-gray-400 text-xs">
                                                        #{invoice.number}
                                                    </p>
                                                </div>
                                                <div className="text-right">
                                                    <p className="text-white text-sm font-medium">
                                                        {formatCurrency(invoice.total)}
                                                    </p>
                                                    <a 
                                                        href={route('billing.invoice.download', invoice.id)}
                                                        className="text-teal-400 hover:text-teal-300 text-xs"
                                                    >
                                                        Baixar
                                                    </a>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-gray-400 text-center py-4">Nenhuma fatura encontrada.</p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Script do Stripe */}
            <script src="https://js.stripe.com/v3/"></script>
        </DashboardLayout>
    );
};

export default Billing;