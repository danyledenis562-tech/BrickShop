<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10 space-y-6">
        <div class="lego-card p-6">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="lego-card p-6">
            @include('profile.partials.update-password-form')
        </div>

        <div class="lego-card p-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
