<?php

namespace App\Services;

use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Stripe;
use Stripe\Invoice as StripeInvoice;
use Stripe\Customer;
use Carbon\Carbon;

class SubscriptionService
{
    public function createCheckoutSession(User $user, string $priceId)
    {
        \Log::info('Criando checkout session', [
            'user' => $user->email,
            'price_id' => $priceId,
            'stripe_id_atual' => $user->stripe_id
        ]);
        
        try {
            // VERIFICAR SE O USUÁRIO JÁ É CUSTOMER NO STRIPE (mesmo sem stripe_id)
            if (empty($user->stripe_id)) {
                \Log::info('Stripe ID vazio, verificando se usuário já existe no Stripe');
                
                try {
                    Stripe::setApiKey(config('services.stripe.secret'));
                    
                    // Buscar customer por email
                    $customers = Customer::all([
                        'email' => $user->email,
                        'limit' => 1
                    ]);
                    
                    if (count($customers->data) > 0) {
                        // Customer já existe no Stripe - recuperar o ID
                        $existingCustomer = $customers->data[0];
                        $user->stripe_id = $existingCustomer->id;
                        $user->save();
                        
                        \Log::info('Customer existente recuperado do Stripe', [
                            'stripe_id' => $user->stripe_id
                        ]);
                    } else {
                        // Criar novo customer
                        \Log::info('Criando novo customer no Stripe');
                        
                        $user->createAsStripeCustomer([
                            'email' => $user->email,
                            'name' => $user->name,
                        ]);
                        
                        \Log::info('Novo customer criado', [
                            'novo_stripe_id' => $user->stripe_id
                        ]);
                    }
                    
                } catch (\Exception $e) {
                    \Log::error('Erro ao verificar/criar customer: ' . $e->getMessage());
                    throw $e;
                }
            } else {
                // Validar customer existente
                try {
                    Stripe::setApiKey(config('services.stripe.secret'));
                    Customer::retrieve($user->stripe_id);
                    \Log::info('Customer existente validado no Stripe');
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    \Log::warning('Customer não existe no Stripe, recriando', [
                        'stripe_id_antigo' => $user->stripe_id,
                        'erro' => $e->getMessage()
                    ]);
                    
                    // Limpar stripe_id inválido e criar novo
                    $user->stripe_id = null;
                    $user->save();
                    
                    // Recursivamente chamar a função novamente
                    return $this->createCheckoutSession($user, $priceId);
                }
            }
        
            \Log::info('Iniciando criação do checkout', [
                'stripe_id_final' => $user->stripe_id
            ]);
            
            return $user->newSubscription('default', $priceId)
                ->checkout([
                    'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('subscription.pricing'),
                    'customer_update' => ['address' => 'auto'],
                    'locale' => 'auto',
                    'automatic_tax' => ['enabled' => false]
                ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao criar sessão de checkout: ' . $e->getMessage(), [
                'user' => $user->email,
                'stripe_id' => $user->stripe_id,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function handleSuccessfulPayment($sessionId)
    {
        \Log::info('Processando pagamento bem-sucedido', ['session_id' => $sessionId]);

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $user = User::where('stripe_id', $session->customer)->first();

            if (!$user) {
                throw new \Exception('Usuário não encontrado para customer: ' . $session->customer);
            }

            \Log::info('Usuário encontrado:', [
                'email' => $user->email,
                'status_atual' => $user->status,
                'role_atual' => $user->getRoleNames()->first()
            ]);

            // VERIFICAR STATUS REAL NO STRIPE
            if ($session->subscription) {
                $stripeSubscription = \Stripe\Subscription::retrieve($session->subscription);
                
                \Log::info('Dados completos da subscription:', [
                    'status' => $stripeSubscription->status,
                    'subscription_id' => $stripeSubscription->id,
                    'current_period_start' => $stripeSubscription->current_period_start,
                    'current_period_end' => $stripeSubscription->current_period_end,
                    'created' => $stripeSubscription->created
                ]);

                // SÓ PROCESSAR SE A SUBSCRIPTION ESTIVER ATIVA
                if ($stripeSubscription->status === 'active' || $stripeSubscription->status === 'trialing') {
                    
                    if (!empty($stripeSubscription->items->data)) {
                        $firstItem = $stripeSubscription->items->data[0];
                        $priceId = $firstItem->price->id;
                        
                        $plan = $this->getPlanByStripePriceId($priceId);
                        
                        if ($plan) {
                            // CALCULAR DATA DE EXPIRAÇÃO DE FORMA SEGURA
                            $expiresAt = $this->calculateSubscriptionExpiry($stripeSubscription);
                            
                            \Log::info('Ativando subscription com dados:', [
                                'plano' => $plan->name,
                                'expires_at' => $expiresAt,
                                'price_id' => $priceId
                            ]);

                            // USAR O MÉTODO EXISTENTE activateSubscription
                            $user->activateSubscription($plan, $expiresAt);
                            
                            \Log::info('Usuário ativado usando activateSubscription');

                            // Registrar assinatura no banco com datas seguras
                            Subscription::updateOrCreate(
                                [
                                    'stripe_subscription_id' => $session->subscription,
                                    'user_id' => $user->id
                                ],
                                [
                                    'plan_id' => $plan->id,
                                    'stripe_price_id' => $priceId,
                                    'status' => 'active',
                                    'current_period_start' => now(),
                                    'current_period_end' => $expiresAt,
                                ]
                            );

                            \Log::info('Processamento concluído com sucesso');
                            
                        } else {
                            throw new \Exception('Plano não encontrado para o price ID: ' . $priceId);
                        }
                    } else {
                        throw new \Exception('Nenhum item encontrado na subscription');
                    }
                } else {
                    \Log::warning('Subscription no Stripe não está ativa:', [
                        'status' => $stripeSubscription->status
                    ]);
                    throw new \Exception('Subscription não está ativa no Stripe. Status: ' . $stripeSubscription->status);
                }
            } else {
                throw new \Exception('Subscription não encontrada na sessão');
            }

            // VERIFICAÇÃO FINAL
            $user->refresh();
            \Log::info('VERIFICAÇÃO FINAL - Usuário após processamento:', [
                'email' => $user->email,
                'status' => $user->status,
                'role_atual' => $user->getRoleNames()->first()
            ]);

            return $user;

        } catch (\Exception $e) {
            \Log::error('Erro ao processar pagamento: ' . $e->getMessage(), [
                'session_id' => $sessionId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Calcular data de expiração
     */
    private function calculateSubscriptionExpiry($stripeSubscription)
    {
        // Prioridade 1: current_period_end
        if (!empty($stripeSubscription->current_period_end)) {
            return Carbon::createFromTimestamp($stripeSubscription->current_period_end);
        }
        
        // Prioridade 2: created + 30 dias (fallback)
        if (!empty($stripeSubscription->created)) {
            return Carbon::createFromTimestamp($stripeSubscription->created)->addMonth();
        }
        
        // Prioridade 3: agora + 30 dias (último fallback)
        \Log::warning('Usando fallback para data de expiração - datas do Stripe não disponíveis');
        return now()->addMonth();
    }

    /**
     * Obter plano baseado no Stripe Price ID
     */
    public function getPlanByStripePriceId($priceId)
    {
        $prices = config('services.stripe.prices');
        
        foreach ($prices as $planName => $stripePriceId) {
            if ($stripePriceId === $priceId) {
                return Plan::where('name', $planName)->first();
            }
        }
        
        // Fallback para Pro se não encontrar
        return Plan::where('name', User::ROLE_PRO)->first();
    }

    /**
     * Obter nome do plano amigável
     */
    public function getFriendlyPlanName($priceId)
    {
        $planNames = [
            config('services.stripe.prices.start') => 'Start',
            config('services.stripe.prices.pro') => 'Pro', 
            config('services.stripe.prices.premium') => 'Premium'
        ];
        
        return $planNames[$priceId] ?? 'Pro';
    }

    /**
     * Obter todos os preços disponíveis
     */
    public function getAvailablePrices()
    {
        return config('services.stripe.prices');
    }

    /**
     * Verificar se price_id é válido
     */
    public function isValidPriceId($priceId)
    {
        $availablePrices = $this->getAvailablePrices();
        return in_array($priceId, $availablePrices);
    }

    /**
     * Obter informações completas do plano do usuário
     */
    public function getUserPlanInfo(User $user)
    {
        \Log::info('Obtendo informações do plano para usuário:', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        $currentPeriodEnd = null;

        $localSubscription = \App\Models\Subscription::where('user_id', $user->id)
            ->whereIn('status', ['active', 'canceled'])
            ->orderBy('created_at', 'desc')
            ->first();

        if ($localSubscription) {
            $isCanceledButActive = $localSubscription->canceled_at !== null &&
                                $localSubscription->current_period_end &&
                                Carbon::parse($localSubscription->current_period_end)->isFuture();

            $currentPeriodEnd = $localSubscription->current_period_end 
                                ? Carbon::parse($localSubscription->current_period_end) 
                                : null;

            return [
                'has_subscription' => true,
                'plan_name' => $localSubscription->plan->name ?? 'unknown',
                'friendly_name' => $this->getFriendlyPlanName($localSubscription->stripe_price_id ?? ''),
                'price_id' => $localSubscription->stripe_price_id ?? null,
                'status' => $isCanceledButActive ? 'active' : $localSubscription->status,
                'current_period_end' => $currentPeriodEnd,
                'cancel_at_period_end' => $localSubscription->canceled_at !== null,
                'on_grace_period' => $isCanceledButActive,
                'local_subscription' => $localSubscription,
            ];
        }

        if ($user->hasActiveStripeSubscription()) {
            $stripeSubscription = $user->getStripeSubscription();

            if ($stripeSubscription) {
                $currentPeriodEnd = isset($stripeSubscription->current_period_end)
                    ? Carbon::parse($stripeSubscription->current_period_end)
                    : null;

                $onGracePeriod = isset($stripeSubscription->cancel_at_period_end) &&
                                $stripeSubscription->cancel_at_period_end &&
                                $currentPeriodEnd && $currentPeriodEnd->isFuture();

                $priceId = $stripeSubscription->stripe_price;
                $plan = $this->getPlanByStripePriceId($priceId);

                return [
                    'has_subscription' => true,
                    'plan_name' => $plan ? $plan->name : 'unknown',
                    'friendly_name' => $this->getFriendlyPlanName($priceId),
                    'price_id' => $priceId,
                    'status' => $stripeSubscription->stripe_status,
                    'current_period_end' => $currentPeriodEnd,
                    'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end ?? false,
                    'on_grace_period' => $onGracePeriod,
                    'stripe_subscription' => $stripeSubscription,
                ];
            }
        }

        return [
            'has_subscription' => false,
            'plan_name' => 'free',
            'friendly_name' => 'Free',
            'status' => 'inactive',
            'current_period_end' => null,
            'cancel_at_period_end' => false,
            'on_grace_period' => false,
        ];
    }

    /**
     * Atualizar assinatura para outro plano
     */
    public function updateSubscription(User $user, string $newPriceId)
    {
        try {
            $subscription = $user->getStripeSubscription();
            
            if (!$subscription) {
                throw new \Exception('Usuário não possui assinatura ativa');
            }

            if (!$this->isValidPriceId($newPriceId)) {
                throw new \Exception('Price ID inválido');
            }

            // Atualizar a assinatura no Stripe
            $user->subscription('default')->swap($newPriceId);
            
            // Sincronizar status local
            $user->syncStripeSubscriptionStatus();

            // Log da atualização
            $newPlan = $this->getPlanByStripePriceId($newPriceId);
            \Log::info("Assinatura atualizada para {$newPlan->name} para usuário: {$user->email}");

            return true;
            
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar assinatura: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getInvoices($user)
    {
        try {
            if (!$user->stripe_id) {
                return collect([]);
            }
            
            return $user->invoices();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            if ($e->getHttpStatus() === 404) {
                $user->update(['stripe_id' => null]);
                return collect([]);
            }
            throw $e;
        }
    }

    /**
     * Obter próxima fatura estimada
     */
    public function getUpcomingInvoice(User $user)
    {
        try {
            // Verificar se o usuário tem uma assinatura ativa no Stripe
            if (!$user->hasActiveStripeSubscription()) {
                return null;
            }

            // Obter a subscription do Stripe
            $stripeSubscription = $user->getStripeSubscription();
            if (!$stripeSubscription) {
                return null;
            }

            \Log::info('Verificando subscription para próxima fatura', [
                'subscription_id' => $stripeSubscription->stripe_id,
                'status' => $stripeSubscription->stripe_status,
                'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end ?? false
            ]);

            // Verificar se a subscription está marcada para cancelamento
            if ($stripeSubscription->cancel_at_period_end ?? false) {
                \Log::info('Subscription cancelada no final do período - sem próxima fatura');
                return null;
            }

            // Tentar método do Cashier primeiro
            try {
                return $user->upcomingInvoice();
            } catch (\Exception $e) {
                \Log::warning('Método upcomingInvoice do Cashier falhou, tentando API direta: ' . $e->getMessage());
                return $this->getUpcomingInvoiceFromStripe($user);
            }
            
        } catch (\Exception $e) {
            \Log::error('Erro ao obter próxima fatura: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obter próxima fatura diretamente da API do Stripe (fallback)
     */
    private function getUpcomingInvoiceFromStripe(User $user)
    {
        $stripeSubscription = $user->getStripeSubscription();

        if (!$stripeSubscription || empty($user->stripe_id)) {
            return null;
        }

        try {
            \Log::info('Tentando obter próxima fatura (Stripe v17.6)');

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            // Substitui o antigo 'upcoming' por 'createPreview'
            $invoicePreview = $stripe->invoices->createPreview([
                'customer' => $user->stripe_id,
                'subscription' => $stripeSubscription->stripe_id,
            ]);

            return $invoicePreview;

        } catch (\Exception $e) {
            \Log::error('Erro ao obter prévia da fatura: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cancelar assinatura (mantém acesso até o final do período)
     */
    public function cancelSubscription(User $user)
    {
        try {
            \Log::info('Iniciando cancelamento de assinatura', ['user' => $user->email]);

            // Verificar se há assinatura ativa no Stripe
            if ($user->hasActiveStripeSubscription()) {
                $stripeSubscription = $user->getStripeSubscription();
                
                \Log::info('Cancelando assinatura no Stripe', [
                    'subscription_id' => $stripeSubscription->stripe_id,
                    'status' => $stripeSubscription->stripe_status
                ]);

                // Método 1: Tentar via Cashier
                $subscription = $user->subscription('default');
                
                if ($subscription) {
                    // Cancelar no Stripe (mantém até o final do período)
                    $subscription->cancel();
                    \Log::info('Assinatura cancelada via Cashier com sucesso');
                } else {
                    // Método 2: Tentar via API direta do Stripe
                    \Log::warning('Assinatura não encontrada via Cashier, tentando API direta');
                    $this->cancelSubscriptionViaStripeAPI($user, $stripeSubscription->stripe_id);
                }
            } else {
                \Log::warning('Usuário não possui assinatura ativa no Stripe', ['user' => $user->email]);
            }

            // Atualizar tabela local
            $localSubscription = \App\Models\Subscription::where('user_id', $user->id)
                ->whereIn('status', ['active', 'trialing'])
                ->orderBy('created_at', 'desc')
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'canceled_at' => now(),
                    'status' => 'canceled'
                ]);
                \Log::info('Subscription local atualizada para canceled');
            } else {
                \Log::warning('Nenhuma subscription local encontrada para cancelar');
            }

            \Log::info('Cancelamento de assinatura concluído com sucesso');
            return true;

        } catch (\Exception $e) {
            \Log::error('Erro ao cancelar assinatura: ' . $e->getMessage(), [
                'user' => $user->email,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Cancelar assinatura via API direta do Stripe (fallback)
     */
    private function cancelSubscriptionViaStripeAPI(User $user, string $subscriptionId)
    {
        try {
            \Log::info('Cancelando assinatura via API direta do Stripe', [
                'subscription_id' => $subscriptionId
            ]);

            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
            
            // Cancelar no final do período
            $stripe->subscriptions->update($subscriptionId, [
                'cancel_at_period_end' => true
            ]);

            \Log::info('Assinatura cancelada via API direta com sucesso');
            return true;

        } catch (\Exception $e) {
            \Log::error('Erro ao cancelar via API direta: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Reativar assinatura cancelada
     */
    public function resumeSubscription(User $user)
    {
        \Log::info('Tentando reativar assinatura via Stripe', ['user' => $user->email]);

        try {
            // 1. Verificar e reativar no Stripe
            $stripeSubscription = $user->getStripeSubscription();
            if (!$stripeSubscription) {
                throw new \Exception('Nenhuma subscription encontrada no Stripe');
            }

            \Log::info('Subscription encontrada para reativação', [
                'subscription_id' => $stripeSubscription->stripe_id,
                'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end ?? false,
                'stripe_status' => $stripeSubscription->stripe_status
            ]);

            // Verificar se está em grace period (apenas cancel_at_period_end = true e status = active)
            $isOnGracePeriod = ($stripeSubscription->cancel_at_period_end ?? false) && 
                            ($stripeSubscription->stripe_status === 'active');
            
            if (!$isOnGracePeriod) {
                throw new \Exception('Subscription não está em grace period - não pode ser reativada. Status: ' . $stripeSubscription->stripe_status);
            }

            // Reativar no Stripe - remover cancel_at_period_end
            Stripe::setApiKey(config('services.stripe.secret'));
            $updatedSubscription = \Stripe\Subscription::update(
                $stripeSubscription->stripe_id,
                ['cancel_at_period_end' => false]
            );
            
            \Log::info('Cancelamento removido - assinatura reativada no Stripe', [
                'subscription_id' => $updatedSubscription->id,
                'cancel_at_period_end' => $updatedSubscription->cancel_at_period_end,
                'status' => $updatedSubscription->status
            ]);

            // 2. Atualizar na SUA tabela subscriptions (apenas para consistência)
            $localSubscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where('stripe_subscription_id', $stripeSubscription->stripe_id)
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'canceled_at' => null,
                    'status' => 'active'
                ]);
                \Log::info('Subscription local atualizada após reativação');
            }

            // 3. Garantir que usuário está ativo
            if ($user->status !== User::STATUS_ACTIVE) {
                $user->update(['status' => User::STATUS_ACTIVE]);
                \Log::info('Status do usuário corrigido para ACTIVE');
            }

            \Log::info('Assinatura reativada com sucesso via Stripe', ['user' => $user->email]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Erro ao reativar assinatura: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verificar se usuário pode reativar assinatura
     */
    public function canResumeSubscription(User $user): bool
    {
        // Verificar apenas no Stripe
        $stripeSubscription = $user->getStripeSubscription();
        if (!$stripeSubscription) {
            \Log::info('Nenhuma subscription encontrada no Stripe - não pode reativar');
            return false;
        }

        \Log::info('Verificando se subscription pode ser reativada no Stripe', [
            'subscription_id' => $stripeSubscription->stripe_id,
            'stripe_status' => $stripeSubscription->stripe_status,
            'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end ?? false,
            'current_period_end' => $stripeSubscription->current_period_end ?? 'null'
        ]);

        $isOnGracePeriod = ($stripeSubscription->cancel_at_period_end ?? false) && 
                        ($stripeSubscription->stripe_status === 'active');
        
        \Log::info('Verificação de grace period no Stripe', [
            'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end ?? false,
            'stripe_status' => $stripeSubscription->stripe_status,
            'is_on_grace_period' => $isOnGracePeriod
        ]);
        
        if ($isOnGracePeriod) {
            \Log::info('Subscription está em grace period - pode ser reativada');
            return true;
        }

        \Log::info('Subscription NÃO está em grace period - NÃO pode ser reativada');
        return false;
    }

    /**
     * Obter histórico de assinaturas do usuário
     */
    public function getSubscriptionHistory(User $user)
    {
        return Subscription::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($subscription) {
                $plan = Plan::find($subscription->plan_id);
                return [
                    'id' => $subscription->id,
                    'plan_name' => $plan ? $plan->name : 'unknown',
                    'status' => $subscription->status,
                    'period_start' => $subscription->current_period_start,
                    'period_end' => $subscription->current_period_end,
                    'canceled_at' => $subscription->canceled_at,
                    'created_at' => $subscription->created_at,
                ];
            });
    }

    /**
     * Obter método de pagamento atual do usuário
     */
    public function getPaymentMethod(User $user)
    {
        try {
            if (!$user->stripe_id) {
                return null;
            }

            $paymentMethods = $user->paymentMethods();
            
            return $paymentMethods->first() ?: null;
            
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar método de pagamento: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Adicionar novo método de pagamento
     */
    public function addPaymentMethod(User $user, string $paymentMethodId)
    {
        \Log::info('Adicionando método de pagamento', ['user' => $user->email]);

        try {
            // Adicionar o método de pagamento
            $user->addPaymentMethod($paymentMethodId);
            
            // Definir como padrão se for o primeiro
            if (!$user->hasDefaultPaymentMethod()) {
                $user->updateDefaultPaymentMethod($paymentMethodId);
            }

            \Log::info('Método de pagamento adicionado com sucesso');
            return true;

        } catch (\Exception $e) {
            \Log::error('Erro ao adicionar método de pagamento: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remover método de pagamento
     */
    public function removePaymentMethod(User $user, string $paymentMethodId)
    {
        \Log::info('Removendo método de pagamento', ['user' => $user->email]);

        try {
            $paymentMethod = $user->findPaymentMethod($paymentMethodId);
            
            if ($paymentMethod) {
                $paymentMethod->delete();
                \Log::info('Método de pagamento removido com sucesso');
                return true;
            }

            throw new \Exception('Método de pagamento não encontrado');

        } catch (\Exception $e) {
            \Log::error('Erro ao remover método de pagamento: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Definir método de pagamento padrão
     */
    public function setDefaultPaymentMethod(User $user, string $paymentMethodId)
    {
        \Log::info('Definindo método de pagamento padrão', ['user' => $user->email]);

        try {
            $user->updateDefaultPaymentMethod($paymentMethodId);
            \Log::info('Método de pagamento definido como padrão');
            return true;

        } catch (\Exception $e) {
            \Log::error('Erro ao definir método de pagamento padrão: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fazer upgrade/downgrade de plano
     */
    public function changePlan(User $user, string $newPriceId)
    {
        \Log::info('Mudando plano do usuário', [
            'user' => $user->email,
            'new_price_id' => $newPriceId
        ]);

        try {
            if (!$user->hasActiveStripeSubscription()) {
                throw new \Exception('Usuário não possui assinatura ativa');
            }

            $subscription = $user->getStripeSubscription();
            $newPlan = $this->getPlanByStripePriceId($newPriceId);

            if (!$newPlan) {
                throw new \Exception('Plano não encontrado');
            }

            // Fazer swap no Stripe
            $user->subscription('default')->swap($newPriceId);

            // Atualizar na tabela local
            $localSubscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'plan_id' => $newPlan->id,
                    'stripe_price_id' => $newPriceId
                ]);
            }

            // Atualizar role do usuário
            $user->syncRoles([$newPlan->name]);

            \Log::info('Plano alterado com sucesso', [
                'user' => $user->email,
                'novo_plano' => $newPlan->name
            ]);

            return $newPlan;

        } catch (\Exception $e) {
            \Log::error('Erro ao alterar plano: ' . $e->getMessage());
            throw $e;
        }
    }

    private function determinarTipoMudanca($planoAtual, $novoPlano)
    {
        $hierarquia = ['free', 'start', 'pro', 'premium'];
        
        $indexAtual = array_search($planoAtual, $hierarquia);
        $indexNovo = array_search($novoPlano, $hierarquia);
        
        if ($indexNovo > $indexAtual) return 'upgrade';
        if ($indexNovo < $indexAtual) return 'downgrade';
        return 'crossgrade';
    }

    private function processarUpgrade(User $user, $subscriptionAtual, $newPriceId, $novoPlano)
    {
        try {
            // 1. Atualizar assinatura no Stripe (mudança imediata)
            $user->subscription('default')->swap($newPriceId);
            
            // 2. Atualizar perfil do usuário IMEDIATAMENTE
            $user->update([
                'current_plan_id' => $novoPlano->id,
                'status' => User::STATUS_ACTIVE
            ]);
            $user->syncRoles([$novoPlano->name]);

            // 3. Registrar nova subscription no banco
            $novaSubscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $novoPlano->id,
                'previous_subscription_id' => $subscriptionAtual->id,
                'stripe_subscription_id' => $subscriptionAtual->stripe_id, // Mesmo ID do Stripe
                'stripe_price_id' => $newPriceId,
                'status' => 'active',
                'upgrade_type' => 'upgrade',
                'current_period_start' => now(),
                'current_period_end' => $subscriptionAtual->ends_at, // Mantém mesma data
            ]);

            // 4. Marcar subscription anterior como substituída
            Subscription::where('id', $subscriptionAtual->id)
                ->update(['status' => 'canceled', 'ends_at' => now()]);

            \Log::info("Upgrade concluído", [
                'user' => $user->email,
                'de' => $subscriptionAtual->plan->name,
                'para' => $novoPlano->name
            ]);

            return $novaSubscription;

        } catch (\Exception $e) {
            \Log::error("Erro no upgrade: " . $e->getMessage());
            throw $e;
        }
    }

    private function processarDowngrade(User $user, $subscriptionAtual, $newPriceId, $novoPlano)
    {
        try {
            // No downgrade, a mudança só ocorre no próximo ciclo
            $user->subscription('default')->swap($newPriceId);
            
            // Registrar a intenção de downgrade
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $novoPlano->id,
                'previous_subscription_id' => $subscriptionAtual->id,
                'stripe_subscription_id' => $subscriptionAtual->stripe_id,
                'stripe_price_id' => $newPriceId,
                'status' => 'active',
                'upgrade_type' => 'downgrade',
                'current_period_start' => $subscriptionAtual->current_period_end, // Começa depois
                'current_period_end' => $subscriptionAtual->current_period_end->addMonth(),
            ]);

            \Log::info("Downgrade agendado", [
                'user' => $user->email,
                'efetivo_em' => $subscriptionAtual->current_period_end
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error("Erro no downgrade: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cria sessão para UPGRADE (mantém assinatura existente)
     */
    public function createUpgradeSession(User $user, string $newPriceId)
    {
        \Log::info('Criando sessão de upgrade', [
            'user' => $user->email,
            'novo_price_id' => $newPriceId
        ]);

        try {
            $planoAtual = $this->getUserPlanInfo($user);

            // Configuração universal para upgrade
            $checkoutData = [
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}&upgrade=true',
                'cancel_url' => route('subscription.pricing'),
                'locale' => 'auto',
                'automatic_tax' => ['enabled' => false],
                'billing_address_collection' => 'required',
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $newPriceId,
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'upgrade_from' => $planoAtual['plan_name'],
                    'upgrade_to' => $this->getPlanByStripePriceId($newPriceId)->name,
                ]
            ];

            $session = $user->checkout($checkoutData);
            
            \Log::info('Sessão de upgrade criada com sucesso', [
                'session_id' => $session->id
            ]);

            return $session;

        } catch (\Exception $e) {
            \Log::error('Erro ao criar sessão de upgrade: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verifica se é o mesmo plano (evita upgrade para mesmo plano)
     */
    public function isSamePlan(User $user, string $newPriceId): bool
    {
        $planoAtual = $this->getUserPlanInfo($user);
        $novoPlano = $this->getPlanByStripePriceId($newPriceId);
        
        return $planoAtual['plan_name'] === $novoPlano->name;
    }

    /**
     * Processa upgrade após pagamento bem-sucedido
     */
    public function processUpgrade($sessionId)
    {
        \Log::info('Processando upgrade pós-pagamento', ['session_id' => $sessionId]);

        try {
            // CONFIGURAR API KEY AQUI
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            $user = User::where('stripe_id', $session->customer)->first();

            if (!$user) {
                \Log::error('Usuário não encontrado para upgrade', ['session_id' => $sessionId]);
                return null;
            }

            \Log::info('Usuário encontrado para upgrade', [
                'user' => $user->email,
                'subscription_id' => $session->subscription
            ]);

            // Atualiza a assinatura existente
            $subscription = $user->getStripeSubscription();
            $newPriceId = $session->line_items->data[0]->price->id;
            
            \Log::info('Fazendo swap da assinatura', [
                'subscription_id' => $subscription->stripe_id,
                'novo_price_id' => $newPriceId
            ]);

            // Faz o swap no Stripe (upgrade imediato)
            $user->subscription('default')->swap($newPriceId);
            
            // Atualiza perfil do usuário IMEDIATAMENTE
            $user->syncStripeSubscriptionStatus();
            
            $novoPlano = $user->getPlan();
            
            \Log::info("Upgrade processado com sucesso", [
                'user' => $user->email,
                'novo_plano' => $novoPlano->name,
                'novo_price_id' => $newPriceId
            ]);

            return $user;

        } catch (\Exception $e) {
            \Log::error('Erro ao processar upgrade: ' . $e->getMessage(), [
                'session_id' => $sessionId
            ]);
            throw $e;
        }
    }
}