<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class LanguageMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Primeiro verifica se o usuário está logado e tem idioma salvo
        if (Auth::check() && Auth::user()->language) {
            App::setLocale(Auth::user()->language);
            Session::put('locale', Auth::user()->language);
        }
        // 2. Se não, verifica se há idioma na sessão
        elseif (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        // 3. Se não, tenta detectar do browser
        else {
            $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
            $availableLocales = ['en', 'pt', 'es'];
            
            $locale = in_array($browserLocale, $availableLocales) 
                ? ($browserLocale === 'pt' ? 'pt_BR' : $browserLocale) 
                : config('app.locale');
            
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}