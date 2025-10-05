<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLang($locale)
    {
        if (!in_array($locale, ['en', 'pt_BR', 'es'])) {
            abort(400);
        }

        App::setLocale($locale);
        Session::put('locale', $locale);

        return redirect()->back();
    }
}