<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Create permissions
        $taskCreatePermission = Permission::create(['name' => 'Create a new task']);
        $taskUpdatePermission = Permission::create(['name' => 'Update a task']);

        //Create manager role
        $managerRole = Role::create(['name' => 'Manager']);

        //Assign manager's permissions to manager role

        $managerRole->givePermissionTo([
            $taskCreatePermission,
            $taskUpdatePermission,
        ]);

        //Create user role
         Role::create(['name' => 'User']);

    }
}
