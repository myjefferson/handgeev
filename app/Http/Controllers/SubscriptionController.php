<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SubscriptionService;
use App\Models\User;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function pricing()
    {
        $user = Auth::user();
        $currentPlan = $user->planInfo();
        
        $plans = [
            'free' => [
                'name' => 'Free',
                'price' => 0,
                'features' => ['3 Workspaces', '3 Tópicos', '10 Campos', '1.000 req/dia'],
                'current' => $user->isFree()
            ],
            'pro' => [
                'name' => 'Pro',
                'stripe_price_id' => config('services.stripe.pro_price_id'),
                'price' => 29.90,
                'features' => ['Workspaces Ilimitados', 'Tópicos Ilimitados', 'Campos Ilimitados', '100.000 req/dia', 'API Access', 'Export'],
                'current' => $user->isPro()
            ]
        ];

        return view('subscription.pricing', compact('plans', 'currentPlan'));
    }

    public function checkout(Request $request)
    {
        \Log::info('Checkout iniciado', [
            'user' => auth()->user()->email,
            'price_id' => $request->price_id,
            'all_data' => $request->all()
        ]);

        $request->validate([
            'price_id' => 'required|string'
        ]);

        $user = Auth::user();

        // Verificar se price_id é válido
        if (!$this->subscriptionService->isValidPriceId($request->price_id)) {
            \Log::error('Price ID inválido', ['price_id' => $request->price_id]);
            return redirect()->route('subscription.pricing')
                ->with('error', 'Plano selecionado é inválido.');
        }

        \Log::info('Price ID válido, verificando situação atual...');

        // Verificar se já tem assinatura ativa - AGORA COM UPGRADE
        if ($user->hasActiveStripeSubscription()) {
            $planInfo = $this->subscriptionService->getUserPlanInfo($user);
            $novoPlano = $this->subscriptionService->getFriendlyPlanName($request->price_id);
            
            \Log::info('Usuário já tem assinatura, verificando upgrade...', [
                'plano_atual' => $planInfo['friendly_name'],
                'novo_plano' => $novoPlano
            ]);

            // Verificar se é upgrade ou mesmo plano
            if ($this->subscriptionService->isSamePlan($user, $request->price_id)) {
                \Log::warning('Usuário tentou assinar mesmo plano', [
                    'plano' => $planInfo['friendly_name']
                ]);
                return redirect()->route('subscription.pricing')
                    ->with('error', "Você já possui uma assinatura {$planInfo['friendly_name']} ativa.");
            }

            // É UPGRADE - permitir
            \Log::info('Iniciando processo de upgrade...');
            try {
                $checkout = $this->subscriptionService->createUpgradeSession($user, $request->price_id);
                \Log::info('Sessão de upgrade criada com sucesso', ['checkout_url' => $checkout->url]);
                return $checkout;
                
            } catch (\Exception $e) {
                \Log::error('Erro ao criar sessão de upgrade: ' . $e->getMessage());
                return redirect()->route('subscription.pricing')
                    ->with('error', 'Erro ao processar upgrade. Tente novamente.');
            }
        }

        // ASSINATURA NOVA (usuário não tem assinatura ativa)
        \Log::info('Criando nova assinatura...');
        try {
            $checkout = $this->subscriptionService->createCheckoutSession($user, $request->price_id);
            \Log::info('Sessão criada com sucesso', ['checkout_url' => $checkout->url]);
            return $checkout;
            
        } catch (\Exception $e) {
            \Log::error('Erro no checkout: ' . $e->getMessage());
            return redirect()->route('subscription.pricing')
                ->with('error', 'Erro ao processar checkout. Tente novamente.');
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        $isUpgrade = $request->get('upgrade', false);
        
        \Log::info('Página success acessada', [
            'session_id' => $sessionId,
            'is_upgrade' => $isUpgrade
        ]);

        if (!$sessionId) {
            \Log::warning('Session ID não encontrado no success');
            return redirect()->route('subscription.pricing')
                ->with('error', 'Sessão inválida.');
        }

        try {
            if ($isUpgrade) {
                \Log::info('Processando UPGRADE no success');
                $user = $this->subscriptionService->processUpgrade($sessionId);
                $message = 'Upgrade realizado com sucesso!';
            } else {
                \Log::info('Processando NOVA ASSINATURA no success');
                $user = $this->subscriptionService->handleSuccessfulPayment($sessionId);
                $message = 'Assinatura ativada com sucesso!';
            }
            
            if ($user) {
                \Log::info('Processamento success concluído', ['user' => $user->email]);
                return view('subscription.success')->with('success', $message);
            }
            
            \Log::error('Usuário não encontrado após processamento success');
            return redirect()->route('subscription.pricing')
                ->with('error', 'Erro ao processar pagamento.');
                
        } catch (\Exception $e) {
            \Log::error('Erro no success: ' . $e->getMessage(), [
                'session_id' => $sessionId,
                'is_upgrade' => $isUpgrade
            ]);
            return redirect()->route('subscription.pricing')
                ->with('error', 'Erro ao processar pagamento.');
        }
    }

    public function cancelSubscription()
    {
        $user = Auth::user();

        if (!$user->hasActiveStripeSubscription()) {
            return redirect()->route('subscription.pricing')
                ->with('error', 'Você não tem uma assinatura ativa para cancelar.');
        }

        try {
            $this->subscriptionService->cancelSubscription($user);
            
            return redirect()->route('subscription.pricing')
                ->with('success', 'Assinatura cancelada com sucesso. Você terá acesso até o final do período pago.');
                
        } catch (\Exception $e) {
            return redirect()->route('subscription.pricing')
                ->with('error', 'Erro ao cancelar assinatura. Tente novamente.');
        }
    }

    public function resumeSubscription()
    {
        $user = Auth::user();

        try {
            if ($this->subscriptionService->resumeSubscription($user)) {
                return redirect()->route('subscription.pricing')
                    ->with('success', 'Assinatura reativada com sucesso!');
            }
            
            return redirect()->route('subscription.pricing')
                ->with('error', 'Não foi possível reativar a assinatura.');
                
        } catch (\Exception $e) {
            return redirect()->route('subscription.pricing')
                ->with('error', 'Erro ao reativar assinatura. Tente novamente.');
        }
    }

    public function portal()
    {
        $user = Auth::user();
        
        if (!$user->hasActiveStripeSubscription()) {
            return redirect()->route('subscription.pricing')
                ->with('error', 'Você não tem uma assinatura ativa.');
        }

        try {
            return $user->redirectToBillingPortal(route('dashboard'));
        } catch (\Exception $e) {
            return redirect()->route('subscription.pricing')
                ->with('error', 'Erro ao acessar portal de cobrança. Tente novamente.');
        }
    }

    public function invoiceDownload($invoiceId)
    {
        $user = Auth::user();
        
        try {
            return $user->downloadInvoice($invoiceId, [
                'vendor' => 'HandGeev',
                'product' => 'Assinatura Pro',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Não foi possível baixar a fatura.');
        }
    }
}