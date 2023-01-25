<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UserCourseSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
//       $env = Config::get('app.env');
// //        $env = 'development';
//       if ($env !== 'local') {
//          /* Site Seeds */
//          $file = database_path('seeds/site_seeds/user_course.json');
//          $data = File::get($file);

//          $data = json_decode($data)[2]->data;

//          foreach ($data as $value) {
//             DB::table('user_course')->insert(
//                ((array) $value)
//             );
//          }
//       } else {
//          DB::table('user_course')->insert([
//             'user_id' => 2,
//             'course_id' => 1,
//             'is_purchased' => true,
//             'created_at' => now()
//          ]);
//          DB::table('user_course')->insert([
//             'user_id' => 2,
//             'course_id' => 2,
//             'is_purchased' => true,
//             'created_at' => now()
//          ]);
//       }
         DB::table('user_course')->insert([
            'user_id' => 8,
            'course_id' => 2,
            'is_purchased' => true,
            'created_at' => now()
         ]);
   }
}
