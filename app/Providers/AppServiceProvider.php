<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\AppSetting;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot()
  {
    if (AppSetting::count() === 0) {
        AppSetting::create([
            'app_name' => 'Default App Name',
            'logo_url' => 'https://default.url/logo.png',
            'languages' => json_encode(['en']),
        ]);
    }
  }
}
