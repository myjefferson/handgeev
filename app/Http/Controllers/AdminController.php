<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Auth;
use DB;

class AdminController extends Controller
{
    public function users()
    {
        $users = Auth::user()->getAllUsers();
        return view('pages.dashboard.admin.users', compact(['users']));
    }

    public function updateUser($id, Request $request)
    {
        $validated = $request->validate([
            'plan_name' => 'sometimes|string|in:free,pro,admin'
        ]);
        
        try {
            $user = User::findOrFail($id);

            $updateData = [];

            if (isset($request->status)) {
                $updateData['status'] = $request->status;
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            if (isset($request->plan_name)) {
                // Verificar se a role existe antes de atribuir
                $roleExists = Role::where('name', $request->plan_name)->exists();
                
                if ($roleExists) {
                    $user->syncRoles($request->plan_name);
                } else {
                    // Opcional: criar a role se não existir
                    $role = Role::create(['name' => $request->plan_name, 'guard_name' => 'web']);
                    $user->syncRoles($role);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        try {



            $user = User::findOrFail($id);
            
            // Não permitir que o usuário se delete
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode excluir sua própria conta!'
                ], 400);
            }
            
            $user->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Usuário excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    public function plans()
    {
        $id_user = Auth::user()->id;
        $user = User::where(['id' => $id_user])->first();
        return view('pages.dashboard.user.index', compact(['user']));
    }
}