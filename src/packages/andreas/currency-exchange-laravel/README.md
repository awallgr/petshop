# Currency Exchange for Laravel

This Laravel package provides an easy way to convert currencies using the latest rates from the European Central Bank.

## Installation

In your composer.json, add following:
```bash
"keywords": [...],
"repositories": {
    "currency-exchange-laravel": {
        "type": "path",
        "url": "YOUR_PACKAGE_LOCATION/andreas/currency-exchange-laravel",
        "options": {
            "symlink": true
        }
    }
},
"require": {
    ...
    "andreas/currency-exchange-laravel": "@dev"
    ...
},
```
Update composer

```bash
composer update
```

Publish the config file:
```bash
php artisan vendor:publish --provider="Andreas\CurrencyExchange\CurrencyExchangeProvider" --tag=config
```

## Usage


```
API Endpoint

The package provides an API endpoint to handle currency exchange:

    Endpoint: /api/v1/currency-exchange/exchange
    Method: POST
    Parameters:
        amount (float): The amount in EUR you want to exchange.
        currency (string): The target currency code.
```
Example Request

```json
{
"amount": 100,
"currency": "USD"
}
```
Example Response

```json
{
"success": 1,
"data": {
"original_amount": 100,
"original_currency": "EUR",
"exchange_amount": 110,
"exchange_currency": "USD",
"exchange_rate": 1.1
},
"error": null,
"errors": [],
"trace": []
}
```

Using the service:
```php
use Andreas\\CurrencyExchange\\Services\\CurrencyExchangeService;

$service = new CurrencyExchangeService();
$response = $service->exchangeAmount(100, 'USD');
```

`exchangeAmount` takes two parameters:
1. `amount` (float) - The amount you want to convert.
2. `currency` (string) - The target currency you want to convert to.

It returns a `JsonResponse`.

## Configuration

You can set the default currency and cache duration (hours) in the published config file `config/currency_exchange.php`.

## Testing

Add the package to phpunit.xml
```xml
<testsuites>
    ...
    <testsuite name="CurrencyExchange">
        <directory>YOUR_PACKAGE_LOCATION/andreas/currency-exchange-laravel/src/Tests</directory>
    </testsuite>
</testsuites>
```

Run the tests with:
```bash
vendor/bin/phpunit
```

## Credits

- [Andreas Wallgren](https://github.com/awallgr)

## License

The MIT License (MIT).

