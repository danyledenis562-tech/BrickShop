@props([
    'path' => null,
    'alt' => '',
    'class' => '',
])

@php
    $url = null;
    if ($path) {
        $normalizedPath = ltrim($path, '/');
        if (\Illuminate\Support\Str::startsWith($normalizedPath, 'storage/')) {
            $normalizedPath = \Illuminate\Support\Str::after($normalizedPath, 'storage/');
        }
        $url = match (true) {
            \Illuminate\Support\Str::startsWith($path, ['images/', '/images/', 'build/', '/build/']) => asset(ltrim($path, '/')),
            default => route('media.public', ['path' => $normalizedPath]),
        };
    }
@endphp

@if ($url)
    <img src="{{ $url }}" alt="{{ $alt }}" class="{{ $class }}" loading="lazy">
@endif
