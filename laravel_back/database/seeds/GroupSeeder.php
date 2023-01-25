<?php

use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groupes')->insert([
			'value'		 => 'CHAT_NOTIFIES',
			'name'		 => 'Chat notifications',
			'description'=> 'User will get an email notification every time a new message appears in any public chat',
			'created_at' => now()
		]);
        DB::table('groupes')->insert([
            'value'		 => 'FEEDBACK_NOTIFIES',
            'name'		 => 'Feedback notifications',
            'description'=> 'User will get an email notification every time a new feedback sends from "Contact-us" block',
            'created_at' => now()
        ]);
        DB::table('groupes')->insert([
            'value'		 => 'VIDEO_CONVERTED_NOTIFIES',
            'name'		 => 'Video converted notifications',
            'description'=> 'User who runs a video converting process will get an email notification when converting is over',
            'created_at' => now()
        ]);
    }
}
