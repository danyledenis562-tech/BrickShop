<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10 space-y-6">
        <div class="lego-card p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        @unless ($user->registeredWithGoogle())
            <div class="lego-card p-6">
                @include('profile.partials.update-password-form')
            </div>
        @else
            <div class="lego-card p-6 text-sm text-[color:var(--muted)]">
                {{ __('messages.password_google_account') }}
            </div>
        @endunless

        <div class="lego-card p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
