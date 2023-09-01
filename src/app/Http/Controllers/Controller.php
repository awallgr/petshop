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
     *      @OA\Contact(
     *          email="andreasforhire@gmail.com"
     *      ),
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
