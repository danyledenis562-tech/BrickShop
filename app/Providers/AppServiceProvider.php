<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        $this->forceCorrectUrlRootForSubdirectoryInstalls();
    }

    /**
     * Якщо сайт відкривають як http://localhost/lego-shop3/public/... а в .env APP_URL=http://localhost,
     * route() генерує http://localhost/product/... — такої сторінки немає (404). Підлаштовуємо корінь URL за SCRIPT_NAME.
     */
    private function forceCorrectUrlRootForSubdirectoryInstalls(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $request = $this->app->make('request');
        $scriptName = $request->server('SCRIPT_NAME');
        if (! is_string($scriptName) || ! str_ends_with($scriptName, '/index.php')) {
            return;
        }

        $basePath = str_replace('\\', '/', dirname($scriptName));
        if ($basePath === '/' || $basePath === '.' || $basePath === '') {
            return;
        }

        URL::forceRootUrl(rtrim($request->getSchemeAndHttpHost(), '/').$basePath);
    }
}
