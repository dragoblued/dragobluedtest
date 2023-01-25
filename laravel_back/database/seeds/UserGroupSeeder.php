<?php

use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_group')->insert([
            'user_id'  => 4,
            'group_id'  => 1
        ]);
        DB::table('user_group')->insert([
            'user_id'  => 4,
            'group_id'  => 2
        ]);
        DB::table('user_group')->insert([
            'user_id'  => 4,
            'group_id'  => 3
        ]);
    }
}
