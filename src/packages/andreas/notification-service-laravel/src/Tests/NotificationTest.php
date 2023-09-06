<?php

namespace Andreas\NotificationService\Tests;

use Tests\TestCase;
use Andreas\NotificationService\Events\OrderStatusUpdatedEvent;
use Andreas\NotificationService\Listeners\SendOrderStatusToTeamsListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NotificationTest extends TestCase
{
    public function testEventConstruction()
    {
        $orderUuid = Str::uuid();
        $newStatus = 'open';
        $timestamp = Carbon::now()->format("Y-m-d H:i:s");

        $event = new OrderStatusUpdatedEvent($orderUuid, $newStatus, $timestamp);

        $this->assertEquals($orderUuid, $event->orderUuid);
        $this->assertEquals($newStatus, $event->newStatus);
        $this->assertEquals($timestamp, $event->timestamp);
    }

    public function testEventAndListener()
    {
        Event::fake();

        $uuid = Str::uuid();
        $status = 'open';
        $timestamp = Carbon::now()->format("Y-m-d H:i:s");

        $event = new OrderStatusUpdatedEvent($uuid, $status, $timestamp);
        event($event);

        Event::assertDispatched(OrderStatusUpdatedEvent::class, function ($event) use ($uuid, $status, $timestamp) {
            return $event->orderUuid == $uuid &&
                   $event->newStatus == $status &&
                   $event->timestamp == $timestamp;
        });
    }
}
