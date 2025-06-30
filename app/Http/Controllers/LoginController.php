<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.auth.login');
    }

    /**
     * Authentication Login
     */
    public function auth(Request $request)
    {
        $request->only('email', 'password');
        $user = User::where('email', $request->email)->firstOrFail();

        if(!$user){
            return redirect()->route('login.index')->withErrors(['error' => 'Email ou senha inválidos']);
        }

        if(!Hash::check($request->password, $user->password)){
            return redirect()->route('login.index')->withErrors(['error' => 'Email ou senha inválidos']);
        }
        $user->primary_hash_api;
        $user->secondary_hash_api;
        Auth::login($user);

        return redirect()->route('dashboard.home')->with(['success' => 'Você entrou!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect()->route('login.index');
    }
}
