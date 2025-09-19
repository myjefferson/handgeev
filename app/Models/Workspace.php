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
        'is_published',
        'password',
    ];


    public static $rules = [
        'title' => 'required|string|max:100',
        'type_workspace_id' => 'required|integer|exists:type_workspaces,id',
        'is_published' => 'sometimes|boolean',
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
     * Relacionamento: Um workspace pertence a um usuário
     */
    public function user(): BelongsTo{
        return $this->belongsTo(User::class); //workspace pertence a um usuário
    }

    /**
     * Relacionamento: Um workspace tem muitos tópicos
     */
    public function topics(): HasMany{
        return $this->hasMany(Topic::class); //workspace tem muitos tópicos
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
        return $this->hasMany(WorkspaceCollaborator::class);
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

    protected static function booted()
    {
        static::created(function ($workspace) {
            WorkspaceCollaborator::create([
                'workspace_id' => $workspace->id,
                'user_id' => $workspace->user_id,
                'role' => 'owner',
                'invited_by' => $workspace->user_id,
                'invited_at' => now(),
                'joined_at' => now(),
                'status' => 'accepted'
            ]);
        });
    }
}
