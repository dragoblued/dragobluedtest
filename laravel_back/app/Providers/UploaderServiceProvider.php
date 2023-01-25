<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Uploader;

class UploaderServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		//
	}

	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('Uploader', function($app)
		{
			return new Uploader($app['request']);
		});
	}
}
