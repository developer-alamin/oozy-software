<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
  public function dashboard()
  {
    return response()->json(['message' => 'Welcome to the Admin Dashboard!']);
  }

  public function verifyAuth(Request $request)
  {
    $user = $request->user();
    if ($user) {
      return response()->json([
        "auth"  => true,
        "user"  => $user
      ]);
    } else {
      return response()->json([
        "auth"  => false,
        "user"  => null
      ]);
    }
  }
}
