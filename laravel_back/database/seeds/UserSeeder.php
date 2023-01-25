<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\User;

class UserSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
      $env = Config::get('app.env');
//      $env = 'development';
      if ($env !== 'local') {
         /* Site Seeds */
         $file = database_path('seeds/site_seeds/users.json');
         $data = File::get($file);

         $data = json_decode($data)[2]->data;

         foreach ($data as $value) {
            $value = (array) $value;
            unset($value['session_id']);
            unset($value['second_session_id']);
            DB::table('users')->insert($value);
         }
      } else {
         $password = Config::get('app.env') === 'local' ? 123456 : '*Secured_P@ssw0rd*';
         User::create([
            'active' => true,
            'login' => 'ROOT',
            'email' => 'root@celado.ru',
            'password' => Hash::make($password),
            'api_token' => Str::random(60),
            'role_id' => 1,
            'created_at' => now()
         ]);
         User::create([
            'active' => true,
            'login' => 'admin',
            'email' => 'admin@celado.ru',
            'password' => Hash::make($password),
            'api_token' => Str::random(60),
            'name' => 'Admin',
            'avatar_url' => 'media/users/admin/admin-avatar.jpg',
            'role_id' => 1,
            'created_at' => now()
         ]);
         User::create([
            'active' => false,
            'login' => 'user',
            'email' => 'user@celado.ru',
            'password' => Hash::make($password),
            'api_token' => Str::random(60),
            'name' => 'User',
            'role_id' => 2,
            'created_at' => now()
         ]);
         User::create([
            'active' => true,
            'login' => 'artem',
            'email' => 'a.bugriy@celado-media.ru',
            'password' => Hash::make($password),
            'api_token' => Str::random(60),
            'name' => 'Artem',
            'role_id' => 1,
            'created_at' => now()
         ]);
      }
   }
}
