<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitService
{
    public static function getRateLimitStatus(User $user): array
    {
        $plan = $user->getPlan();

        return [
            'plan' => $plan->name,
            'limits' => [
                'per_minute' => $plan->api_requests_per_minute,
                'per_hour' => $plan->api_requests_per_hour,
                'per_day' => $plan->api_requests_per_day
            ],
            'current_usage' => [
                'minute' => [
                    'remaining' => RateLimiter::remaining('user_api:' . $user->id . ':minute', $plan->api_requests_per_minute),
                    'available_in' => RateLimiter::availableIn('user_api:' . $user->id . ':minute')
                ],
                'hour' => [
                    'remaining' => RateLimiter::remaining('user_api:' . $user->id . ':hour', $plan->api_requests_per_hour),
                    'available_in' => RateLimiter::availableIn('user_api:' . $user->id . ':hour')
                ]
            ]
        ];
    }

    public static function getSimpleRateLimitInfo(User $user): array
    {
        $plan = $user->getPlan();

        return [
            'plan_name' => $plan->name,
            'limits' => [
                'minute' => $plan->api_requests_per_minute,
                'hour' => $plan->api_requests_per_hour,
                'day' => $plan->api_requests_per_day
            ],
            'remaining_minute' => RateLimiter::remaining('user_api:' . $user->id . ':minute', $plan->api_requests_per_minute)
        ];
    }
}