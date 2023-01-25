<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $env = config('app.env');
//        $env = 'development';
        if ($env !== 'local') {
            /* Site Seeds */
            $file = database_path('seeds/site_seeds/rooms.json');
            $data = File::get($file);

            $data = json_decode($data, true);

            foreach ($data as $value) {
                if (isset($value['lesson_id'])) {
                    $value['subject_id'] = $value['lesson_id'];
                    $value['subject_type'] = 'App\\Lesson';
                    unset($value['lesson_id']);
                }
                DB::table('rooms')->insert($value);
            }
        } else {
            DB::table('rooms')->insert([
                'subject_id' => 2,
                'subject_type' => 'App\\Lesson',
                'creator_id' => 3,
                'created_at' => now()
            ]);
        }
    }
}
