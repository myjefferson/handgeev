<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::active()->get();
        
        return response()->json($plans);
    }

    public function show(Plan $plan)
    {
        return response()->json($plan);
    }

    public function current(Request $request)
    {
        $user = $request->user();
        $user->load('plan');
        
        return response()->json([
            'current_plan' => $user->plan,
            'subscription_active' => $user->hasActiveSubscription(),
            'limits' => [
                'workspaces' => [
                    'current' => $user->workspaces()->count(),
                    'max' => $user->plan->max_workspaces,
                    'unlimited' => $user->plan->hasUnlimitedWorkspaces()
                ],
                'can_export' => $user->canExportData(),
                'can_use_api' => $user->canUseApi()
            ]
        ]);
    }
}