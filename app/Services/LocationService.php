<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class LocationService
{
    protected string $apiKey = 'c6dd092aa009282e506730aace4abe1d3384962ffb52a7b8e732ad66af4abded';
    protected string $baseUrl = 'https://api.countrystatecity.in/v1';

    /**
     * Get all countries.
     */
    public function getCountries(): array
    {
        return Cache::remember('location.countries', 86400, function () {
            $response = Http::withHeaders([
                'X-CSCAPI-KEY' => $this->apiKey,
            ])->get("{$this->baseUrl}/countries");

            return $response->successful() ? $response->json() : [];
        });
    }

    /**
     * Get states for a specific country by ISO2 code.
     */
    public function getStates(string $countryIso2): array
    {
        return Cache::remember("location.states.{$countryIso2}", 86400, function () use ($countryIso2) {
            $response = Http::withHeaders([
                'X-CSCAPI-KEY' => $this->apiKey,
            ])->get("{$this->baseUrl}/countries/{$countryIso2}/states");

            return $response->successful() ? $response->json() : [];
        });
    }

    /**
     * Get cities for a specific country and state.
     */
    public function getCities(string $countryIso2, string $stateIso2): array
    {
        return Cache::remember("location.cities.{$countryIso2}.{$stateIso2}", 86400, function () use ($countryIso2, $stateIso2) {
            $response = Http::withHeaders([
                'X-CSCAPI-KEY' => $this->apiKey,
            ])->get("{$this->baseUrl}/countries/{$countryIso2}/states/{$stateIso2}/cities");

            return $response->successful() ? $response->json() : [];
        });
    }
}
