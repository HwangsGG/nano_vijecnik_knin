<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'city-store',
            'city-update',
            'city-delete',
            'user-delete',
            'user-edit',
            'user-direct-permission',
            'user-reset-password',

        ];

        $roles = Role::pluck('id','id')->all();

        foreach ($permissions as $permission) {
            $per = Permission::create([
                'name' => $permission
            ]);

            $per->assignRole($roles);
        }
    }
}
