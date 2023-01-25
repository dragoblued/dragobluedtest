<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TicketSeeder extends Seeder
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
          $file = database_path('seeds/site_seeds/tickets.json');
          $data = File::get($file);

          $data = json_decode($data)[2]->data;

          foreach ($data as $value) {
             DB::table('tickets')->insert(
                ((array) $value)
             );
          }
       }
    }
}
