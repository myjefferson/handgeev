<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Services\HashService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = User::select('global_key_api')->where(['id' => Auth::user()->id])->first();
        
        return Inertia::render('Dashboard/Settings/Settings', [
            'lang' => __('settings'),
            'settings' => $settings,
        ]);
    }
    /**
     * Update user language preference
     */
    public function updateLanguage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'language' => 'required|in:pt_BR,en,es',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Idioma inválido',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $user->update(['language' => $request->language]);

            // Atualiza a sessão imediatamente
            session(['locale' => $request->language]);
            app()->setLocale($request->language);

            return response()->json([
                'success' => true,
                'message' => __('language_updated'),
                'data' => [
                    'language' => $request->language,
                    'language_name' => config("app.available_locales.{$request->language}")
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('language_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user timezone
     */
    public function updateTimezone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'timezone' => 'required|in:America/Sao_Paulo,UTC,America/New_York,Europe/London,Asia/Tokyo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Fuso horário inválido',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $user->update(['timezone' => $request->timezone]);

            return response()->json([
                'success' => true,
                'message' => __('timezone_updated'),
                'data' => ['timezone' => $request->timezone]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('timezone_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user settings
     */
    public function getSettings()
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'language' => $user->language,
                'timezone' => $user->timezone,
                'global_key_api' => $user->global_key_api,
            ]
        ]);
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
                'global_key_api' => $globalHash
            ]);
            
            // Retorna os hashes atualizados no JSON
            return response()->json([
                'success' => true,
                'message' => 'Código global API gerado com sucesso!',
                'data' => [
                    'global_key_api' => $globalHash
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
