<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class SettingSeeder extends Seeder
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
          $file = database_path('seeds/site_seeds/settings.json');
          $data = File::get($file);

          $data = json_decode($data)[2]->data;

          foreach ($data as $value) {
             DB::table('settings')->insert(
                ((array)$value)
             );
          }
       } else {
          DB::table('settings')->insert([
             'key' => 'about_us',
             'value' => 'Lorem ipsum, dolor, sit amet consectetur adipisicing elit. Alias, atque sint consectetur aperiam nihil quisquam, sunt ab. Modi, alias, vel sapiente et totam tempore quaerat suscipit qui maiores animi! Laudantium?',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'terms_conditions',
             'value' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source.<br><h3>Something interesting about our school',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'privacy_policy',
             'value' => '<p style="margin-bottom: .5rem;">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.</p><ul style="list-style: disc; margin-bottom: 2rem; padding-left: 2rem;">List:<li>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\',</li><li>making it look like readable English.</li><li>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.</li></ul><p style="margin-bottom: .5rem;">The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English.</p>',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'social_links',
             'value' => '[
			 {"id":"1","name":"facebook","url":"http://facebook.com","icon":"facebook"}
			,{"id":"5","name":"youtube","url":"http://youtube.com","icon":"youtube"}
			,{"id":"6","name":"instagram","url":"http://instagram.com","icon":"instagram"}
			]',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'currency',
             'value' => '[
            	{"code":"eur","name":"EUR","sign":"€","selected":true},
            	{"code":"usd","name":"USD","sign":"$","selected":false}
            ]',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'title',
             'value' => 'Algirdas Puisys',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'logo',
             'value' => 'logo.png',
             'created_at' => now()
          ]);

          //Сделать раздел меню в settings с картой
          DB::table('settings')->insert([
             'key' => 'location_coordinates',
             'value' => '[25.270462,54.679921]',
             'created_at' => now()
          ]);
          // type=hidden
          DB::table('settings')->insert([
             'key' => 'location_url',
             'value' => 'https://yandex.com/maps/11475/vilnius/house/ZkwYdABgTUMFQFtufXp2eHVhZQ==/?ll=25.270462%2C54.679921&source=wizbiz_new_map_multi&z=17',
             'created_at' => now()
          ]);
          //visible readonly
          DB::table('settings')->insert([
             'key' => 'address',
             'value' => 'A. Vivulskio str. 7, Vilnius, Lithuania 03162',
             'created_at' => now()
          ]);
          //visible readonly
          DB::table('settings')->insert([
             'key' => 'address_building_name',
             'value' => 'VIC clinic',
             'created_at' => now()
          ]);
          //visible editable


          DB::table('settings')->insert([
             'key' => 'phone',
             'value' => '+37052760725',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'phone_live_courses',
             'value' => '+37063552464',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'email',
             'value' => 'viktorija.auzbikaviciute@vicklinika.lt',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'email_live_courses',
             'value' => 'mokymai@medgrupe.lt',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'email_video_courses',
             'value' => 'algirdas@vicklinika.lt',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'copyright_text',
             'value' => '© Algirdas Puisys',
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'recaptcha_site_key',
             'value' => null,
             'created_at' => now()
          ]);
          DB::table('settings')->insert([
             'key' => 'email_newsletter',
             'value' => 'Do you have any questions about the work of “Algirdas Puisys Online School”? Write to our support service.',
             'created_at' => now()
          ]);
       }
    }
}
