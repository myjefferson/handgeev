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
        \Log::info('Webhook: Assinatura criada', ['payload' => $payload]);
        
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $priceId = $payload['data']['object']['items']['data'][0]['price']['id'];
            $this->subscriptionService->handleSuccessfulPayment($payload['data']['object']['id']);
        }

        $this->logPaymentEvent($payload);
        return $this->successMethod();
    }

    protected function handleInvoicePaymentSucceeded(array $payload)
    {
        Log::info('Webhook: Pagamento de fatura bem-sucedido', ['payload' => $payload]);
        
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $user->syncStripeSubscriptionStatus();
        }

        $this->logPaymentEvent($payload);
        return $this->successMethod();
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
        Log::info('Webhook: Assinatura cancelada', ['payload' => $payload]);
        
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $user->syncStripeSubscriptionStatus();
        }

        $this->logPaymentEvent($payload);
        return $this->successMethod();
    }

    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        \Log::info('Webhook: Assinatura atualizada', ['payload' => $payload]);
        
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);
        if ($user) {
            $user->syncStripeSubscriptionStatus();
            
            // Log da mudança
            $priceId = $payload['data']['object']['items']['data'][0]['price']['id'];
            $plan = $this->subscriptionService->getPlanByStripePriceId($priceId);
            \Log::info("Assinatura atualizada para {$plan->name} para usuário: {$user->email}");
        }

        $this->logPaymentEvent($payload);
        return $this->successMethod();
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