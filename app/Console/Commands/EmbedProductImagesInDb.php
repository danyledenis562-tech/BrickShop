<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EmbedProductImagesInDb extends Command
{
    protected $signature = 'shop:embed-product-images-in-db {--force : Rebuild image_data even if already present}';

    protected $description = 'Download product images and store them in product_images.image_data';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $query = ProductImage::query()->select(['id', 'path', 'image_data']);

        if (! $force) {
            $query->whereNull('image_data');
        }

        $total = (clone $query)->count();
        if ($total === 0) {
            $this->info('Nothing to process.');

            return self::SUCCESS;
        }

        $this->info("Processing {$total} images...");
        $embedded = 0;
        $skipped = 0;
        $failed = 0;

        $query->orderBy('id')->chunkById(100, function ($rows) use (&$embedded, &$skipped, &$failed, $force) {
            foreach ($rows as $row) {
                if (! $force && is_string($row->image_data) && Str::startsWith($row->image_data, 'data:image/')) {
                    $skipped++;

                    continue;
                }

                [$mime, $bytes] = $this->loadImageBytes((string) $row->path);
                if (! $mime || ! $bytes) {
                    $failed++;
                    $this->warn("Failed: #{$row->id} ({$row->path})");

                    continue;
                }

                $row->forceFill([
                    'image_data' => 'data:'.$mime.';base64,'.base64_encode($bytes),
                ])->save();
                $embedded++;
            }
        });

        $this->line("Embedded: {$embedded}, skipped: {$skipped}, failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function loadImageBytes(string $path): array
    {
        $path = trim(str_replace('\\', '/', $path));
        if ($path === '') {
            return [null, null];
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $response = Http::timeout(20)->accept('image/*')->get($path);
            if (! $response->successful()) {
                return [null, null];
            }

            $mime = strtolower((string) $response->header('Content-Type'));
            $mime = trim(explode(';', $mime)[0]);
            if (! Str::startsWith($mime, 'image/')) {
                $mime = $this->detectMimeFromPath($path);
            }

            return [$mime, $response->body()];
        }

        if (Str::startsWith($path, ['images/', '/images/', 'build/', '/build/'])) {
            return [null, null];
        }

        $normalized = ltrim($path, '/');
        if (Str::startsWith($normalized, 'storage/')) {
            $normalized = Str::after($normalized, 'storage/');
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($normalized)) {
            return [null, null];
        }

        $bytes = $disk->get($normalized);
        $mime = $this->detectMimeFromPath($normalized);

        return [$mime, $bytes];
    }

    private function detectMimeFromPath(string $path): string
    {
        $ext = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?? $path, PATHINFO_EXTENSION));

        return match ($ext) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'image/jpeg',
        };
    }
}
