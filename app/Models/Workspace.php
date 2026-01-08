<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TypeWorkspace;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Topic;
use App\Models\User;
use App\Models\Collaborator;
use App\Models\WorkspaceApiPermission;
use App\Models\WorkspaceAllowedDomain;

class Workspace extends Model
{
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
        'api_https_required',
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
        'api_https_required' => 'boolean'
    ];

    protected $attributes = [
        'api_enabled' => false,
        'api_domain_restriction' => false,
        'api_jwt_required' => false,
        'api_https_required' => true,
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
     * Boot: cria automaticamente o colaborador 'owner'
     * e permissões padrão da API
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

            $userPlan = $workspace->user->getPlan()->name;
            $defaultMethods = WorkspaceApiPermission::getDefaultMethods($userPlan);

            foreach ($defaultMethods as $endpoint => $methods) {
                WorkspaceApiPermission::create([
                    'workspace_id' => $workspace->id,
                    'endpoint'    => $endpoint,
                    'allowed_methods' => $methods
                ]);
            }
        });
    }



    /**
     * RELACIONAMENTOS
     * -------------------------------------------------------
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeWorkspace(): BelongsTo
    {
        return $this->belongsTo(TypeWorkspace::class, 'type_workspace_id');
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(Collaborator::class);
    }

    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class)
            ->orderBy('order', 'asc');
    }

    public function allowedDomains(): HasMany
    {
        return $this->hasMany(WorkspaceAllowedDomain::class);
    }

    public function apiPermissions(): HasMany
    {
        return $this->hasMany(WorkspaceApiPermission::class);
    }



    /**
     * MÉTODOS DE ACESSO / PERMISSÃO
     * -------------------------------------------------------
     */

    public function userHasAccess(User $user, string $permission = null): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        $collaborator = $this->collaborators()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->first();

        if (!$collaborator) {
            return false;
        }

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
     * API - domínios permitidos
     * -------------------------------------------------------
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

    private function matchesDomain($requestDomain, $allowedDomain): bool
    {
        $requestDomain = strtolower(trim($requestDomain));
        $allowedDomain = strtolower(trim($allowedDomain));

        if ($requestDomain === $allowedDomain) {
            return true;
        }

        if (str_starts_with($allowedDomain, '*.')) {
            $pattern = str_replace('*.', '', $allowedDomain);
            if (str_ends_with($requestDomain, $pattern)) {
                return true;
            }
        }

        if (str_contains($requestDomain, '.') &&
            str_ends_with($requestDomain, '.' . $allowedDomain)) {
            return true;
        }

        return false;
    }


    public function getActiveDomainsAttribute()
    {
        return $this->allowedDomains()
            ->where('is_active', true)
            ->pluck('domain')
            ->toArray();
    }


    /**
     * API - métodos permitidos
     * -------------------------------------------------------
     */

    public function getAllowedMethods($endpoint): array
    {
        $permission = $this->apiPermissions()
            ->where('endpoint', $endpoint)
            ->first();

        if ($permission) {
            return $permission->allowed_methods;
        }

        $userPlan = $this->user->getPlan()->name;
        return WorkspaceApiPermission::getDefaultMethods($userPlan)[$endpoint] ?? ['GET'];
    }


    public function isMethodAllowed($endpoint, $method): bool
    {
        return in_array(strtoupper($method), $this->getAllowedMethods($endpoint));
    }


    public function updatePermissions($endpoint, $methods): void
    {
        $this->apiPermissions()->updateOrCreate(
            ['endpoint' => $endpoint],
            ['allowed_methods' => $methods]
        );
    }
}