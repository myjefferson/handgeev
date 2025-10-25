<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SubscriptionService;
use Stripe\Customer;
use Stripe\Exception\InvalidRequestException;

class BillingController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

        public function index()
    {
        $user = Auth::user();
        
        // Customer válido no Stripe (agora a API key já está configurada globalmente)
        $this->ensureStripeCustomer($user);
        
        $planInfo = $this->subscriptionService->getUserPlanInfo($user);
        $paymentMethod = $this->subscriptionService->getPaymentMethod($user);
        $invoices = $this->subscriptionService->getInvoices($user);
        $upcomingInvoice = $this->subscriptionService->getUpcomingInvoice($user);
        $subscriptionHistory = $this->subscriptionService->getSubscriptionHistory($user);

        // Planos disponíveis para upgrade/downgrade
        $availablePlans = [
            'start' => [
                'name' => 'Start',
                'price' => 10.00,
                'stripe_price_id' => config('services.stripe.prices.start'),
                'current' => $planInfo['plan_name'] === 'start'
            ],
            'pro' => [
                'name' => 'Pro', 
                'price' => 32.00,
                'stripe_price_id' => config('services.stripe.prices.pro'),
                'current' => $planInfo['plan_name'] === 'pro'
            ],
            'premium' => [
                'name' => 'Premium',
                'price' => 70.00,
                'stripe_price_id' => config('services.stripe.prices.premium'),
                'current' => $planInfo['plan_name'] === 'premium'
            ]
        ];

        return view('pages.dashboard.billing.index', compact(
            'user',
            'planInfo', 
            'paymentMethod',
            'invoices', 
            'upcomingInvoice',
            'subscriptionHistory',
            'availablePlans'
        ));
    }

    private function ensureStripeCustomer($user)
    {
        if (!$user->stripe_id) {
            $user->createAsStripeCustomer();
            return;
        }
        
        try {
            Customer::retrieve($user->stripe_id);
        } catch (InvalidRequestException $e) {
            if ($e->getHttpStatus() === 404) {
                $user->stripe_id = null;
                $user->save();
                $user->createAsStripeCustomer();
            }
        }
    }

    public function addPaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string'
        ]);

        $user = Auth::user();
        
        try {
            $this->subscriptionService->addPaymentMethod($user, $request->payment_method);
            
            return redirect()->route('billing.show')
                ->with('success', 'Método de pagamento adicionado com sucesso!');
                
        } catch (\Exception $e) {
            \Log::error('Erro ao adicionar método de pagamento: ' . $e->getMessage());
            
            return redirect()->route('billing.show')
                ->with('error', 'Erro ao adicionar método de pagamento. Tente novamente.');
        }
    }

    public function removePaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string'
        ]);

        $user = Auth::user();
        
        try {
            $this->subscriptionService->removePaymentMethod($user, $request->payment_method_id);
            
            return redirect()->route('billing.show')
                ->with('success', 'Método de pagamento removido com sucesso!');
                
        } catch (\Exception $e) {
            \Log::error('Erro ao remover método de pagamento: ' . $e->getMessage());
            
            return redirect()->route('billing.show')
                ->with('error', 'Erro ao remover método de pagamento. Tente novamente.');
        }
    }

    public function setDefaultPaymentMethod(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|string'
        ]);

        $user = Auth::user();
        
        try {
            $this->subscriptionService->setDefaultPaymentMethod($user, $request->payment_method_id);
            
            return redirect()->route('billing.show')
                ->with('success', 'Método de pagamento definido como padrão!');
                
        } catch (\Exception $e) {
            \Log::error('Erro ao definir método de pagamento padrão: ' . $e->getMessage());
            
            return redirect()->route('billing.show')
                ->with('error', 'Erro ao definir método de pagamento padrão. Tente novamente.');
        }
    }

    public function changePlan(Request $request)
    {
        $request->validate([
            'price_id' => 'required|string'
        ]);

        $user = Auth::user();
        
        try {
            $newPlan = $this->subscriptionService->changePlan($user, $request->price_id);
            
            return redirect()->route('billing.show')
                ->with('success', "Plano alterado para {$newPlan->name} com sucesso!");
                
        } catch (\Exception $e) {
            \Log::error('Erro ao alterar plano: ' . $e->getMessage());
            
            return redirect()->route('billing.show')
                ->with('error', 'Erro ao alterar plano. Tente novamente.');
        }
    }

    public function downloadInvoice($invoiceId)
    {
        $user = Auth::user();
        
        try {
            return $user->downloadInvoice($invoiceId, [
                'vendor' => 'HandGeev',
                'product' => 'Assinatura',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('billing.show')
                ->with('error', 'Não foi possível baixar a fatura.');
        }
    }

    public function cancelSubscription()
    {
        $user = Auth::user();
        
        $planInfo = $this->subscriptionService->getUserPlanInfo($user);
        
        if (!$planInfo['has_subscription']) {
            return redirect()->route('billing.show')
                ->with('error', 'Você não possui uma assinatura ativa para cancelar.');
        }

        if ($planInfo['cancel_at_period_end']) {
            return redirect()->route('billing.show')
                ->with('error', 'Sua assinatura já está programada para cancelamento.');
        }

        try {
            $this->subscriptionService->cancelSubscription($user);
            
            return redirect()->route('billing.show')
                ->with('success', 'Assinatura cancelada com sucesso. Você terá acesso até ' . $planInfo['current_period_end']->format('d/m/Y'));
                
        } catch (\Exception $e) {
            \Log::error('Erro ao cancelar assinatura: ' . $e->getMessage());
            
            return redirect()->route('billing.show')
                ->with('error', 'Erro ao cancelar assinatura. Tente novamente.');
        }
    }

    public function resumeSubscription()
    {
        $user = Auth::user();
        
        if (!$this->subscriptionService->canResumeSubscription($user)) {
            return redirect()->route('billing.show')
                ->with('error', 'Não é possível reativar a assinatura. O período de cortesia já expirou.');
        }

        try {
            $this->subscriptionService->resumeSubscription($user);
            
            return redirect()->route('billing.show')
                ->with('success', 'Assinatura reativada com sucesso!');
                
        } catch (\Exception $e) {
            \Log::error('Erro ao reativar assinatura: ' . $e->getMessage());
            
            return redirect()->route('billing.show')
                ->with('error', 'Erro ao reativar assinatura. Tente novamente.');
        }
    }
}