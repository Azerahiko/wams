<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string|array>
     */
    protected $policies = [
        User::class => App\Policies\UserPolicy::class,
        App\Models\User::class => [
            'view' => App\Policies\SettingsPolicy::class,
            'update' => App\Policies\SettingsPolicy::class,
            'create' => App\Policies\SettingsPolicy::class,
            'delete' => App\Policies\SettingsPolicy::class,
            'viewAny' => App\Policies\SettingsPolicy::class,
            'edit' => App\Policies\ProfilePolicy::class,
            'updatePassword' => App\Policies\SecurityPolicy::class,
            'updateApiToken' => App\Policies\SecurityPolicy::class,
            'deleteApiToken' => App\Policies\SecurityPolicy::class,
            'viewSecurityLogs' => App\Policies\SecurityPolicy::class,
            'updateSecuritySettings' => App\Policies\SecurityPolicy::class,
        ],
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPermissionsGates();
    }

    /**
     * Register role-based permission gates.
     */
    protected function registerPermissionsGates(): void
    {
        Gate::define('admin', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('staff', function (User $user) {
            return $user->hasRole('staff');
        });

        Gate::define('client', function (User $user) {
            return $user->hasRole('client');
        });

        // Ability-based gates for specific permissions
        Gate::define('manage-users', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-clients', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-projects', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('staff');
        });

        Gate::define('manage-tasks', function (User $user) {
            return $user->hasRole('admin') || ($user->hasRole('staff') && $user->can('update-task-status'));
        });

        Gate::define('view-reports', function (User $user) {
            return $user->hasAnyRole(['admin', 'staff']);
        });

        Gate::define('edit-settings', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-staff', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('view-audit-logs', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-own-projects', function (User $user) {
            return $user->hasRole('staff') || $user->hasRole('client');
        });

        Gate::define('view-clients', function (User $user) {
            return $user->hasAnyRole(['admin', 'staff', 'client']);
        });

        Gate::define('update-task-status', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('staff');
        });

        Gate::define('view-own-projects', function (User $user) {
            return $user->hasRole('client') || $user->hasAnyRole(['admin', 'staff']);
        });

        Gate::define('view-own-tasks', function (User $user) {
            return $user->hasRole('client') || $user->hasAnyRole(['admin', 'staff']);
        });

        Gate::define('download-files', function (User $user) {
            return $user->hasRole('client') || $user->hasAnyRole(['admin', 'staff']);
        });

        Gate::define('view-invoices', function (User $user) {
            return $user->hasRole('client') || $user->hasAnyRole(['admin', 'staff']);
        });
    }
}
