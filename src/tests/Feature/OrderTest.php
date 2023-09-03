<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Auth;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use App\Services\JwtTokenService;

class OrderTest extends TestCase
{
    use DatabaseTransactions;

    protected $jwtTokenService;

    public function setUp(): void
    {
        parent::setUp();
        $this->jwtTokenService = app(JwtTokenService::class);
    }

    public function testAdminGetOrders()
    {
        $user = User::factory()->admin()->create();
        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/orders', $headers)->assertStatus(200);
        $response = json_decode($result->getContent(), true);
        $orders = $response['data'];
        $this->assertEquals(10, count($orders));
    }

    public function testUserGetOrders()
    {
        $user = User::factory()->create();
        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/orders', $headers)->assertStatus(200);
        $response = json_decode($result->getContent(), true);
        $orders = $response['data'];
        $this->assertEquals(0, count($orders));
    }

    public function testCreateOrder()
    {
        $user = User::factory()->create();
        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $result = $this->withHeaders($headers)->post('/api/v1/order', [
            'order_status_uuid' => $orderStatus->uuid,
        ])->assertStatus(200);
        $response = json_decode($result->getContent(), true);
        $order = $response['data'];
        $this->assertTrue($order != null);
        $this->assertEquals($user->id, $order['user_id']);
    }


    public function testGetOrder()
    {
        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => $orderStatus->id]);

        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/order/' . $order->uuid, $headers)->assertStatus(200);
        $response = json_decode($result->getContent(), true);
        $order = $response['data'];
        $this->assertTrue($order != null);
        $this->assertEquals($user->id, $order['user_id']);
        $this->assertEquals($orderStatus->id, $order['order_status_id']);
    }

    public function testGetOrderThatDontExistFail()
    {
        $user = User::factory()->create();
        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/order/2450252', $headers)->assertStatus(422);
    }


    public function testGetOtherOrderFail()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $order = Order::factory()->create(['user_id' => $otherUser->id, 'order_status_id' => $orderStatus->id]);

        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/order/' . $order->uuid, $headers)->assertStatus(422);
    }


    public function testUpdateOrder()
    {
        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $nextOrderStatus = OrderStatus::where('title', '=', 'pending payment')->first();
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => $orderStatus->id]);

        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->withHeaders($headers)->put('/api/v1/order/' . $order->uuid, [
            'order_status_uuid' => $nextOrderStatus->uuid,
        ])->assertStatus(200);

        $order->refresh();
        $this->assertEquals($nextOrderStatus->id, $order->order_status_id);
    }

    public function testUpdateOtherOrderFail()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $nextOrderStatus = OrderStatus::where('title', '=', 'pending payment')->first();
        $order = Order::factory()->create(['user_id' => $otherUser->id, 'order_status_id' => $orderStatus->id]);

        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->withHeaders($headers)->put('/api/v1/order/' . $order->uuid, [
            'order_status_uuid' => $nextOrderStatus->uuid,
        ])->assertStatus(422);

        $order->refresh();
        $this->assertEquals($orderStatus->id, $order->order_status_id);
    }

    public function testDeleteOrder()
    {
        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => $orderStatus->id]);
        $orderUuid = $order->uuid;

        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->withHeaders($headers)->delete('/api/v1/order/' . $order->uuid)->assertStatus(200);

        $this->assertTrue(!Order::where('uuid', '=', $orderUuid)->exists());
    }


    public function testDeleteOtherOrderFail()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $order = Order::factory()->create(['user_id' => $otherUser->id, 'order_status_id' => $orderStatus->id]);
        $orderUuid = $order->uuid;

        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->withHeaders($headers)->delete('/api/v1/order/' . $order->uuid)->assertStatus(422);

        $this->assertTrue(Order::where('uuid', '=', $orderUuid)->exists());
    }



    public function testGetOrderStatuses()
    {
        $result = $this->get('/api/v1/order-statuses')->assertStatus(200);
        $response = json_decode($result->getContent(), true);
        $order_statuses = $response['data'];
        $this->assertEquals(5, count($order_statuses));
    }
}
