<?php

namespace App\Http\Controllers;

use App\Services\Shipping\NovaPoshtaClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function novaCities(Request $request, NovaPoshtaClient $client): JsonResponse
    {
        $query = trim((string) $request->string('q')->toString());
        if (mb_strlen($query) < 1) {
            return response()->json([]);
        }

        return response()->json($client->searchCities($query));
    }

    public function novaBranches(Request $request, NovaPoshtaClient $client): JsonResponse
    {
        $cityRef = trim((string) $request->string('city_ref')->toString());
        if ($cityRef === '') {
            return response()->json([]);
        }

        return response()->json($client->branches($cityRef));
    }

    public function novaStreets(Request $request, NovaPoshtaClient $client): JsonResponse
    {
        $cityRef = trim((string) $request->string('city_ref')->toString());
        $query = trim((string) $request->string('q')->toString());

        if ($cityRef === '' || $query === '') {
            return response()->json([]);
        }

        return response()->json($client->streets($cityRef, $query));
    }
}
