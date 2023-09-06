<?php

namespace Andreas\NotificationService\Listeners;

use GuzzleHttp\Client;
use Andreas\NotificationService\Events\OrderStatusUpdatedEvent;

class SendOrderStatusToTeamsListener
{
    protected Client $client;
    protected string $webhookUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->webhookUrl = config('notification_service.webhook_url');
    }

    public function handle(OrderStatusUpdatedEvent $event): void
    {
        $payload = [
            'message' => 'Order Updated',
            'order_uuid' => $event->orderUuid,
            'new_status' => $event->newStatus,
            'timestamp' => $event->timestamp,
        ];

        $this->client->post($this->webhookUrl, [
            'json' => $payload,
        ]);
    }
}
