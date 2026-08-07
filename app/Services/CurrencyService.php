<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    /**
     * Obtiene el tipo de cambio de USD a la moneda especificada (por defecto PEN)
     */
    public function getExchangeRate(string $to = 'PEN', string $from = 'USD'): ?float
    {


        $cacheKey = "exchange_rate_{$from}_{$to}";

        // Cache the exchange rate for 12 hours (Sunat rates update daily)
        return Cache::remember($cacheKey, 43200, function () use ($from, $to) {
            try {
                // If converting to PEN, try to use SUNAT exchange rate from apis.net.pe
                if ($to === 'PEN' && $from === 'USD') {
                    $response = Http::timeout(5)->get('https://api.apis.net.pe/v1/tipo-cambio-sunat');
                    if ($response->successful()) {
                        $data = $response->json();
                        // Usually we use the selling price (precio Venta)
                        return isset($data['venta']) ? (float) $data['venta'] : 3.75;
                    }
                }

                $apiKey = config('services.fastforex.key');
                if (!$apiKey || $apiKey === '[ELIMINADO_POR_SEGURIDAD]') {
                    // Use free open exchange rate API as fallback
                    $response = Http::timeout(5)->get('https://open.er-api.com/v6/latest/' . $from);
                    if ($response->successful()) {
                        $data = $response->json();
                        return isset($data['rates'][$to]) ? (float) $data['rates'][$to] : 3.75;
                    }
                    return 3.75;
                }

                $response = Http::timeout(5)->withHeaders([
                    'Accept' => 'application/json',
                ])->get(config('services.fastforex.base_url') . '/fetch-one', [
                    'from' => $from,
                    'to' => $to,
                    'api_key' => $apiKey,
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    return isset($data['result'][$to]) ? (float) $data['result'][$to] : null;
                }
            } catch (\Exception $e) {
                // Ignore exception and return null fallback
            }

            return null;
        });
    }
}
