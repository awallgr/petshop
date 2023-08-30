<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Auth;
use Carbon\Carbon;

class AdminTest extends TestCase
{
    use DatabaseTransactions;

    public function testAdminLoginSuccess()
    {
        $response = $this->post('/api/v1/admin/login', [
            'email' => 'admin@buckhill.co.uk',
            'password' => 'admin'
        ])->assertStatus(200);
        $this->assertTrue(Auth::User() != null);
        $this->assertTrue(Auth::User()->is_admin);
    }

    public function testAdminLoginWrongPassword()
    {
        $this->post('/api/v1/admin/login', [
            'email' => 'admin@buckhill.co.uk',
            'password' => 'wrongpassword'
        ])->assertStatus(422);
    }

    public function testAdminLoginEmptyPassword()
    {
        $this->post('/api/v1/admin/login', [
            'email' => 'admin@buckhill.co.uk',
            'password' => ''
        ])->assertStatus(422);
    }

    public function testAdminLoginInvalidEmailFormat()
    {
        $this->post('/api/v1/admin/login', [
            'email' => 'admin',
            'password' => 'admin'
        ])->assertStatus(422);
    }

    public function testAdminLoginEmptyEmail()
    {
        $this->post('/api/v1/admin/login', [
            'email' => '',
            'password' => 'admin'
        ])->assertStatus(422);
    }

    public function testAdminLoginUnregisteredEmail()
    {
        $this->post('/api/v1/admin/login', [
            'email' => 'unregistered@buckhill.co.uk',
            'password' => 'admin'
        ])->assertStatus(422);
    }

    public function testAdminLoginNullValues()
    {
        $this->post('/api/v1/admin/login', [
            'email' => null,
            'password' => null
        ])->assertStatus(422);
    }
}
