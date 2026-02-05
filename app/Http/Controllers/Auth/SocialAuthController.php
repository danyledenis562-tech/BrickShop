<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function callback(Request $request): RedirectResponse
    {
        if ($request->string('error')->toString() !== '' || ! $request->filled('code')) {
            return redirect()->route('login')->with('status', __('messages.google_login_failed'));
        }

        /** @var \Laravel\Socialite\Two\AbstractProvider $provider */
        $provider = Socialite::driver('google');
        if (app()->environment('production')) {
            $provider = $provider->stateless();
        }

        /** @var \Laravel\Socialite\Two\User $googleUser */
        $googleUser = $provider->user();
        $googleId = $googleUser->getId();
        $googleEmail = $googleUser->getEmail();
        $googleName = $googleUser->getName() ?? $googleUser->getNickname() ?? 'Google User';

        $user = User::query()
            ->where('google_id', $googleId)
            ->orWhere('email', $googleEmail)
            ->first();

        if (! $user) {
            $user = User::query()->create([
                'name' => $googleName,
                'email' => $googleEmail,
                'password' => Hash::make(Str::random(32)),
                'google_id' => $googleId,
                'email_verified_at' => now(),
            ]);
        } elseif (! $user->google_id) {
            $user->google_id = $googleId;
            $user->save();
        }

        Auth::login($user, true);

        return redirect()->intended('/');
    }
}
