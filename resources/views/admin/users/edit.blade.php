<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Users / {{ $user->email }}</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.edit') }} {{ __('messages.users') }}</h1>
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mt-6 lego-card p-6">
        @csrf
        @method('PUT')
        <div class="text-sm text-[color:var(--muted)]">{{ $user->name }}</div>
        <select name="role" class="lego-input mt-3">
            <option value="user" @selected($user->role === 'user')>user</option>
            <option value="admin" @selected($user->role === 'admin')>admin</option>
        </select>
        <button class="mt-3 lego-btn lego-btn-primary text-xs">{{ __('messages.update') }}</button>
    </form>
</x-admin-layout>
