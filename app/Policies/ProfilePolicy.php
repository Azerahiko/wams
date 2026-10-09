<?php

namespace App\Policies;

use App\Models\User;

class ProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $profileUser): bool
    {
        return $profileUser->id === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $profileUser): bool
    {
        // Users can always update their own profile
        // Admins can update any profile
        // Staff can update profiles (but not change roles)
        return $profileUser->id === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $profileUser): bool
    {
        return $user->hasRole('admin') && $profileUser->id !== $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $profileUser): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $profileUser): bool
    {
        return $user->hasRole('admin') && $profileUser->id !== $user->id;
    }
}
