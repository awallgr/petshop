<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        User::create(
            [
                'name' => 'Admin',
                'uuid' => Str::uuid(),
                'email' => 'admin@buckhill.co.uk',
                'password' => bcrypt('admin'),
                'is_admin' => 1
            ]
        );

        foreach (range(1, 10) as $index) {
            User::create([
                'name' => $faker->name,
                'uuid' => Str::uuid(),
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('userpassword'),
            ]);
        }
    }
}
