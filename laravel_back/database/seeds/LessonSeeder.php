<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LessonSeeder extends Seeder
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
            $file = database_path('seeds/site_seeds/lessons.json');
            $data = File::get($file);

           $data = json_decode($data)[2]->data;

           foreach ($data as $value) {
                DB::table('lessons')->insert(
                    ((array) $value)
                );
           }

//           $data1 = json_decode($data);
//           $data1[2]->data = $data;
//           File::put(database_path('seeds/site_seeds/lessons.json'), json_encode($data1));
        } else {
            /* Local Seeds */
            DB::table('lessons')->insert([
                'route' => 'basic-lesson',
                'name' => 'basic-lesson',
                'title' => 'Basic lesson',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/beginning/beginning_poster.jpg',
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'topic_id' => 1,
                'route' => 'beginning',
                'name' => 'beginning',
                'title' => 'Beginning',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/beginning/beginning_poster.jpg',
                'video_url' => 'media/lessons/beginning/beginning.mp4',
                'video_available_formats' => '["240p","360p","480p","720p"]',
                'promo_video_url' => 'media/lessons/beginning/beginning_promo.mp4',
                'promo_video_available_formats' => '["240p","360p","480p","720p"]',
                'converted' => 1,
                'is_free' => 1,
                'video_duration' => 166,
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'topic_id' => 1,
                'route' => 'extraction',
                'name' => 'extraction',
                'title' => 'Extraction',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/extraction/extraction_poster.jpg',
                'video_url' => 'media/lessons/extraction/extraction.mp4',
                'video_available_formats' => '["240p","360p"]',
                'promo_video_url' => 'media/lessons/extraction/extraction_promo.mp4',
                'promo_video_available_formats' => '["240p","360p"]',
                'converted' => 1,
                'video_duration' => 142,
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'topic_id' => 1,
                'route' => '3d-position',
                'name' => '3d-position',
                'title' => '3D position',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/3d-position/3d-position_poster.jpg',
                'video_url' => 'media/lessons/3d-position/3d-position.mp4',
                'video_available_formats' => '["240p","360p"]',
                'promo_video_url' => 'media/lessons/3d-position/3d-position_promo.mp4',
                'promo_video_available_formats' => '["240p","360p"]',
                'converted' => 1,
                'video_duration' => 102,
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'topic_id' => 2,
                'route' => 'implants-type',
                'name' => 'implants-type',
                'title' => 'Implants type',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/beginning/beginning_poster.jpg',
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'topic_id' => 2,
                'route' => 'ha-temproraty',
                'name' => 'ha-temproraty',
                'title' => 'HA temproraty',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/beginning/beginning_poster.jpg',
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'topic_id' => 2,
                'route' => 'to-graft-or-not-premolar',
                'name' => 'to-graft-or-not-premolar',
                'title' => 'To graft or not: Premolar',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/beginning/beginning_poster.jpg',
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'topic_id' => 3,
                'route' => 'to-graft-or-not-molar',
                'name' => 'to-graft-or-not-molar',
                'title' => 'To graft or not: Molar',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/beginning/beginning_poster.jpg',
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'topic_id' => 3,
                'route' => 'individual-zro',
                'name' => 'individual-zro',
                'title' => 'Individual ZRO',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/beginning/beginning_poster.jpg',
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'topic_id' => 3,
                'route' => 'wide-implants',
                'name' => 'wide-implants',
                'title' => 'Wide implants',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/beginning/beginning_poster.jpg',
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'topic_id' => 3,
                'route' => 'complications',
                'name' => 'complications',
                'title' => 'Complications',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/beginning/beginning_poster.jpg',
                'created_at' => now()
            ]);
            DB::table('lessons')->insert([
                'route' => 'analysis',
                'name' => 'analysis',
                'title' => 'Analysis',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Viverra pellentesque nulla nulla mauris, odio pretium sapien. Sit sit malesuada felis nunc viverra a viverra. Nulla posuere feugiat faucibus sit lorem scelerisque interdum sed. Tellus elit odio gravida metus, nisl, nisi, adipiscing.
Amet, consequat, augue lacus, pulvinar in imperdiet et, amet. Eu consequat duis integer eu egestas. Massa ultrices condimentum sit aliquam nibh turpis aliquet libero. Convallis tincidunt eu at mi. Ipsum, eget sit tristique leo et nunc. Lorem lectus aenean mauris ipsum tortor et potenti. Amet purus nibh elit, praesent donec a nec. Cras lacus nibh egestas in sagittis, pulvinar ac nibh.
Sed tincidunt iaculis lectus massa porttitor sit ut. Purus eget consequat at.',
                'poster_url' => 'media/lessons/beginning/beginning_poster.jpg',
                'created_at' => now()
            ]);
        }
    }
}
