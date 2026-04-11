<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = ['uk', 'en', 'pl', 'ru'];
        $locale = $request->session()->get('locale') ?? $request->cookie('locale');

        if (! in_array($locale, $available, true)) {
            $locale = 'uk';
        }

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);

        if (! $request->routeIs('locale.switch')) {
            cookie()->queue(cookie('locale', $locale, 60 * 24 * 30));
        }

        return $next($request);
    }
}
