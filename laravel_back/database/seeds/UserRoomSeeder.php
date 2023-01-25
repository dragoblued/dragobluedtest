<?php

use Illuminate\Database\Seeder;

class UserRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('user_room')->insert([
          'user_id'  => 2,
          'room_id'  => 1,
          'created_at' => now()
       ]);
       DB::table('user_room')->insert([
          'user_id'  => 3,
          'room_id'  => 1,
          'created_at' => now()
       ]);
    }
}
