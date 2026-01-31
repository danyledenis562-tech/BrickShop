<x-guest-layout>
    <div class="lego-auth-header">
        <a href="{{ route('welcome') }}" class="lego-auth-logo">Brick Shop</a>
        <p class="lego-auth-subtitle">Створи акаунт у Brick Shop</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="lego-auth-field">
            <x-input-label for="name" :value="__('messages.name')" />
            <x-text-input id="name" class="lego-auth-input block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="lego-auth-field">
            <x-input-label for="email" :value="__('messages.email')" />
            <x-text-input id="email" class="lego-auth-input block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="lego-auth-field">
            <x-input-label for="password" :value="__('messages.password')" />
            <x-text-input id="password" class="lego-auth-input block mt-1 w-full"
                          type="password"
                          name="password"
                          required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="lego-auth-field">
            <x-input-label for="password_confirmation" :value="__('messages.confirm_password')" />
            <x-text-input id="password_confirmation" class="lego-auth-input block mt-1 w-full"
                          type="password"
                          name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="lego-auth-primary w-full justify-center">
                Створити акаунт
            </x-primary-button>
        </div>

        <div class="mt-5 text-center text-sm text-[color:var(--muted)]">
            Вже маєш акаунт?
            <a href="{{ route('login') }}" class="lego-auth-link">Увійти</a>
        </div>
    </form>
</x-guest-layout>
