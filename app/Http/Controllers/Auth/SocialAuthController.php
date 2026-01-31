<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::query()
            ->where('google_id', $googleUser->id)
            ->orWhere('email', $googleUser->email)
            ->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => $googleUser->name ?? $googleUser->nickname ?? 'Google User',
                'email' => $googleUser->email,
                'password' => Hash::make(Str::random(32)),
                'google_id' => $googleUser->id,
                'email_verified_at' => now(),
            ]);
        } elseif (! $user->google_id) {
            $user->google_id = $googleUser->id;
            $user->save();
        }

        Auth::login($user, true);

        return redirect()->intended('/');
    }
}
