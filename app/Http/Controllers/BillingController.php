<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SubscriptionService;
use App\Models\User;
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

        if ($user->isAdmin()) {
            return redirect()->route('dashboard.home')
                ->with('info', 'Administradores não precisam gerenciar assinaturas.');
        }

        // Garantir customer válido no Stripe
        $this->ensureStripeCustomer($user);
        
        $planInfo = $this->subscriptionService->getUserPlanInfo($user);
        $paymentMethod = $this->subscriptionService->getPaymentMethod($user);
        $invoices = $this->subscriptionService->getInvoices($user);
        
        // Próxima fatura com tratamento de erro robusto
        $upcomingInvoice = null;
        try {
            $upcomingInvoice = $this->subscriptionService->getUpcomingInvoice($user);
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar próxima fatura: ' . $e->getMessage());
            // Não quebrar a página se houver erro na próxima fatura
        }

        $subscriptionHistory = $this->subscriptionService->getSubscriptionHistory($user);

        return Inertia::render('Dashboard/Billing/Billing', [
            'billing' => [
                'has_default_payment_method' => $user->hasDefaultPaymentMethod(),
            ],
            'planInfo' => $planInfo,
            'paymentMethod' => $paymentMethod,
            'invoices' => $invoices,
            'upcomingInvoice' => $upcomingInvoice,
            'subscriptionHistory' => $subscriptionHistory,
        ]);
    }

    private function ensureStripeCustomer($user)
    {
        if (!$user->stripe_id) {
            $user->createAsStripeCustomer();
            return;
        }
        
        try {
            Customer::retrieve($user->stripe_id);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            if ($e->getHttpStatus() === 404) {
                $user->stripe_id = null;
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
            // FALTAVA ESTA LINHA - Cancelar a assinatura no serviço
            $this->subscriptionService->cancelSubscription($user);
            
            $periodEnd = $planInfo['current_period_end'] ?? null;

            if ($periodEnd instanceof \Carbon\Carbon || $periodEnd instanceof \DateTime) {
                $endDate = $periodEnd->format('d/m/Y');
            } else {
                $endDate = 'o final do período atual';
            }

            return redirect()->route('billing.show')
                ->with('success', "Assinatura cancelada com sucesso. Você terá acesso até {$endDate}");
                
        } catch (\Exception $e) {
            \Log::error('Erro ao cancelar assinatura: ' . $e->getMessage());
            
            return redirect()->route('billing.show')
                ->with('error', 'Erro ao cancelar assinatura. Tente novamente.');
        }
    }

    public function resumeSubscription()
    {
        $user = Auth::user();
        
        // Verificar se pode reativar (usando apenas Stripe)
        if (!$this->subscriptionService->canResumeSubscription($user)) {
            return redirect()->route('billing.show')
                ->with('error', 'Não é possível reativar a assinatura. A assinatura não está em período de cortesia ou já expirou.');
        }

        try {
            $this->subscriptionService->resumeSubscription($user);
            
            return redirect()->route('billing.show')
                ->with('success', 'Assinatura reativada com sucesso!');
                
        } catch (\Exception $e) {
            \Log::error('Erro ao reativar assinatura: ' . $e->getMessage());
            
            return redirect()->route('billing.show')
                ->with('error', 'Erro ao reativar assinatura: ' . $e->getMessage());
        }
    }
}