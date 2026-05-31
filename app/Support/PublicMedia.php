<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicMedia
{
    public static function url(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');
        if (Str::startsWith($normalizedPath, 'storage/')) {
            $normalizedPath = Str::after($normalizedPath, 'storage/');
        }

        if ($normalizedPath === '' || str_contains($normalizedPath, '..')) {
            return null;
        }

        $suffix = '';
        $disk = Storage::disk('public');
        if ($disk->exists($normalizedPath)) {
            $suffix = '?v='.$disk->lastModified($normalizedPath);
        }

        $publicFile = public_path('storage/'.$normalizedPath);
        if (is_file($publicFile)) {
            return asset('storage/'.$normalizedPath).$suffix;
        }

        return route('media.public', ['path' => $normalizedPath]).$suffix;
    }
}
