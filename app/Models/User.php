<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\QueryException;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use DB;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    const ROLE_FREE = 'free';
    const ROLE_PRO = 'pro';
    const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'surname',
        'password',
        'avatar',
        'email_verified_at',
        'timezone',
        'language',
        'phone',
        'status',
        'primary_hash_api',
        'secondary_hash_api'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed'
        ];
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims() {
        return [];
    }
    
    public function workspaces(){
        return $this->hasMany(Workspace::class);
    }

    public function fields()
    {
        return $this->hasManyThrough(Field::class, Workspace::class);
    }

    // Método seguro para obter o plano
    public function getPlan()
    {
        $roleName = $this->getRoleNames()->first();
        
        if (!$roleName) {
            // Se não tem role, atribui free e retorna plano free
            $this->assignRole(self::ROLE_FREE);
            $roleName = self::ROLE_FREE;
        }
        
        return Plan::where('name', $roleName)->first();
    }

    public function planInfo()
    {
        $plan = $this->getPlan();
        
        return [
            'plan' => $plan,
            'role' => $this->getRoleNames()->first(),
            'limits' => [
                'max_workspaces' => $plan->max_workspaces,
                'max_topics' => $plan->max_topics,
                'max_fields' => $plan->max_fields,
                'can_export' => $plan->can_export,
                'can_use_api' => $plan->can_use_api,
            ],
            'is_admin' => $this->isAdmin(),
            'is_pro' => $this->isPro(),
            'is_free' => $this->isFree()
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isPro(): bool
    {
        return $this->hasRole(self::ROLE_PRO);
    }

    public function isFree(): bool
    {
        return $this->hasRole(self::ROLE_FREE);
    }

    public function getAllUsers() : array {
        try{
            $allUsers = DB::table('users')
            ->leftJoin('model_has_roles', function($join) {
                $join->on(['model_has_roles.model_id' => 'users.id'])
                    ->where('model_has_roles.model_type', 'App\Models\User');
            })
            ->leftJoin('roles', ['roles.id' => 'model_has_roles.role_id'])
            ->leftJoin('plans', ['roles.name' => 'plans.name'])
            ->select(
                'users.id',
                'users.email',
                'users.name',
                'users.surname',
                'roles.name as plan_name',
                'plans.max_workspaces',
                'plans.max_topics',
                'plans.max_fields',
                'plans.can_export',
                'plans.is_active',
            )->get();
            return $allUsers;
        }catch(\QueryException $e){
            dd($e->error());
        }
    }

    public function getUserById($userId = null): object
    {
        try{
            $user = DB::table('users')
            ->leftJoin('model_has_roles', function($join) use ($userId) {
                $join->on(['model_has_roles.model_id' => 'users.id'])
                    ->where('model_has_roles.model_type', 'App\Models\User');
            })
            ->leftJoin('roles', ['roles.id' => 'model_has_roles.role_id'])
            ->leftJoin('plans', ['roles.name' => 'plans.name'])
            ->select(
                'users.id',
                'users.email',
                'users.name',
                'users.surname',
                'roles.name as plan_name',
                'plans.max_workspaces',
                'plans.max_topics',
                'plans.max_fields',
                'plans.can_export',
                'plans.is_active')
            ->where(['users.id' => $userId])->first();
            return $user;
        }catch(\QueryException $e){
            dd($e->error());
        }
    }
    
    // Verificar limites do plano COM SEGURANÇA
    public function canCreateWorkspace(): bool
    {
        if ($this->isAdmin()) return true;
        
        $plan = $this->getPlan();
        $currentWorkspaces = $this->workspaces()->count();
        
        return $plan->max_workspaces === 0 || $currentWorkspaces < $plan->max_workspaces;
    }


    // ========== NOVOS MÉTODOS PARA CONTROLE DE CAMPOS ==========
    
    public function canAddMoreFields($workspaceId = null): bool
    {
        if ($this->isAdmin()) return true;
        
        $plan = $this->getPlan();
        $currentCount = $this->getCurrentFieldsCount($workspaceId);
        
        return $plan->max_fields === 0 || $currentCount < $plan->max_fields;
    }

    public function getFieldsLimit(): int
    {
        $plan = $this->getPlan();
        
        // Planos pro retornam 0 (ilimitado)
        if ($this->isPro() || $this->isAdmin()) {
            return 0;
        }
        
        return $plan->max_fields;
    }

    public function getCurrentFieldsCount($workspaceId = null): int
    {
        if ($workspaceId) {
            // Contar campos por workspace específico
            return Field::whereHas('topic', function($query) use ($workspaceId) {
                $query->where('workspace_id', $workspaceId)
                      ->whereHas('workspace', function($q) {
                          $q->where('user_id', $this->id);
                      });
            })->count();
        }
        
        // Contar todos os campos do usuário
        return $this->fields()->count();
    }

    


    // ========== FIM DOS NOVOS MÉTODOS ==========

    public function canExportData(): bool
    {
        $plan = $this->getPlan();
        return $plan->can_export || $this->isAdmin();
    }

    public function canUseApi(): bool
    {
        $plan = $this->getPlan();
        return $plan->can_use_api || $this->isAdmin();
    }

    // Accessors para acesso seguro aos dados do plano
    public function getPlanLimitsAttribute()
    {
        $plan = $this->getPlan();
        
        return [
            'max_workspaces' => $plan->max_workspaces,
            'max_topics' => $plan->max_topics,
            'max_fields' => $plan->max_fields,
            'can_export' => $plan->can_export,
            'can_use_api' => $plan->can_use_api,
            'current_workspaces' => $this->workspaces()->count(),
            'remaining_workspaces' => $this->getRemainingWorkspacesCount(),
            'current_fields' => $this->getCurrentFieldsCount(),
            'remaining_fields' => $this->getRemainingFieldsCount()
        ];
    }

    public function getRemainingWorkspacesCount(): int
    {
        if ($this->isAdmin()) return PHP_INT_MAX;
        
        $plan = $this->getPlan();
        $currentWorkspaces = $this->workspaces()->count();
        
        return max(0, $plan->max_workspaces - $currentWorkspaces);
    }

    public function getRemainingFieldsCount($workspaceId = null): int
    {
        if ($this->isAdmin()) return PHP_INT_MAX;
        
        $plan = $this->getPlan();
        $currentCount = $this->getCurrentFieldsCount($workspaceId);
        
        return max(0, $plan->max_fields - $currentCount);
    }
}