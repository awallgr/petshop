<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Petshop API",
 *      description="Petshop project",
 * )
 *
 * @OA\Tag(
 *     name="Admin",
 *     description="Admin API endpoint"
 * )
 *
 * @OA\Tag(
 *     name="User",
 *     description="User API endpoint"
 * )
 *
 * @OA\Tag(
 *     name="Orders",
 *     description="Orders API endpoint"
 * )
 *
 * @OA\Tag(
 *     name="OrderStatuses",
 *     description="Order Statuses API endpoint"
 * )
 *
 * @OA\Tag(
 *     name="CurrencyExchange",
 *     description="Currency Exchange API endpoint"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * ),
 */
class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;
}
