@props([
    'path' => null,
    'alt' => '',
    'class' => '',
])

@php
    $url = null;
    if ($path) {
        $url = \Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])
            ? $path
            : asset('storage/'.$path);
    }
@endphp

@if ($url)
    <img src="{{ $url }}" alt="{{ $alt }}" class="{{ $class }}" loading="lazy">
@endif
