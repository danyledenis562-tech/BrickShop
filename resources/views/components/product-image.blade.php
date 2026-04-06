@props([
    'path' => null,
    'alt' => '',
    'class' => '',
])

@php
    $url = null;
    if ($path) {
        $url = match (true) {
            \Illuminate\Support\Str::startsWith($path, ['http://', 'https://']) => $path,
            \Illuminate\Support\Str::startsWith($path, ['images/', '/images/', 'build/', '/build/']) => asset(ltrim($path, '/')),
            default => route('media.public', ['path' => ltrim($path, '/')]),
        };
    }
@endphp

@if ($url)
    <img src="{{ $url }}" alt="{{ $alt }}" class="{{ $class }}" loading="lazy">
@endif
