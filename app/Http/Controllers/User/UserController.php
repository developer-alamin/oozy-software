<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function dashboard()
    {
        return response()->json(['message' => 'Welcome to the User Dashboard!']);
    }
}
