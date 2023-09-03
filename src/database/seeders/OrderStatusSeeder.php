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
        $types = [
            'open', 'pending payment', 'paid', 'shipped', 'cancelled'
        ];

        foreach ($types as $type) {
            OrderStatus::create([
                'uuid' => Str::uuid(),
                'title' => $type,
            ]);
        }
    }
}
