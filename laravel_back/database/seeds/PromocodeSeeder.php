<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class PromocodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $env = Config::get('app.env');
        $env = 'development';
       if ($env !== 'local') {
          /* Site Seeds */
          $file = database_path('seeds/site_seeds/promocodes.json');
          $data = File::get($file);

          $data = json_decode($data);

          foreach ($data as $value) {
             $value = (array) $value;
             DB::table('promocodes')->insert(
                ((array) $value)
             );
          }
       } else {
          DB::table('promocodes')->insert([
             'code' => 'FXWEMNPO',
             'discount_type' => 'percent',
             'discount' => 5,
             'start_at' => date('Y-m-d'),
             'end_at' => date('Y-m-d', strtotime('30 days')),
             'created_at' => now()
          ]);
       }
    }
}
