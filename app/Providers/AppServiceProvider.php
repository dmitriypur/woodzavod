<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $settings = app(GeneralSettings::class);
            $view->with('settings', $settings);
        });

        // Blade директива для замены переменных настроек
        Blade::directive('settings', function ($expression) {
            return "<?php echo App\\Helpers\\SettingsHelper::replaceVariables($expression); ?>";
        });

        // Blade директива для телефонных ссылок
        Blade::directive('phoneLink', function ($expression) {
            return "<?php echo '<a href=\"tel:' . App\\Helpers\\SettingsHelper::phoneDigitsOnly($expression) . '\">' . $expression . '</a>'; ?>";
        });
    }
}
