<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use App\Services\JwtTokenService;

class StateMachineTest extends TestCase
{
    use DatabaseTransactions;

    protected $jwtTokenService;

    public function setUp(): void
    {
        parent::setUp();
        $this->jwtTokenService = app(JwtTokenService::class);
    }

    public function testStateMachineInitialState()
    {
        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => $orderStatus->id]);
        $order->initializeStateMachine();
        $order->changeCurrentStateFromName($order->status->title);

        $this->assertEquals('open', $order->getCurrentState()->name);
    }

    public function testStateMachinePaidStartState()
    {
        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'paid')->first();
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => $orderStatus->id]);
        $order->initializeStateMachine();
        $order->changeCurrentStateFromName($order->status->title);

        $this->assertEquals('paid', $order->getCurrentState()->name);
    }


    public function testStateMachineCannotTransitionPaidToOpenState()
    {
        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'paid')->first();
        $nextOrderStatus = OrderStatus::where('title', '=', 'open')->first();
        $order = Order::factory()->create(['user_id' => $user->id, 'order_status_id' => $orderStatus->id]);
        $order->initializeStateMachine();
        $order->changeCurrentStateFromName($order->status->title);

        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->withHeaders($headers)->put('/api/v1/order/' . $order->uuid, [
            'order_status_uuid' => $nextOrderStatus->uuid,
        ])->assertStatus(422);

        $order->refresh();

        $this->assertEquals('paid', $order->getCurrentState()->name);
    }
}
