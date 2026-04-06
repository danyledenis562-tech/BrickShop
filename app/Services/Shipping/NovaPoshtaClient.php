<?php

namespace App\Services\Shipping;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class NovaPoshtaClient
{
    public function __construct(
        private readonly NovaPoshtaFallbackDirectory $fallback,
    ) {}

    private function apiKey(): string
    {
        $key = config('services.nova_poshta.api_key');

        return is_string($key) ? trim($key) : '';
    }

    private function endpoint(): string
    {
        return rtrim((string) config('services.nova_poshta.endpoint', 'https://api.novaposhta.ua/v2.0/json/'), '/').'/';
    }

    /**
     * @return array<int, array{label:string, ref:string, district:string, area:string}>
     */
    public function searchCities(string $query): array
    {
        $query = trim($query);
        if (mb_strlen($query) < 1) {
            return [];
        }

        $cacheKey = 'shipping.np.cities.'.md5(mb_strtolower($query));

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($query) {
            $apiKey = $this->apiKey();
            if ($apiKey === '') {
                return $this->fallbackCities($query);
            }

            $response = Http::retry(2, 150)
                ->timeout(8)
                ->post($this->endpoint(), [
                    'apiKey' => $apiKey,
                    'modelName' => 'Address',
                    'calledMethod' => 'searchSettlements',
                    'methodProperties' => [
                        'CityName' => $query,
                        'Limit' => 20,
                    ],
                ]);

            if (! $response->ok()) {
                Log::warning('Nova Poshta HTTP error (cities)', ['status' => $response->status()]);

                return $this->fallbackCities($query);
            }

            if (! $this->npResponseSucceeded($response)) {
                return $this->fallbackCities($query);
            }

            return collect($response->json('data.0.Addresses', []))
                ->map(fn (array $row) => [
                    'label' => trim((string) ($row['MainDescription'] ?? $row['Present'] ?? '')),
                    'ref' => (string) ($row['DeliveryCity'] ?? ''),
                    'district' => trim((string) ($row['Region'] ?? '')),
                    'area' => trim((string) ($row['Area'] ?? '')),
                ])
                ->filter(fn (array $row) => $row['label'] !== '' && $row['ref'] !== '')
                ->values()
                ->all();
        });
    }

    /**
     * @return array<int, array{label:string, ref:string, name:string, address:string}>
     */
    public function branches(string $cityRef): array
    {
        $cityRef = trim($cityRef);
        if ($cityRef === '') {
            return [];
        }

        $cacheKey = 'shipping.np.branches.'.md5($cityRef);

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($cityRef) {
            $apiKey = $this->apiKey();
            if ($apiKey === '') {
                return $this->fallbackBranches($cityRef);
            }

            $response = Http::retry(2, 150)
                ->timeout(8)
                ->post($this->endpoint(), [
                    'apiKey' => $apiKey,
                    'modelName' => 'Address',
                    'calledMethod' => 'getWarehouses',
                    'methodProperties' => [
                        'CityRef' => $cityRef,
                        'Limit' => 500,
                        'Language' => 'UA',
                    ],
                ]);

            if (! $response->ok() || ! $this->npResponseSucceeded($response)) {
                return $this->fallbackBranches($cityRef);
            }

            return collect($response->json('data', []))
                ->map(function (array $row) {
                    $description = trim((string) ($row['Description'] ?? ''));
                    $ref = (string) ($row['Ref'] ?? '');
                    $number = trim((string) ($row['Number'] ?? ''));
                    $short = trim((string) ($row['ShortAddress'] ?? ''));
                    $address = $short !== '' ? $short : ($number !== '' ? '№'.$number : '');

                    return [
                        'ref' => $ref,
                        'label' => $description,
                        'name' => $description,
                        'address' => $address,
                    ];
                })
                ->filter(fn (array $row) => $row['label'] !== '')
                ->values()
                ->all();
        });
    }

    /**
     * @return array<int, array{label:string}>
     */
    public function streets(string $cityRef, string $query): array
    {
        $cityRef = trim($cityRef);
        $query = trim($query);
        if ($cityRef === '' || $query === '') {
            return [];
        }

        $cacheKey = 'shipping.np.streets.'.md5($cityRef.'|'.mb_strtolower($query));

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($cityRef, $query) {
            $apiKey = $this->apiKey();
            if ($apiKey === '') {
                return [];
            }

            $response = Http::retry(2, 150)
                ->timeout(8)
                ->post($this->endpoint(), [
                    'apiKey' => $apiKey,
                    'modelName' => 'Address',
                    'calledMethod' => 'getStreet',
                    'methodProperties' => [
                        'CityRef' => $cityRef,
                        'FindByString' => $query,
                        'Limit' => 50,
                    ],
                ]);

            if (! $response->ok() || ! $this->npResponseSucceeded($response)) {
                return [];
            }

            return collect($response->json('data', []))
                ->map(fn (array $row) => ['label' => trim((string) ($row['StreetName'] ?? ''))])
                ->filter(fn (array $row) => $row['label'] !== '')
                ->values()
                ->all();
        });
    }

    private function npResponseSucceeded(Response $response): bool
    {
        $json = $response->json();
        if (! is_array($json)) {
            return false;
        }
        if (($json['success'] ?? false) === true) {
            return true;
        }

        Log::warning('Nova Poshta API returned success=false', [
            'errors' => $json['errors'] ?? null,
            'warnings' => $json['warnings'] ?? null,
        ]);

        return false;
    }

    /**
     * @return array<int, array{label:string, ref:string, district:string, area:string}>
     */
    private function fallbackCities(string $query): array
    {
        return collect($this->fallback->directory())
            ->map(fn (array $row, string $ref) => [
                'label' => $row['label'],
                'ref' => $ref,
                'district' => $row['district'],
                'area' => $row['area'],
            ])
            ->filter(fn (array $row) => str_contains(mb_strtolower($row['label']), mb_strtolower($query)))
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{label:string, ref:string, name:string, address:string}>
     */
    private function fallbackBranches(string $cityRef): array
    {
        $row = $this->fallback->directory()[$cityRef] ?? null;
        if (! $row) {
            return [];
        }

        return collect($row['branches'])
            ->values()
            ->map(fn (string $label, int $idx) => [
                'ref' => $cityRef.'-'.$idx,
                'label' => $label,
                'name' => $label,
                'address' => '',
            ])
            ->all();
    }
}
