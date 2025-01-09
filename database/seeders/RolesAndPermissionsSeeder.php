<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_institutions',
            'create_institutions',
            'edit_institutions',
            'delete_institutions',
            'view_courses',
            'create_courses',
            'edit_courses',
            'delete_courses',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and assign permissions
        $roles = [
            'super_agent' => [
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'view_institutions',
                'create_institutions',
                'edit_institutions',
                'delete_institutions',
            ],
            'agent' => [
                'view_users',
                'view_institutions',
            ],
            'student' => [
                'view_courses',
            ],
            'university' => [
                'view_institutions',
                'create_institutions',
                'edit_institutions',
                'view_courses',
            ],
            'staff' => [
                'view_courses',
                'edit_courses',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
