<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MessageSeeder extends Seeder
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
            $file = database_path('seeds/site_seeds/messages.json');
            $data = File::get($file);

            $data = json_decode($data, true);

            foreach ($data as $value) {
                if (isset($value['new'])) {
                    $value['status'] = $value['new'] === 0 ? 1 : 0;
                    unset($value['new']);
                }
                DB::table('messages')->insert($value);
            }
        } else {
            DB::table('messages')->insert([
                'room_id' => 1,
                'user_id' => 2,
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam interdum sagittis pellentesque justo donec etiam diam neque purus?',
                'created_at' => now()
            ]);
            DB::table('messages')->insert([
                'room_id' => 1,
                'user_id' => 3,
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam interdum sagittis pellentesque justo donec etiam diam neque purus. Duis enim tempus consectetur consectetur nulla integer eget proin. Et imperdiet enim tortor tempus, tincidunt blandit ac amet. Purus vel nibh enim, non, facilisi suscipit imperdiet vulputate. Non.',
                'created_at' => now()
            ]);
            DB::table('messages')->insert([
                'room_id' => 1,
                'user_id' => 3,
                'link' => 1,
                'text' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit?',
                'created_at' => now()
            ]);
            DB::table('messages')->insert([
                'room_id' => 1,
                'user_id' => 2,
                'link' => 1,
                'text' => 'Nullam interdum sagittis pellentesque justo donec etiam diam neque purus.',
                'created_at' => now()
            ]);
        }
    }
}
