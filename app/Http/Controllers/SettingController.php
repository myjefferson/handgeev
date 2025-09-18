<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\HashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = User::select('global_hash_api')->where(['id' => Auth::user()->id])->first();
        return view('pages.dashboard.settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function generateNewHashApi()
    {
        try {           
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            $globalHash = HashService::generateUniqueHash();
            // Atualiza o usuário autenticado
            $user = User::find(auth()->user()->id);
            $user->update([
                'global_hash_api' => $globalHash
            ]);
            
            // Retorna os hashes atualizados no JSON
            return response()->json([
                'success' => true,
                'message' => 'Código global API gerado com sucesso!',
                'data' => [
                    'global_hash_api' => $globalHash
                ],
            ]);
        } catch (\Exception $e) {
            // Trata erros e retorna a mensagem de erro
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao gerar o código Global API.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    
}
