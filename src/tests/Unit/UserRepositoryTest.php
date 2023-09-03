<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;
use App\Repositories\UserRepository;

class UserRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetAllUsers(): void
    {
        $userRepository = new UserRepository();
        $users = $userRepository->getAllUsers();

        $this->assertInstanceOf(LengthAwarePaginator::class, $users);
        $this->assertEquals(11, $users->total());
    }
}
