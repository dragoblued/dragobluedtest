<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GallerySeeder extends Seeder
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
          $file = database_path('seeds/site_seeds/gallery.json');
          $data = File::get($file);

          $data = json_decode($data)[2]->data;

          foreach ($data as $value) {
             DB::table('gallery')->insert(
                ((array) $value)
             );
          }
       } else {
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'certificate',
             'url' => 'media/gallery/1/1.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'certificate',
             'url' => 'media/gallery/2/2.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'video',
             'url' => 'media/gallery/3/3.mp4',
             'poster_url' => 'media/gallery/3/3_poster.jpg',
             'available_formats' => '["360p","480p","720p"]',
             'converted' => true,
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'certificate',
             'url' => 'media/gallery/4/4.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'certificate',
             'url' => 'media/gallery/5/5.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'certificate',
             'url' => 'media/gallery/6/6.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'certificate',
             'url' => 'media/gallery/7/7.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'certificate',
             'url' => 'media/gallery/8/8.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'certificate',
             'url' => 'media/gallery/9/9.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'gallery item',
             'url' => 'media/gallery/10/10.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'gallery item',
             'url' => 'media/gallery/11/11.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'image',
             'name' => 'gallery item',
             'url' => 'media/gallery/12/12.jpg',
             'created_at' => now()
          ]);
          DB::table('gallery')->insert([
             'type' => 'video',
             'url' => 'media/gallery/13/13.mp4',
             'available_formats' => '["360p","480p","720p","1080p"]',
             'converted' => true,
             'created_at' => now()
          ]);
       }
    }
}
