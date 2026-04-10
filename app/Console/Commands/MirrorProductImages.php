<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MirrorProductImages extends Command
{
    protected $signature = 'shop:mirror-product-images {--force : Re-download even if local file already exists}';

    protected $description = 'Download external product images to local public storage and update DB paths';

    public function handle(): int
    {
        $images = ProductImage::query()->orderBy('id')->get(['id', 'path']);
        if ($images->isEmpty()) {
            $this->info('No product images found.');

            return self::SUCCESS;
        }

        $force = (bool) $this->option('force');
        $done = 0;
        $skipped = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($images->count());
        $bar->start();

        foreach ($images as $image) {
            $path = (string) ($image->path ?? '');
            if ($path === '') {
                $skipped++;
                $bar->advance();

                continue;
            }

            if (! Str::startsWith($path, ['http://', 'https://'])) {
                $skipped++;
                $bar->advance();

                continue;
            }

            $ext = $this->detectExtensionFromUrl($path);
            $targetPath = 'products/mirrored/'.$image->id.'.'.$ext;

            if (! $force && Storage::disk('public')->exists($targetPath)) {
                $image->path = $targetPath;
                $image->save();
                $done++;
                $bar->advance();

                continue;
            }

            try {
                $response = Http::timeout(15)->retry(2, 200)->get($path);
            } catch (\Throwable $e) {
                $failed++;
                $bar->advance();
                $this->newLine();
                $this->warn("Failed #{$image->id}: network error");

                continue;
            }

            if (! $response->ok()) {
                $failed++;
                $bar->advance();
                $this->newLine();
                $this->warn("Failed #{$image->id}: HTTP ".$response->status());

                continue;
            }

            $bytes = $response->body();
            if ($bytes === '') {
                $failed++;
                $bar->advance();
                $this->newLine();
                $this->warn("Failed #{$image->id}: empty body");

                continue;
            }

            Storage::disk('public')->put($targetPath, $bytes, ['visibility' => 'public']);

            $image->path = $targetPath;
            $image->save();
            $done++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Mirrored: {$done}, skipped: {$skipped}, failed: {$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function detectExtensionFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $ext = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        return in_array($ext, $allowed, true) ? $ext : 'jpg';
    }
}
