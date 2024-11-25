<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Services\JwtTokenService;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    protected $jwtTokenService;

    public function setUp(): void
    {
        parent::setUp();
        $this->jwtTokenService = app(JwtTokenService::class);
    }

    /* ############### ADMIN-LOGIN ################## */

    public function testAdminLoginSuccess()
    {
        $result = $this->post('/api/v1/admin/login', [
            'email' => 'test@test.com',
            'password' => 'test1234'
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
        $this->assertTrue($user->is_admin);
        $this->assertTrue($user->jwtToken != null);
    }

    public function testAdminLoginWrongPassword()
    {
        $this->post('/api/v1/admin/login', [
            'email' => 'test@test.com',
            'password' => 'wrongpassword'
        ])->assertStatus(422);
    }

    public function testAdminLoginEmptyPassword()
    {
        $this->post('/api/v1/admin/login', [
            'email' => 'test@test.com',
            'password' => ''
        ])->assertStatus(422);
    }

    public function testAdminLoginInvalidEmailFormat()
    {
        $this->post('/api/v1/admin/login', [
            'email' => 'admin',
            'password' => 'test1234'
        ])->assertStatus(422);
    }

    public function testAdminLoginEmptyEmail()
    {
        $this->post('/api/v1/admin/login', [
            'email' => '',
            'password' => 'test1234'
        ])->assertStatus(422);
    }

    public function testAdminLoginUnregisteredEmail()
    {
        $this->post('/api/v1/admin/login', [
            'email' => 'wrongtest@test.com',
            'password' => 'test1234'
        ])->assertStatus(422);
    }

    public function testAdminLoginNullValues()
    {
        $this->post('/api/v1/admin/login', [
            'email' => null,
            'password' => null
        ])->assertStatus(422);
    }

    /* ############### USER-LISTING ################## */

    public function testAdminUserListingSuccess()
    {
        $user = User::factory()->admin()->create();
        $this->jwtTokenService->generateToken($user, 'test token');

        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/admin/user-listing', $headers)->assertStatus(200);
        $response = json_decode($result->getContent(), true);

        $this->assertTrue(isset($response['current_page']));
        $this->assertTrue(isset($response['data']));
        $this->assertTrue(isset($response['links']));

        $users = $response['data'];
        $this->assertEquals(11, count($users));
    }

    public function testAdminUserListingLimitQuery()
    {
        $user = User::factory()->admin()->create();
        $this->jwtTokenService->generateToken($user, 'test token');

        $params = ['limit' => '1'];
        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/admin/user-listing?' . http_build_query($params), $headers)->assertStatus(200);
        $response = json_decode($result->getContent(), true);
        $this->assertTrue(isset($response['data']));
        $users = $response['data'];
        $this->assertEquals(1, count($users));
    }


    public function testAdminUserListingInvalidLimitQuery()
    {
        $user = User::factory()->admin()->create();
        $this->jwtTokenService->generateToken($user, 'test token');

        $params = ['limit' => 'notvalid'];
        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/admin/user-listing?' . http_build_query($params), $headers)->assertStatus(422);
    }


    public function testAdminUserListingEmailQuery()
    {
        $user = User::factory()->admin()->create();
        $this->jwtTokenService->generateToken($user, 'test token');

        $otherUser = User::factory()->create();

        $params = ['email' => $otherUser->email];
        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/admin/user-listing?' . http_build_query($params), $headers)->assertStatus(200);
        $response = json_decode($result->getContent(), true);
        $this->assertTrue(isset($response['data']));
        $users = $response['data'];
        $this->assertEquals(1, count($users));
    }

    public function testAdminUserListingInvalidEmailQuery()
    {
        $user = User::factory()->admin()->create();
        $this->jwtTokenService->generateToken($user, 'test token');

        $otherUser = User::factory()->create();

        $params = ['email' => 'notemail'];
        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/admin/user-listing?' . http_build_query($params), $headers)->assertStatus(422);
    }


    public function testAdminUserListingNotAuthorized()
    {
        $this->get('/api/v1/admin/user-listing')->assertStatus(401);
    }

    public function testAdminUserListingUserNotAuthorized()
    {
        $user = User::factory()->create();
        $this->jwtTokenService->generateToken($user, 'test token');
        $headers = ['Authorization' => $user->jwtToken->token_data];
        $result = $this->get('/api/v1/admin/user-listing', $headers)->assertStatus(401);
    }
}
