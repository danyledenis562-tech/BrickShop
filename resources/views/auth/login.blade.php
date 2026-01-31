<x-guest-layout>
    <div class="lego-auth-header">
        <a href="{{ route('welcome') }}" class="lego-auth-logo">Brick Shop</a>
        <p class="lego-auth-subtitle">Раді бачити тебе знову</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="lego-auth-field">
            <x-input-label for="email" :value="__('messages.email')" />
            <x-text-input id="email" class="lego-auth-input block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="lego-auth-field">
            <x-input-label for="password" :value="__('messages.password')" />
            <x-text-input id="password" class="lego-auth-input block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="lego-auth-remember">
            <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" type="checkbox" class="lego-auth-checkbox" name="remember">
                <span class="text-sm text-[color:var(--muted)]">{{ __('messages.remember_me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="lego-auth-link text-sm" href="{{ route('password.request') }}">
                    {{ __('messages.forgot_password') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <x-primary-button class="lego-auth-primary w-full justify-center">
                Увійти
            </x-primary-button>
        </div>

        <div class="lego-auth-divider">
            <span>або</span>
        </div>

        <a href="{{ route('auth.google') }}" class="lego-auth-google">
            <span class="lego-auth-google-icon">G</span>
            Увійти через Google
        </a>

        <div class="mt-5 text-center text-sm text-[color:var(--muted)]">
            Немає акаунту?
            <a href="{{ route('register') }}" class="lego-auth-link">Зареєструватися</a>
        </div>
    </form>
</x-guest-layout>
