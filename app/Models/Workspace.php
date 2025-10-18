<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TypeWorkspace;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Topic;
use App\Models\User;

class Workspace extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type_workspace_id',
        'title',
        'description',
        'is_published',
        'password',
        'type_view_workspace_id',
        'workspace_key_api',
        'api_domain_restriction',
        'api_enabled',
        'api_jwt_required',
    ];


    public static $rules = [
        'title' => 'required|string|max:100',
        'type_workspace_id' => 'required|integer|exists:type_workspaces,id',
        'is_published' => 'sometimes|boolean',
        'workspace_key_api' => 'required|string',
        'description' => 'nullable|string|max:250',
        'password' => 'nullable|string|max:250',
    ];

    protected $casts = [
        'api_enabled' => 'boolean',
        'api_domain_restriction' => 'boolean',
        'api_jwt_required' => 'boolean',
    ];

    // Valor padrão
    protected $attributes = [
        'api_enabled' => false,
        'api_domain_restriction' => false,
        'api_jwt_required' => false,
    ];

    
    public function messages()
    {
        return [
            'title.required' => 'O título é obrigatório',
            'title.max' => 'O título não pode ter mais de 100 caracteres',
            'type_workspace_id.exists' => 'O tipo de workspace selecionado é inválido',
        ];
    }
    
    /**
     * Boot method para criar permissões padrão
     */
    protected static function booted()
    {
        static::created(function ($workspace) {
            Collaborator::create([
                'workspace_id' => $workspace->id,
                'user_id' => $workspace->user_id,
                'role' => 'owner',
                'invited_by' => $workspace->user_id,
                'invited_at' => now(),
                'joined_at' => now(),
                'status' => 'accepted'
            ]);

            // Criar permissões padrão baseadas no plano
            $userPlan = $workspace->user->getPlan()->name;
            $defaultMethods = WorkspaceApiPermission::getDefaultMethods($userPlan);

            foreach ($defaultMethods as $endpoint => $methods) {
                WorkspaceApiPermission::create([
                    'workspace_id' => $workspace->id,
                    'endpoint' => $endpoint,
                    'allowed_methods' => $methods
                ]);
            }
        });
    }

    /**
     * Relacionamento: Um workspace pertence a um usuário
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class); //workspace pertence a um usuário
    }

    /**
     * Relacionamento: Um workspace tem muitos tópicos
     */
    public function topics(): HasMany{
        return $this->hasMany(Topic::class)->orderBy('order', 'asc'); //workspace tem muitos tópicos
    }

    /**
     * Relacionamento: Um workspace tem um tipo
     */
    public function typeWorkspace(): BelongsTo
    {
        return $this->belongsTo(TypeWorkspace::class, 'type_workspace_id');
    }


    public function collaborators()
    {
        return $this->hasMany(Collaborator::class);
    }

    public function totalFields()
    {
        return $this->topics->sum(function($topic) {
            return $topic->fields->count();
        });
    }

    /**
     * Verificar se usuário tem acesso
     */
    public function userHasAccess(User $user, string $permission = null): bool
    {
        // Dono tem acesso total
        if ($this->user_id === $user->id) {
            return true;
        }

        // Verificar se é colaborador aceito
        $collaborator = $this->collaborators()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->first();

        if (!$collaborator) {
            return false;
        }

        // Verificar permissão específica
        if ($permission) {
            $permissions = [
                'workspace.view' => true,
                'workspace.edit' => $collaborator->canEdit(),
                'collaborator.invite' => $collaborator->canManageCollaborators(),
                'collaborator.manage' => $collaborator->canManageCollaborators(),
            ];

            return $permissions[$permission] ?? false;
        }

        return true;
    }

    /**
     * Relacionamento com domínios permitidos
     */
    public function allowedDomains(): HasMany
    {
        return $this->hasMany(WorkspaceAllowedDomain::class);
    }

    /**
     * Verificar se um domínio é permitido
     */
    public function isDomainAllowed($domain): bool
    {
        if (!$this->api_enabled) {
            return false;
        }

        if (!$this->api_domain_restriction) {
            return true;
        }

        $allowedDomains = $this->allowedDomains()
            ->where('is_active', true)
            ->pluck('domain');

        foreach ($allowedDomains as $allowedDomain) {
            if ($this->matchesDomain($domain, $allowedDomain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o domínio corresponde ao padrão permitido
     */
    private function matchesDomain($requestDomain, $allowedDomain)
    {
        $requestDomain = strtolower(trim($requestDomain));
        $allowedDomain = strtolower(trim($allowedDomain));

        // Se for exatamente igual
        if ($requestDomain === $allowedDomain) {
            return true;
        }

        // Se o domínio permitido tem wildcard
        if (strpos($allowedDomain, '*') === 0) {
            $pattern = '/^' . str_replace('\*', '.*', preg_quote($allowedDomain, '/')) . '$/';
            return preg_match($pattern, $requestDomain) === 1;
        }

        return false;
    }

    /**
     * Obter lista de domínios ativos
     */
    public function getActiveDomainsAttribute()
    {
        return $this->allowedDomains()
            ->where('is_active', true)
            ->pluck('domain')
            ->toArray();
    }

    /**
     * Scope para workspaces com API habilitada
     */
    public function scopeWithApiEnabled($query)
    {
        return $query->where('api_enabled', true);
    }

    /**
     * Scope para workspaces com restrição de domínio ativa
     */
    public function scopeWithDomainRestriction($query)
    {
        return $query->where('api_domain_restriction', true);
    }

    /**
     * Adicionar domínio
     */
    public function addAllowedDomain($domain): bool
    {
        return WorkspaceAllowedDomain::updateOrCreate(
            [
                'workspace_id' => $this->id,
                'domain' => $domain
            ],
            ['is_active' => true]
        ) !== null;
    }

    /**
     * Remover/desativar domínio
     */
    public function removeAllowedDomain($domain): bool
    {
        return $this->allowedDomains()
            ->where('domain', $domain)
            ->update(['is_active' => false]);
    }

    /**
     * Ativar domínio previamente removido
     */
    public function activateDomain($domain): bool
    {
        return $this->allowedDomains()
            ->where('domain', $domain)
            ->update(['is_active' => true]);
    }

    // No Workspace.php
    public function fieldsCount()
    {
        return $this->hasManyThrough(
            Field::class,
            Topic::class,
            'workspace_id', // Foreign key on topics table
            'topic_id',      // Foreign key on fields table
            'id',           // Local key on workspaces table
            'id'            // Local key on topics table
        )->count();
    }

    // Ou este método alternativo usando withCount:
    public function loadFieldsCount()
    {
        return $this->loadCount(['topics' => function($query) {
            $query->select(DB::raw('SUM(
                (SELECT COUNT(*) FROM fields WHERE fields.topic_id = topics.id)
            ) as fields_count'));
        }]);
    }

    // Método mais simples usando withCount nos relacionamentos
    public function getFieldsCountAttribute()
    {
        if (!$this->relationLoaded('topics.fields')) {
            $this->load(['topics.fields']);
        }
        
        return $this->topics->sum(function($topic) {
            return $topic->fields->count();
        });
    }

    /**
     * Relacionamento com permissões da API
     */
    public function apiPermissions(): HasMany
    {
        return $this->hasMany(WorkspaceApiPermission::class);
    }

     /**
     * Obter métodos permitidos para um endpoint
     */
    public function getAllowedMethods($endpoint): array
    {
        $permission = $this->apiPermissions()
            ->where('endpoint', $endpoint)
            ->first();

        if ($permission) {
            return $permission->allowed_methods;
        }

        // Retornar padrão baseado no plano
        $userPlan = $this->user->getPlan()->name;
        return WorkspaceApiPermission::getDefaultMethods($userPlan)[$endpoint] ?? ['GET'];
    }

    /**
     * Verificar se método é permitido para endpoint
     */
    public function isMethodAllowed($endpoint, $method): bool
    {
        return in_array(strtoupper($method), $this->getAllowedMethods($endpoint));
    }

    /**
     * Atualizar permissões
     */
    public function updatePermissions($endpoint, $methods): void
    {
        $this->apiPermissions()->updateOrCreate(
            ['endpoint' => $endpoint],
            ['allowed_methods' => $methods]
        );
    }
}
