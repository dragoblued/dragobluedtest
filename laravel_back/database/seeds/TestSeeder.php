<?php

use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tests')->insert([
            'title'	            => 'Overall Module 1 Test',
            'course_id'	        => 1,
            'duration'	        => 60,
            'minimum_percentage'=> 80,
            'instruction'	    => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.',
            'permitted_attempt_number' => -1,
            'total_mark'        => 30,
            'status'	        => 'published',
            'created_at'	    => now(),
        ]);

        DB::table('tests')->insert([
            'title'	            => 'Overall Module 2 Test',
            'course_id'	        => 2,
            'duration'	        => 30,
            'minimum_percentage'=> 80,
            'instruction'	    => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.',
            'permitted_attempt_number' => 3,
            'total_mark'        => 30,
            'status'	        => 'published',
            'created_at'	    => now(),
        ]);
    }
}
