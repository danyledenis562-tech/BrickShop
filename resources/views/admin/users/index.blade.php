<x-admin-layout>
    <x-slot name="breadcrumb">Admin / Users</x-slot>

    <h1 class="text-2xl font-bold">{{ __('messages.users') }}</h1>
    <div class="mt-6 overflow-x-auto admin-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.email') }}</th>
                    <th>{{ __('messages.role') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role }}</td>
                        <td class="text-right">
                            <a href="{{ route('admin.users.edit', $user) }}" class="admin-icon-btn" aria-label="{{ __('messages.edit') }}">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->appends(request()->query())->links() }}</div>
</x-admin-layout>
