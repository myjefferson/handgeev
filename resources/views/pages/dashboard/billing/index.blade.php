@extends('template.template-dashboard')

@section('title', 'Gerenciar Assinatura - HandGeev')

@section('content_dashboard')
<div class="min-h-screen bg-slate-900">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Gerenciar Assinatura</h1>
            <p class="text-gray-400 mt-2">Controle seu plano, pagamentos e faturas</p>
        </div>

        <!-- Alertas -->
        @include('components.alerts.alert')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Plano Atual -->
                <div class="bg-slate-800 rounded-2xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-white">Plano Atual</h2>
                        <div class="flex items-center space-x-2">
                            @if($planInfo['has_subscription'])
                                <span class="px-3 py-1 bg-green-500/10 text-green-400 text-sm font-medium rounded-full">
                                    Ativo
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-500/10 text-gray-400 text-sm font-medium rounded-full">
                                    Free
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-400">Plano</p>
                            <p class="text-white text-lg font-semibold capitalize">
                                {{ $planInfo['friendly_name'] ?? 'Free' }}
                            </p>
                        </div>
                        
                        @if($planInfo['has_subscription'])
                        <div>
                            <p class="text-gray-400">Próxima cobrança</p>
                            <p class="text-white text-lg font-semibold">
                                {{ $planInfo['current_period_end']?->format('d/m/Y') ?? 'N/A' }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Ações do Plano -->
                    @if($planInfo['has_subscription'])
                    <div class="mt-6 flex flex-wrap gap-3">
                        @if($planInfo['cancel_at_period_end'] || $planInfo['on_grace_period'])
                            @if($planInfo['current_period_end']->isFuture())
                                <form action="{{ route('billing.resume') }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                        Reativar Assinatura
                                    </button>
                                </form>
                            @endif
                        @else
                            <form action="{{ route('billing.cancel') }}" method="POST" 
                                  onsubmit="return confirm('Tem certeza que deseja cancelar sua assinatura?')">
                                @csrf
                                <button type="submit" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                    Cancelar Assinatura
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Status do Cancelamento -->
                    @if($planInfo['cancel_at_period_end'])
                    <div class="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                            <p class="text-yellow-400 text-sm">
                                Sua assinatura será cancelada em <strong>{{ $planInfo['current_period_end']->format('d/m/Y') }}</strong>
                            </p>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="mt-4">
                        <a href="{{ route('subscription.pricing') }}" 
                           class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Fazer Upgrade
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Alterar Plano -->
                @if($planInfo['has_subscription'] && !$planInfo['cancel_at_period_end'])
                <div class="bg-slate-800 rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Alterar Plano</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($availablePlans as $planKey => $plan)
                            @if(!$plan['current'])
                            <div class="border border-slate-600 rounded-lg p-4 hover:border-teal-500 transition-colors">
                                <h3 class="text-white font-semibold mb-2">{{ $plan['name'] }}</h3>
                                <p class="text-gray-400 text-sm mb-3">R$ {{ number_format($plan['price'], 2, ',', '.') }}/mês</p>
                                <form action="{{ route('billing.plan.change') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="price_id" value="{{ $plan['stripe_price_id'] }}">
                                    <button type="submit" 
                                            class="w-full bg-teal-500 hover:bg-teal-600 text-white py-2 rounded-lg font-medium transition-colors">
                                        Mudar para {{ $plan['name'] }}
                                    </button>
                                </form>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Próxima Fatura -->
                @if($upcomingInvoice)
                <div class="bg-slate-800 rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Próxima Fatura</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-gray-400">Data</p>
                            <p class="text-white font-semibold">
                                {{ $upcomingInvoice->next_payment_attempt ? \Carbon\Carbon::createFromTimestamp($upcomingInvoice->next_payment_attempt)->format('d/m/Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-400">Valor</p>
                            <p class="text-white font-semibold">
                                R$ {{ number_format($upcomingInvoice->amount_due / 100, 2, ',', '.') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-400">Status</p>
                            <p class="text-white font-semibold">
                                {{ ucfirst($upcomingInvoice->status) }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Histórico de Assinaturas -->
                @if($subscriptionHistory->count() > 0)
                <div class="bg-slate-800 rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Histórico de Assinaturas</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-700">
                                    <th class="text-left py-2 text-gray-400 font-medium">Plano</th>
                                    <th class="text-left py-2 text-gray-400 font-medium">Status</th>
                                    <th class="text-left py-2 text-gray-400 font-medium">Período</th>
                                    <th class="text-left py-2 text-gray-400 font-medium">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptionHistory as $history)
                                <tr class="border-b border-slate-700/50">
                                    <td class="py-3 text-white capitalize">{{ $history['plan_name'] }}</td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $history['status'] === 'active' ? 'bg-green-500/10 text-green-400' : 'bg-gray-500/10 text-gray-400' }}">
                                            {{ $history['status'] }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-gray-400 text-sm">
                                        @if($history['period_start'] && $history['period_end'])
                                            {{ $history['period_start']->format('d/m/Y') }} - {{ $history['period_end']->format('d/m/Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="py-3 text-gray-400 text-sm">
                                        {{ $history['created_at']->format('d/m/Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Coluna Lateral -->
            <div class="space-y-6">
                
                <!-- Método de Pagamento -->
                <div class="bg-slate-800 rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Método de Pagamento</h2>
                    
                    @if($paymentMethod)
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 bg-slate-700/50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-credit-card text-teal-400 mr-3"></i>
                                    <div>
                                        <p class="text-white font-medium">
                                            **** **** **** {{ $paymentMethod->card->last4 }}
                                        </p>
                                        <p class="text-gray-400 text-xs">
                                            {{ ucfirst($paymentMethod->card->brand) }} • 
                                            Expira {{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($user->hasDefaultPaymentMethod() && $user->defaultPaymentMethod()->id === $paymentMethod->id)
                                        <span class="px-2 py-1 bg-green-500/10 text-green-400 text-xs rounded-full">
                                            Padrão
                                        </span>
                                    @endif
                                    <form action="{{ route('billing.payment-method.remove') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="payment_method_id" value="{{ $paymentMethod->id }}">
                                        <button type="submit" 
                                                class="text-red-400 hover:text-red-300"
                                                onclick="return confirm('Remover este método de pagamento?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-credit-card text-gray-400 text-2xl mb-2"></i>
                            <p class="text-gray-400">Nenhum método de pagamento cadastrado.</p>
                        </div>
                    @endif

                    <!-- Adicionar Novo Cartão -->
                    <div class="mt-4 pt-4 border-t border-slate-700">
                        <h3 class="text-white font-medium mb-3">Adicionar Cartão</h3>
                        <div id="card-element" class="p-3 border border-slate-600 rounded-lg bg-slate-700/50"></div>
                        <button id="card-button" 
                                class="w-full mt-3 bg-teal-500 hover:bg-teal-600 text-white py-2 rounded-lg font-medium transition-colors">
                            Adicionar Cartão
                        </button>
                    </div>
                </div>

                <!-- Faturas Recentes -->
                <div class="bg-slate-800 rounded-2xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Faturas Recentes</h2>
                    
                    @if($invoices->count() > 0)
                        <div class="space-y-3">
                            @foreach($invoices->take(5) as $invoice)
                            <div class="flex items-center justify-between py-2 border-b border-slate-700/30 last:border-0">
                                <div>
                                    <p class="text-white text-sm font-medium">
                                        {{ $invoice->date()->format('d/m/Y') }}
                                    </p>
                                    <p class="text-gray-400 text-xs">
                                        #{{ $invoice->number }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-white text-sm font-medium">
                                        R$ {{ number_format($invoice->total / 100, 2, ',', '.') }}
                                    </p>
                                    <a href="{{ route('billing.invoice.download', $invoice->id) }}" 
                                       class="text-teal-400 hover:text-teal-300 text-xs">
                                        Baixar
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-400 text-center py-4">Nenhuma fatura encontrada.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stripe Elements -->
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
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

    cardElement.mount('#card-element');

    const cardButton = document.getElementById('card-button');
    const clientSecret = '{{ $user->createSetupIntent()->client_secret }}';

    cardButton.addEventListener('click', async (e) => {
        e.preventDefault();
        cardButton.disabled = true;
        cardButton.textContent = 'Processando...';

        const { setupIntent, error } = await stripe.confirmCardSetup(
            clientSecret, {
                payment_method: {
                    card: cardElement,
                }
            }
        );

        if (error) {
            alert('Erro: ' + error.message);
            cardButton.disabled = false;
            cardButton.textContent = 'Adicionar Cartão';
        } else {
            // Enviar payment_method para o servidor
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("billing.payment-method.add") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            const paymentMethod = document.createElement('input');
            paymentMethod.type = 'hidden';
            paymentMethod.name = 'payment_method';
            paymentMethod.value = setupIntent.payment_method;
            form.appendChild(paymentMethod);

            document.body.appendChild(form);
            form.submit();
        }
    });

    // Confirmação para cancelar assinatura
    const cancelForms = document.querySelectorAll('form[onsubmit]');
    cancelForms.forEach(form => {
        form.onsubmit = function(e) {
            if (!confirm('Tem certeza que deseja cancelar sua assinatura? Você perderá acesso aos recursos premium no final do período.')) {
                e.preventDefault();
            }
        };
    });
});
</script>
@endsection