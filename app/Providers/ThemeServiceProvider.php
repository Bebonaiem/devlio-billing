¿<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $theme = config('settings.theme', 'default');
        $themePath = base_path("themes/{$theme}");

        if (is_dir($themePath)) {
            View::addNamespace('theme', $themePath);

            $viewsPath = $themePath.'/views';
            if (is_dir($viewsPath)) {
                View::addNamespace('theme-views', $viewsPath);
            }

            $componentsPath = $themePath.'/components';
            if (is_dir($componentsPath)) {
                View::addNamespace('theme-components', $componentsPath);
            }
        }
    }
}
