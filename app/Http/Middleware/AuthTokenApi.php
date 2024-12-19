<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthTokenApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $primary_hash_api = $request->input('primary_hash_api');
        $secondary_hash_api = $request->input('secondary_hash_api');

        if ($primary_hash_api && $secondary_hash_api && $request->isMethod('post')) {
            $userExists = User::where([
                'primary_hash_api' => $primary_hash_api,
                'secondary_hash_api' => $secondary_hash_api
            ])->exists();

            if ($userExists) {
                return $next($request);
            }
        }

        return redirect('/');
    }
}
