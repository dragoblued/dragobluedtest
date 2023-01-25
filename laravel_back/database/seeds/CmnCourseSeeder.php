<?php

use Illuminate\Database\Seeder;

class CmnCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cmn-courses')->insert([
            'course_id'  => 1,
            'route'      => 'basic',
            'name'       => 'basic',
            'title'      => 'Basic',
            'sub_title'  => 'My first implant. How to start with confidence',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. ',
            'created_at' => now()
        ]);
        DB::table('cmn-courses')->insert([
            'course_id'  => 2,
            'event_id'   => 1,
            'route'		 => 'module1',
            'name'		 => 'module1',
            'title'		 => 'Module 1',
            'sub_title'  => 'Bone and Soft Tissue Augmentation',
            'subsign'    => 'posterior mendible',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. ',
            'created_at' => now()
        ]);
        DB::table('cmn-courses')->insert([
            'course_id'  => 3,
            'event_id'   => 2,
            'route'		 => 'module2',
            'name'		 => 'module2',
            'title'		 => 'Module 2',
            'sub_title'  => 'Implants in Aesthetic Area',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. ',
            'created_at' => now()
        ]);
        DB::table('cmn-courses')->insert([
            'course_id'  => 4,
            'event_id'   => 3,
            'route'		 => 'module3',
            'name'		 => 'module3',
            'title'		 => 'Module 3',
            'sub_title'  => 'Soft Tissue Management. Bone Splitting.',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. ',
            'created_at' => now()
        ]);
        DB::table('cmn-courses')->insert([
            'course_id'  => 5,
            'route'      => 'peri-implantitis',
            'name'       => 'peri-implantitis',
            'title'      => 'Peri-implantitis',
            'sub_title'  => 'Prevention and treatment',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. ',
            'created_at' => now()
        ]);
    }
}
