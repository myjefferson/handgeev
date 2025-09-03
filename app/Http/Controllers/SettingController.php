<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $settings = User::select('primary_hash_api', 'secondary_hash_api')->where(['id' => Auth::user()->id])->first();
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
            // Gera os novos hashes
            $primaryHash = $this->generateHash();
            $secondaryHash = $this->generateHash();

            // Atualiza o usuário autenticado
            $user = User::findOrFail(Auth::user()->id);
            $user->update([
                'primary_hash_api' => $primaryHash,
                'secondary_hash_api' => $secondaryHash,
            ]);

            // Retorna os hashes atualizados no JSON
            return response()->json([
                'success' => true,
                'message' => 'Códigos gerados com sucesso!',
                'data' => [
                    'primary_hash_api' => $primaryHash,
                    'secondary_hash_api' => $secondaryHash,
                ],
            ]);
        } catch (\Exception $e) {
            // Trata erros e retorna a mensagem de erro
            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao gerar os códigos API.',
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

    public function generateHash()
    {
        $randomString = Str::random(32);
        $hash = hash('sha256', $randomString);
        $cleanHash = preg_replace('/[^a-zA-Z0-9]/', '', $hash);
        return substr($cleanHash, 0, 32);
    }
}
