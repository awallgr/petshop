<?php

namespace App\Services;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Auth\Authenticatable;

class LoginService
{
    protected JwtTokenService $jwtTokenService;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->failAuthentication();
        }

        $user = Auth::user();
        $this->revokeExistingToken($user);

        return $this->successAuthentication($user);
    }

    private function failAuthentication(): JsonResponse
    {
        return response()->fail('Failed to authenticate user', 422);
    }

    private function revokeExistingToken(?Authenticatable $user): void
    {
        $token = optional($user)->jwtToken;
        optional($token)->delete();
    }

    private function successAuthentication(?Authenticatable $user): JsonResponse
    {
        $token = $this->jwtTokenService->generateToken($user, "Authorize JWT Token")->token_data;
        return response()->success(['token' => $token]);
    }
}
