<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssetPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Asset $asset): bool
    {
        if ($user->roles->pluck('name')->intersect(['admin', 'supervisor'])->isNotEmpty()) {
            return true;
        }

        return $user->projects()->where('project_id', $asset->project_id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->roles->pluck('name')->intersect(['admin', 'supervisor', 'pentester'])->isNotEmpty();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Asset $asset): bool
    {
        if ($user->roles->pluck('name')->intersect(['admin', 'supervisor'])->isNotEmpty()) {
            return true;
        }

        return $user->roles->pluck('name')->contains('pentester') && $user->projects()->where('project_id', $asset->project_id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Asset $asset): bool
    {
        if ($user->roles->pluck('name')->intersect(['admin', 'supervisor'])->isNotEmpty()) {
            return true;
        }

        return $user->roles->pluck('name')->contains('pentester') && $user->projects()->where('project_id', $asset->project_id)->exists();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Asset $asset): bool
    {
        return $user->roles->pluck('name')->intersect(['admin', 'supervisor'])->isNotEmpty();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Asset $asset): bool
    {
        return $user->roles->pluck('name')->intersect(['admin', 'supervisor'])->isNotEmpty();
    }
}
