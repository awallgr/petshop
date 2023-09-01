<?php

namespace Andreas\CurrencyExchange\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Andreas\CurrencyExchange\Requests\CurrencyExchangeRequest;
use Andreas\CurrencyExchange\Services\CurrencyExchangeService;

class CurrencyExchangeController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/currency-exchange/exchange",
     *      operationId="currencyExchange",
     *      tags={"CurrencyExchange"},
     *      summary="Exchange currency (Default EUR)",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"amount", "currency"},
     *                 @OA\Property(
     *                     property="amount",
     *                     type="number",
     *                     format="double",
     *                     description="Currency amount (2 decimals ex. 12.50)"
     *                 ),
     *                 @OA\Property(
     *                     property="currency",
     *                     type="string",
     *                     enum={
     *                        "USD", "JPY", "BGN", "CZK", "DKK",
     *                        "GBP", "HUF", "PLN", "RON", "SEK",
     *                        "CHF", "ISK", "NOK", "TRY", "AUD",
     *                        "BRL", "CAD", "CNY", "HKD", "IDR",
     *                        "ILS", "INR", "KRW", "MXN", "MYR",
     *                        "NZD", "PHP", "SGD", "THB", "ZAR"},
     *                     description="Currency to exchange to"
     *                 ),
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function exchange(
        CurrencyExchangeRequest $request,
        CurrencyExchangeService $currentExchangeService
    ): JsonResponse {
        $amount = floatval($request->input('amount'));
        $currency = strval($request->input('currency'));
        return $currentExchangeService->exchangeAmount($amount, $currency);
    }
}
