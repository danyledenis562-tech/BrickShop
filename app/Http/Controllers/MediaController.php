<?php

namespace App\Http\Controllers;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    public function publicStorage(string $path): BinaryFileResponse
    {
        $path = trim(str_replace('\\', '/', $path), '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        abort_if($path === '' || str_contains($path, '..'), 404);

        $disk = Storage::disk('public');
        if (! $disk->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::path('public/'.$path), [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    public function productImage(ProductImage $image): Response
    {
        abort_unless($image->hasEmbeddedData(), 404);

        $raw = (string) $image->image_data;
        if (! preg_match('#^data:(image/[a-zA-Z0-9.+-]+);base64,(.+)$#', $raw, $matches)) {
            abort(404);
        }

        $mime = $matches[1];
        $payload = base64_decode($matches[2], true);
        if ($payload === false) {
            abort(404);
        }

        return response($payload, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
