<?php

namespace App\Policies;

use App\Models\User;

class SecurityPolicy
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
     * Determine whether the user can change the password.
     */
    public function updatePassword(User $user, User $profileUser): bool
    {
        // Any authenticated user can change their own password
        // Admins can change any user's password
        return $profileUser->id === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update API tokens.
     */
    public function updateApiToken(User $user, User $profileUser): bool
    {
        return $profileUser->id === $user->id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete API tokens.
     */
    public function deleteApiToken(User $user, User $profileUser): bool
    {
        return $user->hasRole('admin') && $profileUser->id !== $user->id;
    }

    /**
     * Determine whether the user can view security logs.
     */
    public function viewSecurityLogs(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can change security settings.
     */
    public function updateSecuritySettings(User $user): bool
    {
        return $user->hasRole('admin');
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
