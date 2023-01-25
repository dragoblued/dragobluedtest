<?php

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'name'          => 'USER_EDIT',
            'description'   => 'Permission to edit user\'s account',
            'created_at'    => now()
        ]);
        DB::table('permissions')->insert([
            'name'          => 'USER_MESSAGING',
            'description'   => 'Permission to leave messages in chats',
            'created_at'    => now()
        ]);
		DB::table('permissions')->insert([
			'name'	        => 'ADMIN_VIEW_MAIN',
            'description'   => 'View General sections of the app admin panel',
			'created_at'	=> now()
		]);
        DB::table('permissions')->insert([
            'name'          => 'ADMIN_VIEW_ALL',
            'description'   => 'View all sections of the app\'s admin panel',
            'created_at'    => now()
        ]);
        DB::table('permissions')->insert([
            'name'          => 'ADMIN_CREATE',
            'description'   => 'Creating new items available from the admin panel',
            'created_at'    => now()
        ]);
        DB::table('permissions')->insert([
            'name'          => 'ADMIN_EDIT',
            'description'   => 'Editing content available from the admin panel',
            'created_at'    => now()
        ]);
        DB::table('permissions')->insert([
            'name'          => 'ADMIN_DELETE',
            'description'   => 'Removal of materials is available from the admin panel',
            'created_at'    => now()
        ]);
        DB::table('permissions')->insert([
            'name'          => 'DELETE_CHAT_MESSAGE',
            'description'   => 'Permission to delete any message in public chat',
            'created_at'    => now()
        ]);
    }
}
