<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class TopicSeeder extends Seeder
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
            $file = database_path('seeds/site_seeds/topics.json');
            $data = File::get($file);

            $data = json_decode($data)[2]->data;

            foreach ($data as $value) {
                DB::table('topics')->insert(
                    ((array) $value)
                );
            }
        } else {
            /* Local Seeds */
            DB::table('topics')->insert([
                'course_id'  => 2,
                'route'      => 'imediate',
                'name'       => 'imediate',
                'title'      => 'Imediate',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut condimentum arcu tortor, sollicitudin feugiat. Diam in sagittis, volutpat commodo lectus ornare. In mi venenatis ornare sed.',
                'poster_url' => 'media/topics/imediate/imediate_poster.jpg',
                'promo_video_url' => 'media/gallery/3/3.mp4',
                'promo_video_available_formats' => '["360p","480p","720p"]',
                'converted' => 1,
                'lessons_count' => 3,
                'total_lessons_duration' => 245,
                'actual_price' => 100,
                'created_at' => now()
            ]);
            DB::table('topics')->insert([
                'course_id'  => 2,
                'route'      => 'regular-implant-placement',
                'name'       => 'regular implant placement',
                'title'      => 'Regular implant placement',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut condimentum arcu tortor, sollicitudin feugiat. Diam in sagittis, volutpat commodo lectus ornare. In mi venenatis ornare sed.',
                'poster_url' => 'media/topics/imediate/imediate_poster.jpg',
                'promo_video_url' => 'media/gallery/3/3.mp4',
                'promo_video_available_formats' => '["360p","480p","720p"]',
                'converted' => 0,
                'lessons_count' => 3,
                'total_lessons_duration' => 245,
                'actual_price' => 100,
                'discount_price' => 80,
                'created_at' => now()
            ]);
            DB::table('topics')->insert([
                'course_id'  => 2,
                'route'      => 'lateral-bone-augmentation',
                'name'       => 'lateral bone augmentation',
                'title'      => 'Lateral bone augmentation',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut condimentum arcu tortor, sollicitudin feugiat. Diam in sagittis, volutpat commodo lectus ornare. In mi venenatis ornare sed.',
                'poster_url' => 'media/topics/imediate/imediate_poster.jpg',
                'promo_video_url' => 'media/gallery/3/3.mp4',
                'promo_video_available_formats' => '["360p","480p","720p"]',
                'converted' => 0,
                'lessons_count' => 4,
                'total_lessons_duration' => 545,
                'actual_price' => 120,
                'created_at' => now()
            ]);
        }
    }
}
