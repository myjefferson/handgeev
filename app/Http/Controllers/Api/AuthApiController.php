<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthApiController extends Controller
{
    public function token(Request $request)
    {
        $startTime = microtime(true);
        
        // Rate limiting
        $authLimitKey = 'auth_attempts:' . $request->ip();
        if (!RateLimiter::attempt($authLimitKey, 5, function() {}, 300)) {
            $this->logApiRequest(null, null, $startTime, 429, 'AUTH_RATE_LIMIT_EXCEEDED');
            return response()->json([
                'error' => 'Too many authentication attempts',
                'message' => 'Please try again in 5 minutes'
            ], 429);
        }

        // Validação
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (!auth()->attempt($credentials)) {
            $this->logApiRequest(null, null, $startTime, 401, 'INVALID_CREDENTIALS');
            return response()->json(['error' => 'Invalid credentials.'], 401);
        }

        $user = auth()->user();
        $plan = $user->getPlan();

        if (!$plan || !$plan->can_use_api) {
            $this->logApiRequest($user, null, $startTime, 403, 'API_ACCESS_DENIED');
            return response()->json([
                'error' => 'API access denied',
                'message' => 'Your plan does not include API access'
            ], 403);
        }

        try {
            $token = auth('api')->login($user);
            
            $response = response()->json([
                'status' => 'success',
                'authorization' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                    'rate_limits' => [
                        'per_minute' => $plan->api_requests_per_minute,
                    ]
                ]
            ]);

            $this->logApiRequest($user, null, $startTime, 200, 'AUTH_SUCCESS');
            return $response;

        } catch (\Exception $e) {
            $this->logApiRequest($user, null, $startTime, 500, 'TOKEN_GENERATION_FAILED');
            return response()->json([
                'error' => 'Token generation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método para logging de requisições da API
     */
    private function logApiRequest($user, $workspace, $startTime, $statusCode, $statusMessage = '')
    {
        try {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);
            
            ApiRequestLog::create([
                'user_id' => $user?->id,
                'workspace_id' => $workspace?->id,
                'ip_address' => request()->ip(),
                'method' => request()->method(),
                'endpoint' => request()->path(),
                'response_code' => $statusCode,
                'response_time' => $responseTime,
                'user_agent' => request()->userAgent(),
            ]);

            \Log::debug('API Request Logged', [
                'user_id' => $user?->id,
                'workspace_id' => $workspace?->id,
                'endpoint' => request()->path(),
                'method' => request()->method(),
                'status_code' => $statusCode,
                'response_time' => $responseTime,
                'status_message' => $statusMessage
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to log API request: ' . $e->getMessage());
        }
    }
}