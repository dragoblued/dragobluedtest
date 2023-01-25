<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $env = Config::get('app.env');
//        $env = 'development';
        if ($env !== 'local') {
            /* Site Seeds */
            $file = database_path('seeds/site_seeds/courses.json');
            $data = File::get($file);

            $data = json_decode($data)[2]->data;

            foreach ($data as $value) {
                DB::table('courses')->insert(
                    ((array) $value)
                );
            }
        } else {
            DB::table('courses')->insert([
                'route' => 'basic',
                'name' => 'basic',
                'status' => 'coming-soon',
                'title' => 'Basic',
                'sub_title' => 'My first implant. How to start with confidence',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. ',
                'poster_url' => 'media/courses/module1/module1_poster.jpg',
                'promo_video_url' => 'media/courses/module1/module1_promo.mp4',
                'promo_video_available_formats' => '["360p","480p","720p"]',
                'converted' => 1,
                'topics_count' => 2,
                'lessons_count' => 5,
                'total_lessons_duration' => 7460,
                'actual_price' => 900,
                'discount_price' => 600,
                'created_at' => now()
            ]);
            DB::table('courses')->insert([
                'route' => 'module1',
                'name' => 'module1',
                'status' => 'published',
                'title' => 'Module 1',
                'sub_title' => 'Bone and Soft Tissue Augmentation',
                'subsign' => 'posterior mendible',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. ',
                'poster_url' => 'media/courses/module1/module1_poster.jpg',
                'promo_video_url' => 'media/courses/module1/module1_promo.mp4',
                'promo_video_available_formats' => '["360p","480p","720p"]',
                'converted' => 1,
                'topics_count' => 1,
                'lessons_count' => 15,
                'total_lessons_duration' => 10460,
                'actual_price' => 1000,
                'discount_price' => 800,
                'created_at' => now()
            ]);
            DB::table('courses')->insert([
                'route' => 'module2',
                'name' => 'module2',
                'status' => 'coming-soon',
                'title' => 'Module 2',
                'sub_title' => 'Implants in Aesthetic Area',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. ',
                'poster_url' => 'media/courses/module2/module2_poster.jpg',
                'promo_video_url' => 'media/courses/module1/module1_promo.mp4',
                'promo_video_available_formats' => '["360p","480p","720p"]',
                'converted' => 1,
                'topics_count' => 1,
                'lessons_count' => 1,
                'total_lessons_duration' => 3600,
                'actual_price' => 500,
                'created_at' => now()
            ]);
            DB::table('courses')->insert([
                'route' => 'module3',
                'name' => 'module3',
                'status' => 'coming-soon',
                'title' => 'Module 3',
                'sub_title' => 'Soft Tissue Management. Bone Splitting.',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. ',
                'poster_url' => 'media/courses/module3/module3_poster.jpg',
                'promo_video_url' => 'media/courses/module1/module1_promo.mp4',
                'promo_video_available_formats' => '["360p","480p","720p"]',
                'converted' => 1,
                'topics_count' => 0,
                'lessons_count' => 0,
                'total_lessons_duration' => 0,
                'actual_price' => 1500,
                'discount_price' => 900,
                'created_at' => now()
            ]);
            DB::table('courses')->insert([
                'route' => 'peri-implantitis',
                'name' => 'peri-implantitis',
                'status' => 'coming-soon',
                'title' => 'Peri-implantitis',
                'sub_title' => 'Prevention and treatment',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. ',
                'poster_url' => 'media/courses/module1/module1_poster.jpg',
                'promo_video_url' => 'media/courses/module1/module1_promo.mp4',
                'promo_video_available_formats' => '["360p","480p","720p"]',
                'converted' => 0,
                'topics_count' => 3,
                'lessons_count' => 12,
                'total_lessons_duration' => 18460,
                'actual_price' => 1200,
                'created_at' => now()
            ]);
        }
    }
}
