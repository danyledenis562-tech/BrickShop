<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    public function publicStorage(string $path): BinaryFileResponse
    {
        $path = trim(str_replace('\\', '/', $path), '/');
        abort_if($path === '' || str_contains($path, '..'), 404);

        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::path('public/'.$path), [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
