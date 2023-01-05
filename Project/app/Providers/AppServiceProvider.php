<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
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
      if(env('FORCE_HTTPS',false)) {
        error_log('configuring https');
        $app_url = config("app.url");
        URL::forceRootUrl($app_url);
        $schema = explode(':', $app_url)[0];
        URL::forceScheme($schema);
      }
    }
}
