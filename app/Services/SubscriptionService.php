<?php

namespace App\Services;

use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Stripe;

class SubscriptionService
{
    public function createCheckoutSession(User $user, string $priceId)
    {
        \Log::info('Criando checkout session', [
            'user' => $user->email,
            'price_id' => $priceId,
            'locale_atual' => app()->getLocale()
        ]);
        
        try {
            if (empty($user->stripe_id)) {
                \Log::info('Usuário sem stripe_id válido, criando customer no Stripe');
                
                $user->stripe_id = null;
                $user->save();
                
                $user->createAsStripeCustomer([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
            }
            
            $locale = $this->getStripeLocale();
        
            \Log::info('Locale formatado para Stripe', ['locale' => $locale]);
            
            return $user->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscription.pricing'),
                'customer_update' => [
                    'address' => 'auto',
                    'name' => 'auto'
                ],
                'locale' => $locale,
                'automatic_tax' => [
                    'enabled' => true
                ],
                'tax_id_collection' => [
                    'enabled' => true
                ],
                'invoice_creation' => [
                    'enabled' => true
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erro ao criar sessão de checkout: ' . $e->getMessage());
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
                throw new \Exception('Usuário não encontrado');
            }

            \Log::info('Usuário antes da atualização:', [
                'role_atual' => $user->getRoleNames()->first()
            ]);

            // OBTER DADOS DA SUBSCRIPTION
            if ($session->subscription) {
                $subscription = \Stripe\Subscription::retrieve($session->subscription);
                
                if (!empty($subscription->items->data)) {
                    $firstItem = $subscription->items->data[0];
                    $priceId = $firstItem->price->id;
                    
                    \Log::info('Price ID obtido:', ['price_id' => $priceId]);
                    
                    $plan = $this->getPlanByStripePriceId($priceId);
                    
                    if ($plan) {
                        // ATUALIZAR APENAS A ROLE (não usar current_plan_id)
                        \Log::info('Atualizando role do usuário para: ' . $plan->name);
                        
                        $user->syncRoles([$plan->name]);
                        $user->update([
                            'status' => User::STATUS_ACTIVE,
                            'plan_expires_at' => now()->addMonth()
                        ]);
                        
                        \Log::info('Role atualizada com sucesso', [
                            'nova_role' => $plan->name,
                            'roles_apos_update' => $user->getRoleNames()
                        ]);

                        // Registrar assinatura no banco (apenas para histórico)
                        Subscription::create([
                            'user_id' => $user->id,
                            'plan_id' => $plan->id,
                            'stripe_subscription_id' => $session->subscription,
                            'stripe_price_id' => $priceId,
                            'status' => 'active',
                            'current_period_start' => now(),
                            'current_period_end' => now()->addMonth(),
                        ]);

                        \Log::info('Assinatura registrada no banco com sucesso');
                        
                    } else {
                        throw new \Exception('Plano não encontrado para o price ID: ' . $priceId);
                    }
                } else {
                    throw new \Exception('Nenhum item encontrado na subscription');
                }
            } else {
                throw new \Exception('Subscription não encontrada na sessão');
            }

            // VERIFICAÇÃO FINAL
            \Log::info('VERIFICAÇÃO FINAL - Usuário após processamento:', [
                'email' => $user->email,
                'role_atual' => $user->getRoleNames()->first(),
                'plano_determinado' => $user->getPlan()->name
            ]);

            return $user;

        } catch (\Exception $e) {
            \Log::error('Erro ao processar pagamento: ' . $e->getMessage());
            throw $e;
        }
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
        // Primeiro verificar na SUA tabela subscriptions
        $localSubscription = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($localSubscription) {
            $plan = $localSubscription->plan;
            return [
                'has_subscription' => true,
                'plan_name' => $plan->name,
                'friendly_name' => $this->getFriendlyPlanName($localSubscription->stripe_price_id),
                'price_id' => $localSubscription->stripe_price_id,
                'status' => $localSubscription->status,
                'current_period_end' => $localSubscription->current_period_end,
                'cancel_at_period_end' => $localSubscription->canceled_at !== null,
                'on_grace_period' => $localSubscription->canceled_at !== null && $localSubscription->current_period_end->isFuture(),
                'local_subscription' => $localSubscription,
            ];
        }

        // Fallback: verificar no Stripe (Cashier)
        $stripeSubscription = $user->getStripeSubscription();
        
        if (!$stripeSubscription) {
            return [
                'has_subscription' => false,
                'plan_name' => 'free',
                'friendly_name' => 'Free',
                'status' => 'inactive'
            ];
        }

        $priceId = $stripeSubscription->stripe_price;
        $plan = $this->getPlanByStripePriceId($priceId);
        
        return [
            'has_subscription' => true,
            'plan_name' => $plan ? $plan->name : 'unknown',
            'friendly_name' => $this->getFriendlyPlanName($priceId),
            'price_id' => $priceId,
            'status' => $stripeSubscription->status,
            'current_period_end' => $stripeSubscription->ends_at,
            'cancel_at_period_end' => $stripeSubscription->cancel_at_period_end,
            'on_grace_period' => $stripeSubscription->onGracePeriod(),
            'stripe_subscription' => $stripeSubscription,
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
            if (!$user->hasActiveStripeSubscription()) {
                return null;
            }

            return $user->upcomingInvoice();
            
        } catch (\Exception $e) {
            \Log::error('Erro ao obter próxima fatura: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Cancelar assinatura (mantém acesso até o final do período)
     */
    public function cancelSubscription(User $user)
    {
        \Log::info('Iniciando cancelamento de assinatura', ['user' => $user->email]);

        try {
            // 1. Cancelar no Stripe (se existir)
            $stripeSubscription = $user->getStripeSubscription();
            if ($stripeSubscription) {
                $stripeSubscription->cancel();
                \Log::info('Assinatura cancelada no Stripe', [
                    'subscription_id' => $stripeSubscription->stripe_id
                ]);
            }

            // 2. Atualizar na SUA tabela subscriptions
            $localSubscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'status' => 'canceled',
                    'canceled_at' => now(),
                    'ends_at' => $stripeSubscription ? $stripeSubscription->ends_at : now()->addMonth()
                ]);
                \Log::info('Subscription local atualizada para canceled');
            }

            // 3. Atualizar usuário (mantém role atual até o fim do período)
            $user->update([
                'status' => User::STATUS_INACTIVE,
                'plan_expires_at' => $stripeSubscription ? $stripeSubscription->ends_at : now()->addMonth()
            ]);

            \Log::info('Assinatura cancelada com sucesso', [
                'user' => $user->email,
                'acesso_ate' => $stripeSubscription ? $stripeSubscription->ends_at : now()->addMonth()
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Erro ao cancelar assinatura: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Reativar assinatura cancelada
     */
    public function resumeSubscription(User $user)
    {
        \Log::info('Tentando reativar assinatura', ['user' => $user->email]);

        try {
            // 1. Reativar no Stripe (se existir)
            $stripeSubscription = $user->getStripeSubscription();
            if ($stripeSubscription && $stripeSubscription->onGracePeriod()) {
                $stripeSubscription->resume();
                \Log::info('Assinatura reativada no Stripe');
            }

            // 2. Atualizar na SUA tabela subscriptions
            $localSubscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where('status', 'canceled')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'status' => 'active',
                    'canceled_at' => null,
                    'ends_at' => null
                ]);
                \Log::info('Subscription local reativada');
            }

            // 3. Atualizar usuário
            $user->update([
                'status' => User::STATUS_ACTIVE,
                'plan_expires_at' => $stripeSubscription ? $stripeSubscription->ends_at : now()->addMonth()
            ]);

            \Log::info('Assinatura reativada com sucesso', ['user' => $user->email]);

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
        // Verificar no Stripe
        $stripeSubscription = $user->getStripeSubscription();
        if ($stripeSubscription && $stripeSubscription->onGracePeriod()) {
            return true;
        }

        // Verificar na tabela local
        $localSubscription = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'canceled')
            ->where('current_period_end', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();

        return $localSubscription !== null;
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
            $subscription = $user->getStripeSubscription();
            $planoAtual = $this->getUserPlanInfo($user);
            $novoPlano = $this->getFriendlyPlanName($newPriceId);
            $locale = $this->getStripeLocale();

            \Log::info('Detalhes do upgrade', [
                'de' => $planoAtual['friendly_name'],
                'para' => $novoPlano,
                'subscription_id' => $subscription->stripe_id,
                'locale' => $locale
            ]);

            // Configuração específica para upgrade
            $checkoutData = [
                'success_url' => route('subscription.success') . '?session_id={CHECKOUT_SESSION_ID}&upgrade=true',
                'cancel_url' => route('subscription.pricing'),
                'customer_update' => ['address' => 'auto'],
                'locale' => $locale,
                'automatic_tax' => ['enabled' => false],
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $newPriceId,
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'upgrade_from' => $planoAtual['plan_name'],
                    'upgrade_to' => $this->getPlanByStripePriceId($newPriceId)->name,
                    'existing_subscription_id' => $subscription->stripe_id
                ]
            ];

            // Usa customer existente se disponível
            if ($user->stripe_id) {
                $checkoutData['customer'] = $user->stripe_id;
                \Log::info('Usando customer existente do Stripe', ['stripe_customer_id' => $user->stripe_id]);
            }

            $session = $user->checkout($checkoutData);
            
            \Log::info('Sessão de upgrade criada com sucesso', [
                'session_id' => $session->id,
                'url' => $session->url
            ]);

            return $session;

        } catch (\Exception $e) {
            \Log::error('Erro ao criar sessão de upgrade: ' . $e->getMessage(), [
                'user' => $user->email,
                'price_id' => $newPriceId
            ]);
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

    /**
     * Converte locale do Laravel para formato do Stripe
     */
    private function getStripeLocale(): string
    {
        $locale = app()->getLocale();
        
        // Se estiver cobrando em USD, usa locale neutro
        $currency = config('cashier.currency', 'usd');
        if (strtolower($currency) === 'usd') {
            return 'en'; // ou 'auto'
        }
        
        // Converte pt_BR para pt-BR
        if (str_contains($locale, '_')) {
            $locale = str_replace('_', '-', $locale);
        }
        
        // Lista de locales válidos do Stripe
        $validLocales = [
            'auto', 'bg', 'cs', 'da', 'de', 'el', 'en', 'en-GB', 'es', 'es-419', 
            'et', 'fi', 'fil', 'fr', 'fr-CA', 'hr', 'hu', 'id', 'it', 'ja', 'ko', 
            'lt', 'lv', 'ms', 'mt', 'nb', 'nl', 'pl', 'pt', 'pt-BR', 'ro', 'ru', 
            'sk', 'sl', 'sv', 'th', 'tr', 'vi', 'zh', 'zh-HK', 'zh-TW'
        ];
        
        if (!in_array($locale, $validLocales)) {
            return 'auto';
        }
        
        return $locale;
    }
}