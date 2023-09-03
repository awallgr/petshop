<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderStatus;

class OrderSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::factory()->create();
        $orderStatus = OrderStatus::where('title', '=', 'open')->first();

        foreach (range(1, 10) as $index) {
            Order::factory()->create([
                'user_id' => $user->id,
                'order_status_id' => $orderStatus->id
            ]);
        }
    }
}
