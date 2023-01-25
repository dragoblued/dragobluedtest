<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
   /**
    * The policy mappings for the application.
    *
    * @var array
    */
   protected $policies = [
      'App\Model' => 'App\Policies\ModelPolicy',
   ];

   /**
    * Register any application services.
    *
    * @return void
    */
   public function register()
   {
      //
   }

   /**
    * Bootstrap any application services.
    *
    * @return void
    */
   public function boot()
   {
      If (Config::get('app.env') !== 'local') {
         $this->app['request']->server->set('HTTPS', true);
      }
      Schema::defaultStringLength(191);

      $this->registerPolicies();

      Passport::routes();

   }
}
