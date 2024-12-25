<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Laravel API Documentation",
 *     version="1.0.0",
 *     description="This is the API documentation for the Laravel project.",
 *     @OA\Contact(
 *         email="ferdawusa@gmail.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://gi-mis-api.test/api",
 *     description="Local Development Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Use a Bearer Token to authenticate.",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 * )
 */
abstract class Controller
{
    //
}