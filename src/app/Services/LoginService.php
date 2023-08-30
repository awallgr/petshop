<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Carbon\Carbon;

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

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     */
    private function revokeExistingToken(?Authenticatable $user): void
    {
        $token = optional($user)->jwtToken;
        optional($token)->delete();
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     */
    private function successAuthentication($user): JsonResponse
    {
        $token = $this->jwtTokenService->generateToken($user, "Authorize JWT Token")->token_data;
        return response()->success(['token' => $token]);
    }
}
