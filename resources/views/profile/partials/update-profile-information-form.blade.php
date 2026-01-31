@php
    /** @var \App\Models\User $user */
    $avatarUrl = $user->avatar ? asset('storage/'.$user->avatar) : null;
@endphp

<section>
    <header>
        <h2 class="text-lg font-medium">
            {{ __('messages.profile_info') }}
        </h2>

        <p class="mt-1 text-sm text-[color:var(--muted)]">
            {{ __('messages.profile_info_subtitle') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex flex-wrap items-center gap-4 rounded-2xl border border-[color:var(--border)] bg-[color:var(--card)] p-4">
            <div class="profile-avatar-lg">
                @if ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="profile-avatar-img" data-avatar-preview>
                @else
                    <img src="" alt="{{ $user->name }}" class="profile-avatar-img hidden" data-avatar-preview>
                    <span class="profile-avatar-fallback">{{ mb_substr($user->name, 0, 1) }}</span>
                @endif
            </div>
            <div class="min-w-[220px] flex-1">
                <x-input-label for="avatar" :value="__('messages.profile_photo')" />
                <input id="avatar" name="avatar" type="file" accept="image/*" class="lego-input mt-1 block w-full" data-avatar-input>
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                <p class="mt-1 text-xs text-[color:var(--muted)]">PNG, JPG до 2MB. Фото буде видно у профілі.</p>
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('messages.name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('messages.email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('messages.email_unverified') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('messages.email_resend') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('messages.verify_email_sent') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="phone" :value="__('messages.phone')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="city" :value="__('messages.city')" />
            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->city)" />
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>

        <div>
            <x-input-label for="address" :value="__('messages.address')" />
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->address)" />
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('messages.save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
