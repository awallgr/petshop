<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\JwtTokenService;
use Lcobucci\JWT\Token;
use Auth;

class ValidateJwtToken
{
    protected JwtTokenService $jwtTokenService;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tokenString = $this->extractTokenFromRequest($request);

        if ($this->isTokenValid($tokenString)) {
            $this->authenticateUser($this->jwtTokenService->getLastValidatedToken());
        } else {
            return response()->fail('Unauthorized', 401);
        }

        return $next($request);
    }

    private function extractTokenFromRequest(Request $request): string
    {
        return str_replace('Bearer ', '', $request->header('Authorization') ?? "");
    }

    private function isTokenValid(string $tokenString): bool
    {
        return $this->jwtTokenService->validateToken($tokenString);
    }

    private function authenticateUser(?Token $token): void
    {
        if (!$token) {
            return;
        }
        $user = $this->jwtTokenService->getUserFromToken($token);
        if ($user) {
            Auth::setUser($user);
        }
    }
}
