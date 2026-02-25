@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'lego-auth-error text-sm space-y-1 mt-1.5']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
