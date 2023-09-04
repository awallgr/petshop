<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Authenticatable;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Encoding\{ChainedFormatter,JoseEncoder};
use Lcobucci\JWT\Validation\Validator as JWTValidator;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\RelatedTo;
use Lcobucci\JWT\Validation\Constraint\HasClaimWithValue;
use App\Models\User;
use App\Models\JwtToken;
use Auth;
use Carbon\Carbon;

class JwtTokenService
{
    private ?Token $lastValidatedToken = null;

    public function generateToken(?Authenticatable $user, string $title): JwtToken
    {
        $token = $this->buildToken($user);
        return $this->createAndSaveJwtToken($user, $title, $token);
    }

    public function validateToken(string $tokenString): bool
    {
        try {
            $token = $this->parseToken($tokenString);
            return $this->performTokenValidation($token);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getUserFromToken(Token $token): User|null
    {
        /** @var UnencryptedToken $token */
        $userUuid = $token->claims()->get('user_uuid');
        return User::where('uuid', '=', $userUuid)->first();
    }

    public function getLastValidatedToken(): Token|null
    {
        return $this->lastValidatedToken;
    }

    private function buildToken(?Authenticatable $user): Token
    {
        $tokenBuilder = new Builder(new JoseEncoder(), ChainedFormatter::default());
        $privateKey = $this->getKey('private.pem');
        $now = new \DateTimeImmutable();

        return $tokenBuilder
            ->issuedBy($this->validateNotEmpty(request()->getHost(), 'Host cannot be empty.'))
            ->identifiedBy(uniqid())
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('user_uuid', $user->uuid ?? "")
            ->getToken(new Sha256(), $privateKey);
    }

    private function createAndSaveJwtToken(?Authenticatable $user, string $title, Token $token): JwtToken
    {
        $jwtToken = new JwtToken([
            'user_id' => $user->id ?? "",
            'uuid' => Str::uuid(),
            'token_title' => $title,
            'token_data' => $token->toString(),
            'expires_at' => Carbon::now()->addHours(1),
        ]);
        $jwtToken->save();
        return $jwtToken;
    }

    private function parseToken(string $tokenString): Token
    {
        return (new Parser(new JoseEncoder()))->parse($this->validateNotEmpty($tokenString, 'Token cannot be empty.'));
    }

    private function performTokenValidation(Token $token): bool
    {
        $this->lastValidatedToken = $token;
        return $this->isValidToken($token, new Sha256());
    }

    private function isValidToken(Token $token, Sha256 $algorithm): bool
    {
        return $this->isTokenSignedWithAlgorithm($token, $algorithm)
            && $this->isTokenIssuedByHost($token)
            && $this->isUserAndTokenActive($token);
    }

    private function isTokenSignedWithAlgorithm(Token $token, Sha256 $algorithm): bool
    {
        $publicKey = $this->getKey('public.pem');
        $validator = new JWTValidator();
        return $validator->validate($token, new SignedWith($algorithm, $publicKey));
    }

    private function isTokenIssuedByHost(Token $token): bool
    {
        $validator = new JWTValidator();
        return $validator->validate(
            $token,
            new IssuedBy($this->validateNotEmpty(request()->getHost(), 'Host cannot be empty.'))
        );
    }

    private function isUserAndTokenActive(Token $token): bool
    {
        $user = $this->getUserFromToken($token);
        return $user && $this->isTokenActive($user);
    }

    private function isTokenActive(User $user): bool
    {
        $jwtToken = $user->jwtToken;
        return $jwtToken && ($jwtToken->expires_at && !Carbon::parse($jwtToken->expires_at)->isPast());
    }


    private function getKey(string $filename): InMemory
    {
        $keyPath = storage_path("jwt-keys/{$filename}");
        return InMemory::file($this->validateNotEmpty($keyPath, 'Key path cannot be empty.'));
    }

    /**
     * @return non-empty-string
     */
    private function validateNotEmpty(string $value, string $errorMessage): string
    {
        return empty($value) ? throw new \InvalidArgumentException($errorMessage) : $value;
    }
}
