<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RecordLastLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request):
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        if (Auth::check() && $request->isMethod('GET') && $response->getStatusCode() === 200) {
            $user = Auth::user();
            
            $shouldUpdate = true;
            
            if ($user->last_login_at) {
                $lastUpdate = \Carbon\Carbon::parse($user->last_login_at);
                $shouldUpdate = $lastUpdate->diffInMinutes(now()) > 5;
            }
            
            if ($shouldUpdate) {
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip()
                ]);
            }
        }
        
        return $response;
    }
}