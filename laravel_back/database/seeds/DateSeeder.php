<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DateSeeder extends Seeder
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
          $file = database_path('seeds/site_seeds/dates.json');
          $data = File::get($file);

          $data = json_decode($data)[2]->data;

          foreach ($data as $value) {
             DB::table('dates')->insert(
                ((array) $value)
             );
          }
       } else {
          DB::table('dates')->insert([
             'year' => '2021',
             'start' => '2021-11-01',
             'end' => '2021-11-03',
             'lang' => 'Russian',
             'seats_total' => 20,
             'seats_vacant' => 10,
             'event_id' => 1,
             'created_at' => now()
          ]);
          DB::table('dates')->insert([
             'year' => '2021',
             'start' => '2021-12-11',
             'end' => '2021-12-13',
             'lang' => 'Lithuanian',
             'seats_total' => 10,
             'seats_vacant' => 10,
             'event_id' => 1,
             'created_at' => now()
          ]);
          DB::table('dates')->insert([
             'year' => '2021',
             'start' => '2021-05-01',
             'end' => '2021-05-03',
             'lang' => 'English',
             'seats_total' => 20,
             'seats_vacant' => 10,
             'event_id' => 1,
             'created_at' => now()
          ]);

          DB::table('dates')->insert([
             'year' => '2022',
             'start' => '2022-06-24',
             'end' => '2022-06-26',
             'lang' => 'English',
             'seats_total' => 10,
             'seats_vacant' => 3,
             'event_id' => 1,
             'created_at' => now()
          ]);

          DB::table('dates')->insert([
             'year' => '2021',
             'start' => '2021-06-24',
             'end' => '2021-06-26',
             'lang' => 'English',
             'seats_total' => 10,
             'seats_vacant' => 3,
             'event_id' => 1,
             'created_at' => now()
          ]);

          DB::table('dates')->insert([
             'year' => '2021',
             'start' => '2021-12-20',
             'end' => '2021-12-21',
             'lang' => 'Lithuanian',
             'seats_total' => 30,
             'seats_vacant' => 1,
             'event_id' => 2,
             'created_at' => now()
          ]);
          DB::table('dates')->insert([
             'year' => '2021',
             'start' => '2021-02-20',
             'end' => '2021-02-21',
             'lang' => 'Lithuanian',
             'seats_total' => 30,
             'seats_vacant' => 1,
             'event_id' => 2,
             'created_at' => now()
          ]);

          DB::table('dates')->insert([
             'year' => '2021',
             'start' => '2021-12-05',
             'end' => '2021-12-06',
             'lang' => 'Lithuanian',
             'seats_total' => 30,
             'seats_vacant' => 1,
             'event_id' => 3,
             'created_at' => now()
          ]);
          DB::table('dates')->insert([
             'year' => '2021',
             'start' => '2021-04-05',
             'end' => '2021-04-06',
             'lang' => 'English',
             'seats_total' => 10,
             'seats_vacant' => 5,
             'event_id' => 3,
             'created_at' => now()
          ]);
       }
    }
}
