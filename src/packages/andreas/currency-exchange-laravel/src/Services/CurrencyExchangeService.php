<?php

namespace Andreas\CurrencyExchange\Services;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CurrencyExchangeService
{
    protected Client $client;
    protected string $defaultCurrency;
    protected int $cacheTime;
    protected string $cachePrefix;

    public function __construct()
    {
        $this->client = new Client();
        $this->defaultCurrency = config('currency_exchange.default_currency', 'EUR');
        $this->cacheTime = config('currency_exchange.cache_time', 24);
        $this->cachePrefix = "currency-exchange-rates-";
    }

    public function exchangeAmount(float $amount, string $currency): JsonResponse
    {
        try {
            $rates = $this->getCurrentExchangeRates();
            $rate = $this->getRate($currency, $rates);
            $convertedAmount = $this->convertAmount($amount, $rate);
        } catch (\Exception $e) {
            return response()->fail('Failed to exchange amount', 422);
        }

        return response()->success(
            [
                'original_amount' => $amount,
                'original_currency' => $this->defaultCurrency,
                'exchange_amount' => $convertedAmount,
                'exchange_currency' => $currency,
                'exchange_rate' => $rate,
            ]
        );
    }

    /**
     * @return array<string, string>
     */
    private function getCurrentExchangeRates(): array
    {
        $today = Carbon::now()->format("Y-m-d");
        return Cache::remember($this->cachePrefix . $today, now()->addHours($this->cacheTime), function () {
            return $this->getLiveExchangeRates();
        });
    }

    /**
     * @param array<string, string> $rates
     */
    private function getRate(string $currency, array $rates): float
    {
        if (!array_key_exists($currency, $rates)) {
            throw new \Exception("Rate for currency {$currency} not found");
        }
        return floatval($rates[$currency]);
    }

    private function convertAmount(float $amount, float $rate): float
    {
        $convertedAmount = $amount * $rate;
        return round($convertedAmount, 2);
    }

    /**
     * @return array<string, string>
     */
    private function getLiveExchangeRates(): array
    {
        $response = $this->client->get("https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
        $content = $response->getBody()->getContents();
        return $this->parseRates($content);
    }

    /**
     * @return array<string, string>
     */
    private function parseRates(string $xmlString): array
    {
        $xml = simplexml_load_string($xmlString);
        if ($xml === false) {
            throw new \Exception('Failed to parse XML');
        }

        // Register a namespace for XPath queries. It's for properly parsing XML namespaces
        $xml->registerXPathNamespace('c', 'http://www.ecb.int/vocabulary/2002-08-01/eurofxref');

        $rateNodes = $xml->xpath('//c:Cube[@currency]');
        if ($rateNodes === false || $rateNodes === null) {
            throw new \Exception('Failed to get rateNodes');
        }

        $rates = [];
        foreach ($rateNodes as $rateNode) {
            $currency = (string) $rateNode['currency'];
            $rate = (string) $rateNode['rate'];
            $rates[$currency] = $rate;
        }
        return $rates;
    }
}
