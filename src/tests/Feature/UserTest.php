<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Auth;
use Carbon\Carbon;
use App\Models\User;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function testUserLoginSuccess()
    {
        $user = User::factory()->create();
        $result = $this->post('/api/v1/user/login', [
            'email' => $user->email,
            'password' => 'userpassword'
        ])->assertStatus(200);

        $response = json_decode($result->getContent(), true);
        $this->assertTrue(is_array($response));
        $this->assertTrue(isset($response['data']));
        $data = $response['data'];
        $this->assertTrue(isset($data['token']));
        $token = $data['token'];
        $this->assertTrue(is_string($token));
        $this->assertTrue($token != "");

        $user = Auth::User()->refresh();

        $this->assertTrue($user != null);
        $this->assertTrue(!$user->is_admin);
        $this->assertTrue($user->jwtToken != null);
    }

    public function testUserLoginWrongPassword()
    {
        $user = User::factory()->create();
        $this->post('/api/v1/user/login', [
            'email' => $user->email,
            'password' => 'wrongpassword'
        ])->assertStatus(422);
    }

    public function testUserLoginEmptyPassword()
    {
        $user = User::factory()->create();
        $this->post('/api/v1/user/login', [
            'email' => 'user@buckhill.co.uk',
            'password' => ''
        ])->assertStatus(422);
    }

    public function testUserLoginInvalidEmailFormat()
    {
        $this->post('/api/v1/user/login', [
            'email' => 'user',
            'password' => 'user'
        ])->assertStatus(422);
    }

    public function testUserLoginEmptyEmail()
    {
        $this->post('/api/v1/user/login', [
            'email' => '',
            'password' => 'user'
        ])->assertStatus(422);
    }

    public function testUserLoginUnregisteredEmail()
    {
        $this->post('/api/v1/user/login', [
            'email' => 'unregistered@buckhill.co.uk',
            'password' => 'user'
        ])->assertStatus(422);
    }

    public function testUserLoginNullValues()
    {
        $this->post('/api/v1/user/login', [
            'email' => null,
            'password' => null
        ])->assertStatus(422);
    }
}
