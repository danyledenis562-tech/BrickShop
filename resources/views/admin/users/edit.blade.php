<x-admin-layout>
    <x-slot name="breadcrumb">{{ __('messages.admin') }} / {{ __('messages.users') }} / {{ $user->email }}</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.edit') }} {{ __('messages.users') }}</h1>
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 lego-card p-6">
        @csrf
        @method('PUT')
        <div class="text-sm text-[color:var(--muted)]">{{ $user->name }}</div>
        <div class="mt-3 text-sm">
            <span class="text-[color:var(--muted)]">{{ __('messages.user_auth_method') }}:</span>
            @if ($user->registeredWithGoogle())
                <span class="admin-badge badge-paid">{{ __('messages.user_auth_google') }}</span>
            @else
                <span class="admin-badge badge-processing">{{ __('messages.user_auth_site') }}</span>
            @endif
        </div>
        <select name="role" class="lego-input mt-3">
            <option value="user" @selected($user->role === 'user')>{{ __('messages.role_user') }}</option>
            <option value="admin" @selected($user->role === 'admin')>{{ __('messages.role_admin') }}</option>
        </select>
        <button class="mt-3 lego-btn lego-btn-primary text-xs">{{ __('messages.update') }}</button>
    </form>
</x-admin-layout>
