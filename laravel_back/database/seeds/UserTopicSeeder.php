<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UserTopicSeeder extends Seeder
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
         $file = database_path('seeds/site_seeds/user_topic.json');
         $data = File::get($file);

         $data = json_decode($data)[2]->data;

         foreach ($data as $value) {
            DB::table('user_topic')->insert(
               ((array) $value)
            );
         }
      } else {
         DB::table('user_topic')->insert([
            'user_id' => 2,
            'topic_id' => 1,
            'is_purchased' => true,
            'created_at' => now()
         ]);
      }
   }
}
