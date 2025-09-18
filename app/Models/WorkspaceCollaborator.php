<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceCollaborator extends Model
{
    protected $table = 'workspace_collaborators';

    protected $fillable = [
        'workspace_id',
        'user_id',
        'role',
        'invitation_email',
        'invitation_token',
        'invited_by',
        'invited_at',
        'joined_at',
        'status'
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'joined_at' => 'datetime'
    ];

    /**
     * Relacionamento com workspace
     */
    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com quem convidou
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Verificar se é pendente
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Verificar se foi aceito
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Verificar se é owner
     */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Verificar se pode editar
     */
    public function canEdit(): bool
    {
        return in_array($this->role, ['owner', 'admin', 'editor']);
    }

    /**
     * Verificar se pode gerenciar colaboradores
     */
    public function canManageCollaborators(): bool
    {
        return in_array($this->role, ['owner', 'admin']);
    }
}