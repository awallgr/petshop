# Laravel State Machine for Orders

This Laravel package provides a State Machine trait specifically designed for managing Orders.

## Installation

Modify your `composer.json` to include the following configurations:

```bash
"keywords": [...],
"repositories": [
  {
    "type": "path",
    "url": "YOUR_PACKAGE_LOCATION/andreas/statemachine-laravel",
    "options": {
      "symlink": true
    }
  }
],
"require": {
    ...
    "andreas/statemachine-laravel": "@dev",
    ...
},
```

Run the composer update:

```bash
composer update
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Andreas\\StateMachine\\StateMachineServiceProvider" --tag=config
```

## Usage

### Implementing the Trait

Include the `HasStateMachine` trait in your `Order` model:

```php
use Andreas\\StateMachine\\Traits\\HasStateMachine;

class Order extends Model
{
    use HasStateMachine;
}
```

### Initializing the State Machine

The state machine initializes automatically when retrieving a model from the database, based on the `graph.json` file.

To initialize the current state based on a value saved in ex. `order_status_id`:

```php
$order->changeCurrentStateFromName('STATE_NAME');
```

### Performing Transitions

To transition to another state, you can use the following code:

```php
$nextState = $order->getStateFromName('NEXT_STATE_NAME');
$transition = $order->getTransitionToState($nextState);

$order->process($transition->name);
```

If the transition is invalid, the state will not change.


### Getting current state

If you would like to know what the current state is

```php
$order->getCurrentState();
```

## Configuration

Upon publishing the config, you will receive a `graph.json` file that will be used for initializing the state machine.

## Testing

Add the package to your `phpunit.xml`:

```xml
<testsuites>
    ...
    <testsuite name="NotificationService">
        <directory>YOUR_PACKAGE_LOCATION/andreas/notification-service-laravel/src/Tests</directory>
    </testsuite>
</testsuites>
```

Run the tests:

```bash
vendor/bin/phpunit
```

## Credits

- [Andreas Wallgren](https://github.com/awallgr)

## License

MIT License

