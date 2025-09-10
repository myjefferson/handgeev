<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkspacePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Workspace $workspace)
    {
        return $user->id === $workspace->user_id;
    }

    public function create(User $user)
    {
        return $user->canCreateWorkspace();
    }

    public function update(User $user, Workspace $workspace)
    {
        return $user->id === $workspace->user_id;
    }

    public function delete(User $user, Workspace $workspace)
    {
        return $user->id === $workspace->user_id;
    }

    public function export(User $user, Workspace $workspace)
    {
        return $user->id === $workspace->user_id && 
               $user->canExportData();
    }
}