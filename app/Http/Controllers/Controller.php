<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(title="API Motolegacy", version="1.0")
 *
 *  @OA\Server(
 *      url="https://KidziBack.squaretech.tech/motolegacy/api",
 *  )
 *  @OA\Server(
 *      url="http://127.0.0.1:8000/motolegacy/api",
 *  )
 *
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
*/

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
