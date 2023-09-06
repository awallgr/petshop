<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\OrderStatus;

class OrderStatusSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $orderStatuses = [
            [
                'uuid' => '5502113d-4754-443a-af26-aa354fc85655',
                'title' => 'open'
            ],
            [
                'uuid' => '72d98b11-dd7a-486d-9c2d-b55619b8ba67',
                'title' => 'pending payment'
            ],
            [
                'uuid' => '05299896-d5d9-4913-b1ac-997714175015',
                'title' => 'paid'
            ],
            [
                'uuid' => 'c54c1ecc-1528-4b71-a4bd-b8cc2e29db5e',
                'title' => 'shipped'
            ],
            [
                'uuid' => 'b2996ebf-a7fe-4834-bf4a-b1686da28a45',
                'title' => 'cancelled'
            ],
        ];

        foreach ($orderStatuses as $orderStatus) {
            OrderStatus::create([
                'uuid' => $orderStatus['uuid'],
                'title' => $orderStatus['title'],
            ]);
        }
    }
}
