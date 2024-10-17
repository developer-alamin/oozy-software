<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8|confirmed',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);
    //     $token = $user->createToken('YourAppName')->plainTextToken;

    //     // You may also return a token here if you are using API authentication
    //     return response()->json([
    //         'user' => $user,
    //         'token' => $token,
    //         'message' => 'User registered successfully',
    //     ], 201);
    // }
    // public function login(Request $request)
    // {
    //     // Validate the login data
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);
    //     // Find the user by email
    //     $user = User::where('email', $request->email)->first();
    //     // Check if the user exists and the password is correct
    //     if ($user && Hash::check($request->password, $user->password)) {
    //         // Generate a new Sanctum token
    //         $token = $user->createToken('UserToken')->plainTextToken;
    //         // Return the token and user data
    //         return response()->json([
    //             'token' => $token,
    //             'user'  => $user,
    //         ]);
    //     }
    // }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete(); // Revoke all tokens
        return response()->json(['message' => 'Logged out successfully']);
    }


    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email'    => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::guard('user')->attempt($request->only('email', 'password'))) {
    //         $user  = Auth::guard('user')->user();
            
    //         $token = $user->createToken('UserToken')->plainTextToken;

    //         return response()->json(['token' => $token, 'user' => $user]);
    //     }

    //     return response()->json(['message' => 'Invalid credentials'], 401);
    // }

    public function login(Request $request)
    {
        // Validate the login data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Try to find the admin first
        $admin = Admin::where('email', $request->email)->first();
        if ($admin && Hash::check($request->password, $admin->password)) {
            // Generate Sanctum token for admin
            $token = $admin->createToken('AdminToken')->plainTextToken;
            return response()->json([
                'token'  => $token,
                'user'   => $admin,
                'role'   => 'admin', // Adding role to identify
            ]);
        }

        // If not admin, try to find the user
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            // Generate Sanctum token for user
            $token = $user->createToken('UserToken')->plainTextToken;
            return response()->json([
                'token'  => $token,
                'user'   => $user,
                'role'   => 'user', // Adding role to identify
            ]);
        }

        // If neither found, return error
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function register(Request $request)
    {
        // Validate the registration data
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users|unique:admins',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|string|in:admin,user', // Admin or user role
        ]);

        // Determine if the user is registering as an admin or user
        if ($request->role === 'admin') {
            // Create an admin
            $admin = Admin::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $token = $admin->createToken('AdminToken')->plainTextToken;

            return response()->json([
                'token'  => $token,
                'user'   => $admin,
                'role'   => 'admin',
            ]);
        } else {
            // Create a user
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $token = $user->createToken('UserToken')->plainTextToken;

            return response()->json([
                'token'  => $token,
                'user'   => $user,
                'role'   => 'user',
            ]);
        }
    }





}
