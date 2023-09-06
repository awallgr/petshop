<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;
use App\Repositories\OrderRepository;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderStatus;
use Faker\Factory as Faker;

class OrderRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminGetOrders(): void
    {
        $user = User::factory()->admin()->create();
        $orderRepository = new OrderRepository();
        $orders = $orderRepository->getAllOrders($user);
        $this->assertTrue(count($orders) > 0);
    }

    public function testUserGetOrders(): void
    {
        $user = User::factory()->create();
        $orderRepository = new OrderRepository();
        $orders = $orderRepository->getAllOrders($user);
        $this->assertEquals(0, count($orders));
    }

    public function testGetOrder(): void
    {
        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => $orderStatus->id]);
        $orderRepository = new OrderRepository();
        $foundOrder = $orderRepository->find($order->uuid);
        $this->assertTrue($foundOrder != null);
        $this->assertEquals($order->id, $foundOrder->id);
    }

    public function testCreateOrder(): void
    {
        $faker = Faker::create();

        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();

        $data = [];
        $data['uuid'] = Str::uuid();
        $data['order_status_uuid'] = $orderStatus->uuid;
        $data['products'] = json_encode([
            [
                "product" => Str::uuid(),
                "quantity" => $faker->numberBetween(1, 100)
            ],
        ]);
        $data['address'] = json_encode([
            "billing" => $faker->streetAddress,
            "shipping" => $faker->streetAddress
        ]);
        $data['amount'] = $faker->randomFloat(2, 1, 100);

        $orderRepository = new OrderRepository();
        $createdOrder = $orderRepository->create($user, $data);
        $this->assertTrue($createdOrder != null);
    }


    public function testUpdateOrder(): void
    {
        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $nextOrderStatus = OrderStatus::where('title', '=', 'pending payment')->first();
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => $orderStatus->id]);

        $orderRepository = new OrderRepository();

        $data = [];
        $data['order_status_uuid'] = $nextOrderStatus->uuid;
        $orderRepository->update($order->uuid, $data);
        $order->refresh();

        $this->assertEquals($nextOrderStatus->id, $order->order_status_id);
    }


    public function testDeleteOrder(): void
    {
        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => $orderStatus->id]);
        $uuid = $order->uuid;

        $orderRepository = new OrderRepository();
        $orderRepository->delete($order->uuid);

        $this->assertTrue(!Order::where('uuid', '=', $uuid)->exists());
    }
}
