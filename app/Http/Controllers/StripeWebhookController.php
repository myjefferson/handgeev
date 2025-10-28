<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;
use App\Models\User;
use App\Models\PaymentLog;
use App\Services\SubscriptionService;

class StripeWebhookController extends CashierController
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        \Log::info('Webhook: Assinatura criada', [
            'event_id' => $payload['id'],
            'subscription_id' => $payload['data']['object']['id']
        ]);
        
        try {
            $user = $this->getUserByStripeId($payload['data']['object']['customer']);
            if ($user) {
                \Log::info('Sincronizando subscription created:', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                // USAR O MÉTODO EXISTENTE DE SINCRONIZAÇÃO
                $user->syncStripeSubscriptionStatus();
            }

            $this->logPaymentEvent($payload);
            return $this->successMethod();
            
        } catch (\Exception $e) {
            \Log::error('Erro no handleCustomerSubscriptionCreated: ' . $e->getMessage());
            return $this->successMethod();
        }
    }


    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        Log::info('Webhook: Pagamento de fatura bem-sucedido', [
            'event_id' => $payload['id']
        ]);
        
        try {
            $user = $this->getUserByStripeId($payload['data']['object']['customer']);
            if ($user) {
                // USAR SINCRONIZAÇÃO EXISTENTE
                $user->syncStripeSubscriptionStatus();
            }

            $this->logPaymentEvent($payload);
            return $this->successMethod();
            
        } catch (\Exception $e) {
            \Log::error('Erro no handleInvoicePaymentSucceeded: ' . $e->getMessage());
            return $this->successMethod();
        }
    }

    protected function handleInvoicePaymentFailed(array $payload)
    {
        Log::warning('Webhook: Pagamento de fatura falhou', ['payload' => $payload]);
        
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $user->update(['status' => User::STATUS_PAST_DUE]);
        }

        $this->logPaymentEvent($payload);
        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        Log::info('Webhook: Assinatura finalizada (período terminou)', [
            'subscription_id' => $payload['data']['object']['id'],
            'status' => $payload['data']['object']['status']
        ]);
        
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ($user) {
            \Log::info('Processando término da assinatura para usuário:', [
                'user_id' => $user->id,
                'email' => $user->email,
                'status_atual' => $user->status
            ]);
            
            // 1. Atualizar subscription local para canceled
            $localSubscription = \App\Models\Subscription::where('user_id', $user->id)
                ->where('stripe_subscription_id', $payload['data']['object']['id'])
                ->first();

            if ($localSubscription) {
                $localSubscription->update([
                    'status' => 'canceled',
                    'ends_at' => now()
                ]);
                \Log::info('Subscription local atualizada para canceled');
            }

            // 2. Mudar usuário para FREE apenas se não tiver outra subscription ativa
            if (!$user->hasActiveStripeSubscription()) {
                $freePlan = \App\Models\Plan::where('name', \App\Models\User::ROLE_FREE)->first();
                
                // Mudar para FREE mas manter usuário ATIVO
                $user->syncRoles([\App\Models\User::ROLE_FREE]);
                $user->update([
                    'status' => \App\Models\User::STATUS_ACTIVE, // ✅ MANTÉM ATIVO
                    'plan_expires_at' => null
                ]);
                
                \Log::info('Usuário movido para plano FREE após término da assinatura', [
                    'user' => $user->email,
                    'novo_status' => $user->status,
                    'nova_role' => $user->getRoleNames()->first()
                ]);
            } else {
                \Log::info('Usuário tem outra assinatura ativa, mantendo plano atual');
            }
        }

        $this->logPaymentEvent($payload);
        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        \Log::info('Webhook: Assinatura atualizada', [
            'event_id' => $payload['id'],
            'subscription_id' => $payload['data']['object']['id'],
            'status' => $payload['data']['object']['status']
        ]);
        
        try {
            $user = $this->getUserByStripeId($payload['data']['object']['customer']);
            if ($user) {
                \Log::info('Sincronizando subscription updated para usuário:', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'status_anterior' => $user->status
                ]);
                
                $user->syncStripeSubscriptionStatus();
                
                // Log da mudança
                if (isset($payload['data']['object']['items']['data'][0]['price']['id'])) {
                    $priceId = $payload['data']['object']['items']['data'][0]['price']['id'];
                    $plan = $this->subscriptionService->getPlanByStripePriceId($priceId);
                    \Log::info("Assinatura atualizada para {$plan->name} para usuário: {$user->email}");
                }
            }

            $this->logPaymentEvent($payload);
            return $this->successMethod();
            
        } catch (\Exception $e) {
            \Log::error('Erro no handleCustomerSubscriptionUpdated: ' . $e->getMessage());
            return $this->successMethod();
        }
    }

    private function logPaymentEvent(array $payload)
    {
        try {
            $customerId = $payload['data']['object']['customer'] ?? null;
            $user = $customerId ? User::where('stripe_id', $customerId)->first() : null;

            PaymentLog::create([
                'user_id' => $user ? $user->id : null,
                'stripe_event_id' => $payload['id'],
                'event_type' => $payload['type'],
                'payload' => json_encode($payload),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao logar evento de pagamento: ' . $e->getMessage());
        }
    }
}