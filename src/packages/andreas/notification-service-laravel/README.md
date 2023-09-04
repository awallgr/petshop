# Notification Service to MS Teams for Laravel

This Laravel package provides an easy way to sending notification messages regarding an Order to MS Teams Webhook.

## Installation

In your composer.json, add following:
```bash
"keywords": [...],
"repositories": {
    "notification-service-laravel": {
        "type": "path",
        "url": "YOUR_PACKAGE_LOCATION/andreas/notification-service-laravel",
        "options": {
            "symlink": true
        }
    }
},
"require": {
    ...
    "andreas/notification-service-laravel": "@dev"
    ...
},
```
Update composer

```bash
composer update
```

Publish the config file:
```bash
php artisan vendor:publish --provider="Andreas\NotificationService\NotificationServiceProvider" --tag=config
```

## Usage

### Emitting Events

To emit an `OrderStatusUpdatedEvent`, you need to pass in three parameters:

- `orderUuid` (string): The UUID of the order
- `newStatus` (string): The new status of the order
- `timestamp` (string): The time the order was updated

Example:

```php
use YourNamespace\Events\OrderStatusUpdatedEvent;

event(new OrderStatusUpdatedEvent($orderUuid, $newStatus, $timestamp));
```

### Handling Events

The `SendOrderStatusToTeamsListener` will automatically handle the event, create a MS Teams notification card, and send it to the specified Webhook URL.

### Customizing Payload

You can customize the notification payload in `SendOrderStatusToTeamsListener.php`.

## Configuration

You can set the MS Teams Webhook in the published config file `config/notification_service.php`.

## Testing

Add the package to phpunit.xml
```xml
<testsuites>
    ...
    <testsuite name="NotificationService">
        <directory>YOUR_PACKAGE_LOCATION/andreas/notification-service-laravel/src/Tests</directory>
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

