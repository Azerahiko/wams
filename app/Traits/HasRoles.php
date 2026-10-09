<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    /**
     * The roles that are assigned to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'user_role',
            'user_id',
            'role_id'
        );
    }

    /**
     * Determine if the user has the given role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Determine if the user has any of the given roles.
     */
    public function hasAnyRole(array|string $roles): bool
    {
        $roles = (array) $roles;

        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Determine if the user has all of the given roles.
     */
    public function hasAllRoles(array|string $roles): bool
    {
        $roles = (array) $roles;

        return $this->roles()->whereIn('name', $roles)->count() === count($roles);
    }

    /**
     * Get all of the user's role names.
     */
    public function getRoleNames(): array
    {
        return $this->roles()->pluck('name')->toArray();
    }
}
