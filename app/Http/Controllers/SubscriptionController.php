<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Plan;
use App\Http\Requests\SubscriptionRequest;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum']);
        $this->authorizeResource(Subscription::class, 'subscription');
    }

    public function index()
    {
        $subscriptions = auth()->user()->subscriptions()
            ->with(['plan', 'status'])
            ->paginate(10);

        return response()->json($subscriptions);
    }

    public function store(SubscriptionRequest $request, PaymentService $paymentService)
    {
        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);

        // Verifica se pode fazer upgrade
        if (!$user->canUpgradeTo($plan)) {
            return response()->json([
                'error' => 'Cannot upgrade to this plan'
            ], 403);
        }

        // Processa o pagamento
        $payment = $paymentService->createSubscription($user, $plan, $request->payment_method);

        if (!$payment['success']) {
            return response()->json([
                'error' => $payment['message']
            ], 400);
        }

        return response()->json($payment['subscription'], 201);
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['plan.features', 'payments', 'status']);
        return response()->json($subscription);
    }

    public function cancel(Subscription $subscription)
    {
        $this->authorize('cancel', $subscription);

        $subscription->update([
            'status_id' => SubscriptionStatus::where('name', 'canceled')->first()->id,
            'canceled_at' => now()
        ]);

        return response()->json(['message' => 'Subscription canceled successfully']);
    }

    public function plans()
    {
        $plans = Plan::with('features')
            ->where('active', true)
            ->get();

        return response()->json($plans);
    }
}