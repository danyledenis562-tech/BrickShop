<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncProductImagesToCloudinary extends Command
{
    protected $signature = 'shop:sync-product-images-to-cloudinary {--force : Re-upload images even if they already use cloudinary}';

    protected $description = 'Upload product images to Cloudinary and replace image paths with secure Cloudinary URLs';

    public function handle(): int
    {
        $cloudName = (string) config('services.cloudinary.cloud_name');
        $apiKey = (string) config('services.cloudinary.api_key');
        $apiSecret = (string) config('services.cloudinary.api_secret');
        $folder = trim((string) config('services.cloudinary.folder', 'brickshop/products'), '/');

        if ($cloudName === '' || $apiKey === '' || $apiSecret === '') {
            $this->error('Cloudinary config is missing. Set CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY, CLOUDINARY_API_SECRET.');

            return self::FAILURE;
        }

        $force = (bool) $this->option('force');
        $images = ProductImage::query()->orderBy('id')->get(['id', 'path']);
        if ($images->isEmpty()) {
            $this->info('No product images found.');

            return self::SUCCESS;
        }

        $uploaded = 0;
        $skipped = 0;
        $failed = 0;
        $uploadUrl = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

        $bar = $this->output->createProgressBar($images->count());
        $bar->start();

        foreach ($images as $image) {
            $path = trim((string) $image->path);
            if ($path === '') {
                $skipped++;
                $bar->advance();

                continue;
            }

            if (! $force && Str::contains($path, 'res.cloudinary.com')) {
                $skipped++;
                $bar->advance();

                continue;
            }

            [$bytes, $filename] = $this->loadBytesAndFilename($image->id, $path);
            if ($bytes === null || $filename === null) {
                $failed++;
                $bar->advance();
                $this->newLine();
                $this->warn("Failed #{$image->id}: unable to load source image");

                continue;
            }

            $timestamp = time();
            $publicId = ($folder !== '' ? $folder.'/' : '').$image->id;
            $signature = sha1("folder={$folder}&public_id={$publicId}&timestamp={$timestamp}{$apiSecret}");

            $response = $this->cloudinaryClient()
                ->attach('file', $bytes, $filename)
                ->post($uploadUrl, [
                    'api_key' => $apiKey,
                    'timestamp' => $timestamp,
                    'folder' => $folder,
                    'public_id' => (string) $image->id,
                    'signature' => $signature,
                ]);

            if (! $response->successful()) {
                $failed++;
                $bar->advance();
                $this->newLine();
                $this->warn("Failed #{$image->id}: Cloudinary HTTP ".$response->status());

                continue;
            }

            $secureUrl = (string) data_get($response->json(), 'secure_url', '');
            if ($secureUrl === '') {
                $failed++;
                $bar->advance();
                $this->newLine();
                $this->warn("Failed #{$image->id}: secure_url missing in response");

                continue;
            }

            $image->path = $secureUrl;
            $image->save();
            $uploaded++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Uploaded: {$uploaded}, skipped: {$skipped}, failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function cloudinaryClient(): PendingRequest
    {
        return Http::timeout(30)->retry(2, 300);
    }

    private function loadBytesAndFilename(int $id, string $path): array
    {
        if (Str::startsWith($path, ['http://', 'https://'])) {
            $response = Http::timeout(20)->retry(2, 200)->get($path);
            if (! $response->successful()) {
                return [null, null];
            }

            $ext = $this->detectExtensionFromPath($path);

            return [$response->body(), "product-{$id}.{$ext}"];
        }

        $normalized = ltrim($path, '/');
        if (Str::startsWith($normalized, 'storage/')) {
            $normalized = Str::after($normalized, 'storage/');
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($normalized)) {
            return [null, null];
        }

        $ext = $this->detectExtensionFromPath($normalized);

        return [$disk->get($normalized), "product-{$id}.{$ext}"];
    }

    private function detectExtensionFromPath(string $path): string
    {
        $ext = strtolower(pathinfo((string) parse_url($path, PHP_URL_PATH), PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true) ? $ext : 'jpg';
    }
}
