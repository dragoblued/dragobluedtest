<?php

use Illuminate\Database\Seeder;

use App\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create([
            'name'       => 'Admin',
            'created_at' => now()
        ]);
        foreach ([1,2,3,4,5,6,7,8] as $permissionId) {
            $admin->permissions()->attach($permissionId);
        }

        $user = Role::create([
            'name'       => 'User',
            'created_at' => now()
        ]);
        foreach ([1,2] as $permissionId) {
            $user->permissions()->attach($permissionId);
        }
    }
}
