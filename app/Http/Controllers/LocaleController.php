<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function switch(string $locale): RedirectResponse
    {
        $available = ['uk', 'en', 'pl', 'ru'];

        if (! in_array($locale, $available, true)) {
            $locale = 'uk';
        }

        session(['locale' => $locale]);

        return redirect()->back()->withCookie(cookie('locale', $locale, 60 * 24 * 30));
    }
}
