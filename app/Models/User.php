<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    const ROLE_FREE = 'free';
    const ROLE_PREMIUM = 'premium';
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
        'email',
        'email_verified_at',
        'timezone',
        'language',
        'phone',
        'current_plan_id',
        'plan_expires_at',
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
            'password' => 'hashed',
            'plan_expires_at' => 'datetime',
        ];
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims() {
        return [];
    }
    
    // Métodos helper para verificar roles
    public function plan(){
        return $this->belongsTo(Plan::class, 'current_plan_id');
    }
    
    public function workspaces(){
        return $this->hasMany(Workspace::class);
    }

    public function fields()
    {
        return $this->hasManyThrough(Field::class, Workspace::class);
    }

    // Método seguro para obter o plano
    protected function getSafePlan()
    {
        // Se a relação plan já estiver carregada e não for nula
        if ($this->relationLoaded('plan') && $this->plan) {
            return $this->plan;
        }

        // Se current_plan_id estiver definido, tente carregar o plano
        if ($this->current_plan_id) {
            $plan = Plan::find($this->current_plan_id);
            if ($plan) {
                return $plan;
            }
        }

        // Fallback para plano free padrão
        return (object) [
            'name' => 'free',
            'max_workspaces' => 1,
            'max_topics' => 3,
            'max_fields' => 10,
            'can_export' => false,
            'can_use_api' => false
        ];
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isPremium(): bool
    {
        return $this->hasRole(self::ROLE_PREMIUM);
    }

    public function isFree(): bool
    {
        return $this->hasRole(self::ROLE_FREE);
    }

    // Verificar limites do plano COM SEGURANÇA
    public function canCreateWorkspace(): bool
    {
        if ($this->isAdmin()) return true;
        
        $plan = $this->getSafePlan();
        $maxWorkspaces = $plan->max_workspaces;
        $currentWorkspaces = $this->workspaces()->count();
        
        return $maxWorkspaces === 0 || $currentWorkspaces < $maxWorkspaces;
    }

    // ========== NOVOS MÉTODOS PARA CONTROLE DE CAMPOS ==========
    
    public function canAddMoreFields($workspaceId = null): bool
    {
        // Se for admin, não tem limites
        if ($this->isAdmin()) {
            return true;
        }
        
        $plan = $this->getSafePlan();
        
        // Se o plano tem campos ilimitados
        if ($plan->max_fields === 0) {
            return true;
        }
        
        // Contar campos totais do usuário ou por workspace
        $fieldsCount = $this->getCurrentFieldsCount($workspaceId);
        
        return $fieldsCount < $plan->max_fields;
    }

    public function getFieldsLimit(): int
    {
        $plan = $this->getSafePlan();
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

    public function getRemainingFieldsCount($workspaceId = null): int
    {
        if ($this->isAdmin()) return PHP_INT_MAX;
        
        $plan = $this->getSafePlan();
        
        // Se for ilimitado
        if ($plan->max_fields === 0) return PHP_INT_MAX;
        
        $currentCount = $this->getCurrentFieldsCount($workspaceId);
        return max(0, $plan->max_fields - $currentCount);
    }

    // ========== FIM DOS NOVOS MÉTODOS ==========

    public function canExportData(): bool
    {
        $plan = $this->getSafePlan();
        return $plan->can_export || $this->isAdmin();
    }

    public function canUseApi(): bool
    {
        $plan = $this->getSafePlan();
        return $plan->can_use_api || $this->isAdmin();
    }

    // Verificar se a assinatura está ativa
    public function hasActiveSubscription(): bool
    {
        if ($this->isFree() || $this->isAdmin()) {
            return true;
        }

        return $this->plan_expires_at && $this->plan_expires_at->isFuture();
    }

    // Método para garantir que o usuário tenha um plano
    public function ensurePlan()
    {
        if (!$this->current_plan_id) {
            $freePlan = Plan::where('name', 'free')->first();
            if ($freePlan) {
                $this->update(['current_plan_id' => $freePlan->id]);
            }
        }
    }

    // Accessors para acesso seguro aos dados do plano
    public function getPlanLimitsAttribute()
    {
        $plan = $this->getSafePlan();
        
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
        
        $plan = $this->getSafePlan();
        $maxWorkspaces = $plan->max_workspaces;
        
        if ($maxWorkspaces === 0) return PHP_INT_MAX;
        
        $currentWorkspaces = $this->workspaces()->count();
        return max(0, $maxWorkspaces - $currentWorkspaces);
    }
}