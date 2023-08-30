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
    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     * @param string $title
     */
    public function generateToken($user, $title): JwtToken
    {
        $token = $this->buildToken($user);
        return $this->createAndSaveJwtToken($user, $title, $token);
    }

    /**
     * @param string $tokenString
     */
    public function validateToken($tokenString): bool
    {
        try {
            $token = $this->parseToken($tokenString);
            return $this->isValidToken($token, new Sha256());
        } catch (\Exception $e) {
            return false;
        }
    }






    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     */
    private function buildToken($user): Token
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


    /**
     * @param string $tokenString
     */
    private function parseToken($tokenString): Token
    {
        return (new Parser(new JoseEncoder()))->parse($this->validateNotEmpty($tokenString, 'Token cannot be empty.'));
    }

    /**
     * @param Token $token
     * @param Sha256 $algorithm
     */
    private function isValidToken($token, $algorithm): bool
    {
        $publicKey = $this->getKey('public.pem');
        if (!$this->isTokenSignedWithAlgorithm($token, $algorithm, $publicKey)) {
            return false;
        }

        if (!$this->isTokenIssuedByHost($token)) {
            return false;
        }

        return $this->isUserAndTokenActive($token);
    }

    /**
     * @param Token $token
     * @param Sha256 $algorithm
     * @param InMemory $publicKey
     */
    private function isTokenSignedWithAlgorithm($token, $algorithm, $publicKey): bool
    {
        $validator = new JWTValidator();
        return $validator->validate($token, new SignedWith($algorithm, $publicKey));
    }

    /**
     * @param Token $token
     */
    private function isTokenIssuedByHost($token): bool
    {
        $validator = new JWTValidator();
        return $validator->validate(
            $token,
            new IssuedBy($this->validateNotEmpty(request()->getHost(), 'Host cannot be empty.'))
        );
    }

    /**
     * @param Token $token
     */
    private function isUserAndTokenActive($token): bool
    {
        $user = $this->getUserFromToken($token);
        return $user && $this->isTokenActive($user);
    }

    private function getUserFromToken(Token $token): User|null
    {
        assert($token instanceof UnencryptedToken);
        $userUuid = $token->claims()->get('user_uuid');
        return User::where('uuid', '=', $userUuid)->first();
    }

    private function isTokenActive(User $user): bool
    {
        $jwtToken = $user->jwtToken;
        if (!$jwtToken || ($jwtToken->expires_at && Carbon::parse($jwtToken->expires_at)->isPast())) {
            return false;
        }
        return true;
    }


    private function getKey(string $filename): InMemory
    {
        $keyPath = storage_path("jwt-keys/{$filename}");
        return InMemory::file($this->validateNotEmpty($keyPath, 'Key path cannot be empty.'));
    }

    /**
     * @param string $value
     * @param string $errorMessage
     *
     * @return non-empty-string
     */
    private function validateNotEmpty($value, $errorMessage): string
    {
        if (empty($value)) {
            throw new \InvalidArgumentException($errorMessage);
        }
        return $value;
    }
}
