<?php

use Illuminate\Database\Seeder;

class TestQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('test_questions')->insert([
            'test_id'	        => 1,
            'type'	            => 'multiple-choice',
            'title'	            => 'What color are the walls on the image: <br/> <img src="media/wisiwyg/tests/paintingbedroom.jpg" alt="">',
            'number_of_options'	=> 6,
            'options'	        => '["Black", "White", "Crimson", "Orange", "Chocolate", "None of the above"]',
            'correct_answers'	=> '[2,3]',
            'mark'	            => 10,
            'created_at'	    => now(),
        ]);
        DB::table('test_questions')->insert([
            'test_id'	        => 1,
            'type'	            => 'single-choice',
            'title'	            => 'A dentist is a person who treats teeth. Is this statement correct?',
            'number_of_options'	=> 6,
            'options'	        => '["Yes", "No", "Not exactly", "Not always", "None of the above"]',
            'correct_answers'	=> '[0]',
            'mark'	            => 5,
            'created_at'	    => now(),
        ]);
        DB::table('test_questions')->insert([
            'test_id'	        => 1,
            'type'	            => 'fill-in-the-blanks',
            'title'	            => 'Fill in the blanks.',
            'number_of_options'	=> 2,
            'options'	        => '2 + 2 = @@; <br/> 6 x @@ = 30',
            'correct_answers'	=> '[4, 5]',
            'mark'	            => 15,
            'created_at'	    => now(),
        ]);


        DB::table('test_questions')->insert([
            'test_id'	        => 2,
            'type'	            => 'multiple-choice',
            'title'	            => 'What color are the walls on the image: <br/> <img src="media/wisiwyg/tests/paintingbedroom.jpg" alt="">',
            'number_of_options'	=> 6,
            'options'	        => '["Black", "White", "Crimson", "Orange", "Chocolate", "None of the above"]',
            'correct_answers'	=> '[2,3]',
            'mark'	            => 10,
            'created_at'	    => now(),
        ]);
        DB::table('test_questions')->insert([
            'test_id'	        => 2,
            'type'	            => 'single-choice',
            'title'	            => 'A dentist is a person who treats teeth. Is this statement correct?',
            'number_of_options'	=> 6,
            'options'	        => '["Yes", "No", "Not exactly", "Not always", "None of the above"]',
            'correct_answers'	=> '[0]',
            'mark'	            => 5,
            'created_at'	    => now(),
        ]);
        DB::table('test_questions')->insert([
            'test_id'	        => 2,
            'type'	            => 'fill-in-the-blanks',
            'title'	            => 'Fill in the blanks.',
            'number_of_options'	=> 2,
            'options'	        => '2 + 2 = @@; <br/> 6 x @@ = 30',
            'correct_answers'	=> '[4, 5]',
            'mark'	            => 15,
            'created_at'	    => now(),
        ]);
    }
}
