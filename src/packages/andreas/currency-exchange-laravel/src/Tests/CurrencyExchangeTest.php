<?php

namespace Andreas\CurrencyExchange\Tests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Andreas\CurrencyExchange\Services\CurrencyExchangeService;
use Carbon\Carbon;

class CurrencyExchangeTest extends TestCase
{
    use WithFaker;

    protected CurrencyExchangeService $currencyExchangeService;

    public function setUp(): void
    {
        parent::setUp();
        $this->currencyExchangeService = app(CurrencyExchangeService::class);
    }

    public function testExchangeSuccess()
    {
        $today = Carbon::now()->format("Y-m-d");
        Cache::shouldReceive('remember')
            ->with("currency-exchange-rates-" . $today, \Mockery::any(), \Mockery::any())
            ->andReturn([
                'SEK' => '11.00'
            ]);

        $result = $this->post('/api/v1/currency-exchange/exchange', [
            'amount' => '10',
            'currency' => 'SEK'
        ])->assertStatus(200);

        $data = json_decode($result->getContent());
        $this->assertEquals(1, $data->success);
        $this->assertEquals(10, $data->data->original_amount);
        $this->assertEquals('EUR', $data->data->original_currency);
        $this->assertEquals(110, $data->data->exchange_amount);
        $this->assertEquals('SEK', $data->data->exchange_currency);
        $this->assertEquals(11, $data->data->exchange_rate);
    }

    public function testExchangeWrongAmount()
    {
        $today = Carbon::now()->format("Y-m-d");
        Cache::shouldReceive('remember')
            ->with("currency-exchange-rates-" . $today, \Mockery::any(), \Mockery::any())
            ->andReturn([
                'SEK' => '11.00'
            ]);

        $response = $this->post('/api/v1/currency-exchange/exchange', [
            'amount' => 'amount',
            'currency' => 'SEK'
        ])->assertStatus(422);

        $data = json_decode($response->content());
        $this->assertEquals(0, $data->success);
    }

    public function testExchangeWrongCurrency()
    {
        $today = Carbon::now()->format("Y-m-d");
        Cache::shouldReceive('remember')
            ->with("currency-exchange-rates-" . $today, \Mockery::any(), \Mockery::any())
            ->andReturn([
                'SEK' => '11.00'
            ]);

        $response = $this->post('/api/v1/currency-exchange/exchange', [
            'amount' => '10',
            'currency' => 'EUR'
        ])->assertStatus(422);

        $data = json_decode($response->content());
        $this->assertEquals(0, $data->success);
        $this->assertEquals("Failed to exchange amount", $data->error);

        $response = $this->post('/api/v1/currency-exchange/exchange', [
            'amount' => '10',
            'currency' => 'USD'
        ])->assertStatus(422);

        $data = json_decode($response->content());
        $this->assertEquals(0, $data->success);
        $this->assertEquals("Failed to exchange amount", $data->error);
    }

    public function testItCanExchangeAmountWithValidData()
    {
        $today = Carbon::now()->format("Y-m-d");
        Cache::shouldReceive('remember')
            ->with("currency-exchange-rates-" . $today, \Mockery::any(), \Mockery::any())
            ->andReturn([
                'USD' => '1.1'
            ]);

        $response = $this->currencyExchangeService->exchangeAmount(100, 'USD');
        $data = json_decode($response->content());

        $this->assertEquals(100, $data->data->original_amount);
        $this->assertEquals('EUR', $data->data->original_currency);
        $this->assertEquals(110, $data->data->exchange_amount);
        $this->assertEquals('USD', $data->data->exchange_currency);
        $this->assertEquals(1.1, $data->data->exchange_rate);
    }

    public function testItFailsIfCurrencyIsNotFound()
    {
        $today = Carbon::now()->format("Y-m-d");
        Cache::shouldReceive('remember')
            ->with("currency-exchange-rates-" . $today, \Mockery::any(), \Mockery::any())
            ->andReturn([
                'USD' => '1.1'
            ]);

        $response = $this->currencyExchangeService->exchangeAmount(100, 'XYZ');
        $data = json_decode($response->content());
        $this->assertEquals(0, $data->success);
        $this->assertEquals("Failed to exchange amount", $data->error);
    }

    public function testItGetsLiveDataCorrectly()
    {
        $response = $this->currencyExchangeService->exchangeAmount(100, 'JPY');
        $data = json_decode($response->content());

        $this->assertEquals(1, $data->success);
        $this->assertEquals(100, $data->data->original_amount);
        $this->assertEquals('EUR', $data->data->original_currency);

        $this->assertGreaterThanOrEqual(12000, $data->data->exchange_amount);
        $this->assertLessThanOrEqual(18000, $data->data->exchange_amount);

        $this->assertEquals('JPY', $data->data->exchange_currency);

        $this->assertGreaterThanOrEqual(120, $data->data->exchange_rate);
        $this->assertLessThanOrEqual(180, $data->data->exchange_rate);
    }
}
