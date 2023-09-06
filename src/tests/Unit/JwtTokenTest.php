<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Support\Str;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Validation\Validator as JWTValidator;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use App\Services\JwtTokenService;
use App\Models\User;
use App\Models\JwtToken;
use Auth;
use Carbon\Carbon;

class JwtTokenTest extends TestCase
{
    use DatabaseTransactions;

    protected $jwtTokenService;

    public function setUp(): void
    {
        parent::setUp();
        $this->jwtTokenService = app(JwtTokenService::class);
    }

    public function testGenerateJwtToken(): void
    {
        $publicKey = InMemory::file(storage_path('jwt-keys/public.pem'));
        $algorithm = new Sha256();

        $user = User::factory()->admin()->create();
        $token = $this->jwtTokenService->generateToken($user, "Test token");
        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertTrue($token->token_data != "");
        $this->assertEquals($token->uuid, $user->jwtToken->uuid);
        $this->assertEquals($token->user->id, $user->id);
        $this->assertTrue(Carbon::now()->lt($token->expires_at));

        $validator = new JWTValidator();
        $parser = new Parser(new JoseEncoder());
        $jwtToken = $parser->parse($token->token_data);
        $this->assertTrue($validator->validate($jwtToken, new SignedWith($algorithm, $publicKey)));
    }

    public function testValidateJwtToken(): void
    {
        $user = User::factory()->admin()->create();
        $token = $this->jwtTokenService->generateToken($user, "Test token");

        $this->assertInstanceOf(JwtToken::class, $token);
        $this->assertTrue($token->token_data != "");
        $this->assertTrue($this->jwtTokenService->validateToken($token->token_data));
    }

    public function testGetLastValidatedToken(): void
    {
        $publicKey = InMemory::file(storage_path('jwt-keys/public.pem'));
        $algorithm = new Sha256();

        $user = User::factory()->admin()->create();
        $token = $this->jwtTokenService->generateToken($user, "Test token");
        $this->jwtTokenService->validateToken($token->token_data);
        $lastValidatedToken = $this->jwtTokenService->getLastValidatedToken();

        $this->assertEquals($lastValidatedToken->toString(), $token->token_data);
    }

    public function testGetUserFromToken(): void
    {
        $user = User::factory()->admin()->create();
        $token = $this->jwtTokenService->generateToken($user, "Test token");
        $this->jwtTokenService->validateToken($token->token_data);
        $lastValidatedToken = $this->jwtTokenService->getLastValidatedToken();
        $userFromToken = $this->jwtTokenService->getUserFromToken($lastValidatedToken);

        $this->assertEquals($user->id, $userFromToken->id);
    }
}
