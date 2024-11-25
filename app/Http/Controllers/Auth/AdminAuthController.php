<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
  // public function login(Request $request)
  // {
  //     $request->validate([
  //         'email' => 'required|email',
  //         'password' => 'required',
  //     ]);

  //     $admin = Admin::where('email', $request->email)->first();

  //     if ($admin && Hash::check($request->password, $admin->password)) {
  //         Auth::guard('admin')->login($admin);
  //         return response()->json(['message' => 'Admin logged in successfully']);
  //     }

  //     return response()->json(['message' => 'Invalid credentials'], 401);
  // }

  public function logout(Request $request)
  {
    $user = $request->user();
    $user->tokens()->delete();
    return response()->json(['message' => 'User logged out successfully']);
  }

  public function login(Request $request)
  {
    // Validate the login data
    $request->validate([
      'email' => 'required|email',
      'password' => 'required',
    ]);
    // Find the user by email
    $admin = Admin::where('email', $request->email)->first();
    // Check if the admin exists and the password is correct
    if ($admin && Hash::check($request->password, $admin->password)) {
      // Generate a new Sanctum token
      $token = $admin->createToken('AdminToken')->plainTextToken;
      // Return the token and admin data
      return response()->json([
        'token'  => $token,
        'admin'  => $admin,
      ]);
    }
  }
}
