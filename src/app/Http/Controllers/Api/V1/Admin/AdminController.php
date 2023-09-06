<?php

namespace App\Http\Controllers\Api\V1\Admin;

use Auth;
use App\Models\User;
use App\Services\LoginService;
use OpenApi\Annotations as OA;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Contracts\UserRepositoryContract;
use App\Http\Requests\UserListingRequest;
use App\Http\Resources\UserResourceCollection;

class AdminController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/admin/login",
     *      operationId="loginAdmin",
     *      tags={"Admin"},
     *      summary="Login an Admin account",
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="Admin email"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     description="Admin password"
     *                 ),
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function login(LoginRequest $request, LoginService $loginService): JsonResponse
    {
        return $loginService->login($request);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/admin/user-listing",
     *      operationId="adminShowUsers",
     *      tags={"Admin"},
     *      summary="List all users",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Internal server error"
     *      )
     * )
     */
    public function userListing(UserListingRequest $request, UserRepositoryContract $userRepository): JsonResponse
    {
        $user = Auth::User();
        if (!$user || $user->cannot('userListing', User::class)) {
            return response()->fail('Unauthorized', 401);
        }
        $users = $userRepository->getAllUsers((array) $request->query());
        return (new UserResourceCollection($users))->toResponse($request);
    }
}
