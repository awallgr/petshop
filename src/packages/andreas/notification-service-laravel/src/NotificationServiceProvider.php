<?php

namespace Andreas\NotificationService;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(
            'Andreas\NotificationService\Events\OrderStatusUpdatedEvent',
            'Andreas\NotificationService\Listeners\SendOrderStatusToTeamsListener'
        );

        $this->publishes([
            __DIR__ . '/config/notification_service.php' => config_path('notification_service.php'),
        ], 'config');
    }
}
