<?php

namespace App\Services\Shipping;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class NovaPoshtaClient
{
    public function __construct(
        private readonly NovaPoshtaFallbackDirectory $fallback,
    ) {}

    private function apiKey(): string
    {
        return (string) config('services.nova_poshta.api_key', '');
    }

    private function endpoint(): string
    {
        return 'https://api.novaposhta.ua/v2.0/json/';
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
                return [];
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
     * @return array<int, array{label:string}>
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
                        'Limit' => 100,
                        'Language' => 'UA',
                    ],
                ]);

            if (! $response->ok()) {
                return [];
            }

            return collect($response->json('data', []))
                ->map(fn (array $row) => ['label' => trim((string) ($row['Description'] ?? ''))])
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

            if (! $response->ok()) {
                return [];
            }

            return collect($response->json('data', []))
                ->map(fn (array $row) => ['label' => trim((string) ($row['StreetName'] ?? ''))])
                ->filter(fn (array $row) => $row['label'] !== '')
                ->values()
                ->all();
        });
    }

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

    private function fallbackBranches(string $cityRef): array
    {
        $row = $this->fallback->directory()[$cityRef] ?? null;
        if (! $row) {
            return [];
        }

        return collect($row['branches'])
            ->map(fn (string $label) => ['label' => $label])
            ->values()
            ->all();
    }
}
