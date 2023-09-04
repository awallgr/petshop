<?php

namespace Andreas\NotificationService\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public string $orderUuid;
    public string $newStatus;
    public string $timestamp;

    public function __construct(string $orderUuid, string $newStatus, string $timestamp)
    {
        $this->orderUuid = $orderUuid;
        $this->newStatus = $newStatus;
        $this->timestamp = $timestamp;
    }
}
