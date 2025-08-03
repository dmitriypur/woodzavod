<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
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
        // Принудительное использование HTTPS в production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
        
        // Глобальная переменная settings для всех шаблонов
        View::share('settings', app(GeneralSettings::class));

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
