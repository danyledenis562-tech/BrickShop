@props([
    'path' => null,
    'imageId' => null,
    'embedded' => false,
    'alt' => '',
    'class' => '',
])

@php
    $url = null;
    if ($embedded && $imageId) {
        $url = route('media.product-image', ['image' => $imageId]);
    } elseif ($path) {
        $normalizedPath = ltrim($path, '/');
        if (\Illuminate\Support\Str::startsWith($normalizedPath, 'storage/')) {
            $normalizedPath = \Illuminate\Support\Str::after($normalizedPath, 'storage/');
        }
        $url = match (true) {
            \Illuminate\Support\Str::startsWith($path, ['http://', 'https://']) => $path,
            \Illuminate\Support\Str::startsWith($path, ['images/', '/images/', 'build/', '/build/']) => asset(ltrim($path, '/')),
            default => route('media.public', ['path' => $normalizedPath]),
        };
    }
@endphp

@if ($url)
    <img src="{{ $url }}" alt="{{ $alt }}" class="{{ $class }}" loading="lazy">
@endif
