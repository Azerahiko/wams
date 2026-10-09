<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $adminPermissions = [
            'manage-users',
            'manage-clients',
            'manage-projects',
            'manage-tasks',
            'view-reports',
            'edit-settings',
            'manage-staff',
            'view-audit-logs',
        ];

        $staffPermissions = [
            'manage-own-projects',
            'view-clients',
            'view-tasks',
            'update-task-status',
            'view-reports',
        ];

        $clientPermissions = [
            'view-own-projects',
            'view-own-tasks',
            'download-files',
            'view-invoices',
        ];

        // Create permissions
        foreach (array_merge($adminPermissions, $staffPermissions, $clientPermissions) as $permissionName) {
            Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // Create roles
        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'web',
            'description' => 'Full administrative access to the system',
        ]);

        $staffRole = Role::create([
            'name' => 'staff',
            'guard_name' => 'web',
            'description' => 'Operational staff with limited administrative access',
        ]);

        $clientRole = Role::create([
            'name' => 'client',
            'guard_name' => 'web',
            'description' => 'Client-facing access to their own resources',
        ]);

        // Assign admin permissions
        foreach ($adminPermissions as $permission) {
            \DB::table('role_has_permissions')->insert([
                'role_id' => $adminRole->id,
                'permission_id' => Permission::where('name', $permission)->value('id'),
            ]);
        }

        // Assign staff permissions
        foreach ($staffPermissions as $permission) {
            \DB::table('role_has_permissions')->insert([
                'role_id' => $staffRole->id,
                'permission_id' => Permission::where('name', $permission)->value('id'),
            ]);
        }

        // Assign client permissions
        foreach ($clientPermissions as $permission) {
            \DB::table('role_has_permissions')->insert([
                'role_id' => $clientRole->id,
                'permission_id' => Permission::where('name', $permission)->value('id'),
            ]);
        }
    }
}
