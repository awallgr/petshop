<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create(
            [
                'name' => 'Admin',
                'uuid' => Str::uuid(),
                'email' => 'admin@buckhill.co.uk',
                'password' => bcrypt('admin'),
                'is_admin' => 1
            ]
        );
    }
}
