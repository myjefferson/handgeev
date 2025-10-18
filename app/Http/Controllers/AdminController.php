<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Auth;
use DB;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        // Buscar todos os planos disponíveis
        $plans = ['free', 'start', 'pro', 'premium', 'admin'];
        
        // Buscar todos os status disponíveis
        $statuses = ['active', 'inactive', 'suspended', 'past_due', 'unpaid', 'incomplete', 'trial'];

        $query = User::select(
            'users.id',
            'users.email',
            'users.name',
            'users.surname',
            'users.status',
            'users.created_at',
            'users.last_login_at',
            'users.email_verified_at',
            'roles.name as plan_name',
            DB::raw('(SELECT COUNT(*) FROM workspaces WHERE workspaces.user_id = users.id) as workspaces_count'),
            DB::raw('(SELECT COUNT(*) FROM topics 
                     INNER JOIN workspaces ON topics.workspace_id = workspaces.id 
                     WHERE workspaces.user_id = users.id) as topics_count')
        )
        ->leftJoin('model_has_roles', function($join) {
            $join->on('model_has_roles.model_id', '=', 'users.id')
                 ->where('model_has_roles.model_type', User::class);
        })
        ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id');

        // Aplicar filtros
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.surname', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%");
            });
        }

        if ($request->has('plan') && $request->plan) {
            $query->where('roles.name', $request->plan);
        }

        if ($request->has('status') && $request->status) {
            $query->where('users.status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('users.created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('users.created_at', '<=', $request->date_to);
        }

        // Ordenação
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $users = $query->paginate(10);

        return view('pages.dashboard.admin.users', compact('users', 'plans', 'statuses'));
    }

    public function toggleUserStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            
            $action = $request->input('action');
            
            if ($action === 'suspend') {
                $user->update(['status' => 'suspended']);
                $message = 'Usuário suspenso com sucesso!';
            } elseif ($action === 'activate') {
                $user->update(['status' => 'active']);
                $message = 'Usuário ativado com sucesso!';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Ação inválida'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_status' => $user->status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUserDetails($id)
    {
        try {
            $user = User::with(['workspaces', 'topics'])->findOrFail($id);
            
            $stats = [
                'total_workspaces' => $user->workspaces->count(),
                'total_topics' => $user->topics->count(),
                'total_fields' => $user->fields()->count(),
                'last_activity' => $user->last_login_at,
                'email_verified' => (bool)$user->email_verified_at,
                'registration_date' => $user->created_at,
            ];

            return response()->json([
                'success' => true,
                'user' => $user,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        }
    }

    public function updateUser($id, Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'surname' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'plan_name' => 'sometimes|string|in:free,start,pro,premium,admin',
            'status' => 'sometimes|string|in:active,inactive,suspended,past_due,unpaid,incomplete,trial',
        ]);
        
        try {
            $user = User::findOrFail($id);

            $updateData = [];
            $fields = ['name', 'surname', 'email', 'status'];
            
            foreach ($fields as $field) {
                if (isset($validated[$field])) {
                    $updateData[$field] = $validated[$field];
                }
            }

            if (!empty($updateData)) {
                $user->update($updateData);
            }

            if (isset($validated['plan_name'])) {
                $roleExists = Role::where('name', $validated['plan_name'])->exists();
                
                if ($roleExists) {
                    $user->syncRoles($validated['plan_name']);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso!',
                'user' => $user->fresh(['roles'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar usuário: ' . $e->getMessage()
            ], 500);
        }
    }

    public function resetPassword($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Não permitir que o admin reset sua própria senha
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode resetar sua própria senha!'
                ], 400);
            }

            $newPassword = Str::random(12);
            $user->update([
                'password' => Hash::make($newPassword)
            ]);

            // Log da ação
            \Log::info("Senha resetada pelo admin", [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Senha resetada com sucesso!',
                'new_password' => $newPassword
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao resetar senha: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUserActivity($id)
    {
        try {
            $user = User::findOrFail($id);
            
            $activities = DB::table('api_request_logs')
                ->where('user_id', $id)
                ->select('method', 'endpoint', 'response_code', 'response_time', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            $workspaces = $user->workspaces()->withCount('topics')->get();

            return response()->json([
                'success' => true,
                'activities' => $activities,
                'workspaces' => $workspaces
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar atividades: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você não pode excluir sua própria conta!'
                ], 400);
            }
            
            // Backup dos dados antes de excluir
            $userData = [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ];
            
            $user->delete();
            
            // Log da exclusão
            \Log::info('Usuário excluído pelo admin', [
                'admin_id' => auth()->id(),
                'deleted_user' => $userData
            ]);
            
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

    public function getUserActivities($id)
    {
        try {
            $user = User::findOrFail($id);
            
            $activities = DB::table('api_request_logs')
                ->where('user_id', $id)
                ->select('method', 'endpoint', 'response_code', 'response_time', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function($activity) {
                    return [
                        'method' => $activity->method,
                        'endpoint' => $activity->endpoint,
                        'response_code' => $activity->response_code,
                        'response_time' => $activity->response_time . 'ms',
                        'created_at' => \Carbon\Carbon::parse($activity->created_at)->format('d/m/Y H:i:s')
                    ];
                });

            return response()->json([
                'success' => true,
                'activities' => $activities,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar atividades: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkActions(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:activate,suspend,delete,change_plan'
        ]);

        try {
            $users = User::whereIn('id', $validated['user_ids'])->get();
            
            foreach ($users as $user) {
                switch ($validated['action']) {
                    case 'activate':
                        $user->update(['status' => 'active']);
                        break;
                    case 'suspend':
                        $user->update(['status' => 'suspended']);
                        break;
                    case 'delete':
                        if ($user->id !== auth()->id()) {
                            $user->delete();
                        }
                        break;
                    case 'change_plan':
                        if (isset($validated['plan_name'])) {
                            $user->syncRoles($validated['plan_name']);
                        }
                        break;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Ação em lote executada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na ação em lote: ' . $e->getMessage()
            ], 500);
        }
    }
}