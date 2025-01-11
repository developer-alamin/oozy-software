<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OTPMail;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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
    /**

  /**
 * @OA\Post(
 *     path="/user/login",
 *     tags={"Authentication"},
 *     summary="Authenticate user or admin and retrieve a token",
 *     description="This endpoint allows a user or admin to log in using their email and password. It validates the credentials against the database. If the credentials match an admin, a token with the role 'admin' is generated and returned. Similarly, if the credentials match a user, a token with the role 'user' is generated and returned. The response includes the token, user details, and the role.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", example="admin@gmail.com"),
 *             @OA\Property(property="password", type="string", example="12345678")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string"),
 *             @OA\Property(property="user", type="object"),
 *             @OA\Property(property="role", type="string", example="admin or user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials"
 *     )
 * )
 */

  public function login(Request $request)
  {
      // Validate the login data
      $request->validate( [
          'email'    => 'required|email',
          'password' => 'required',
      ]);

      // If Match User, try to find the user
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

  public function forgotPassword(Request $request){
       // Validate the login data
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Retrieve the user by email
        $user = User::where('email', $request->input('email'))->first();
        if ($user) {
            // Generate a random OTP
            $otp = rand(1000, 9999);

            // Send OTP to the user's email
            try {
                Mail::to($user->email)->send(new OTPMail($otp));
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP. Please try again later.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            // Save OTP to the user record (optional: include expiration)
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(100); // Optional expiration time
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.',
            ], 200);
        }

        // If the email doesn't exist (though this shouldn't happen due to validation)
        return response()->json([
            'success' => false,
            'message' => 'Email not found.',
        ], 404);
  }

  public function verifyOtp(Request $request){
        // Validate the request data
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|exists:users,otp',
        ]);

        // Retrieve the user based on email
        $user = User::where('email', $validatedData['email'])->first();

        // Check if the OTP matches
        if ($user && $user->otp == $validatedData['otp']) {
            // Check if the OTP has expired
            if (now()->greaterThan($user->otp_expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired.',
                ], 400);
            }

            // Mark OTP as verified (optional: clear OTP fields)
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->otp_verified = true; // Add this field if needed
            $user->save();

            return response()->json([
                'success' => true,
                'item' => $user,
                'message' => 'OTP verified successfully.',
            ], 200);
        }

        // Return an error if OTP is invalid (although validation should catch most cases)
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP.',
        ], 400);
  }
  public function resetPassword(Request $request){
        // Validate the input data
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed', // 'password_confirmation' is required
        ]);

        // Retrieve the user based on email
        $user = User::where('email', $validatedData['email'])->first();
        
        // Ensure the OTP is verified (assuming you have an `otp_verified` column)
        if ($user && $user->otp_verified) {
            // Update the user's password
            $user->password = Hash::make($validatedData['password']);
            $user->otp_verified = false; // Reset OTP verification status
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully.',
            ], 200);
        }

        // If OTP has not been verified
        return response()->json([
            'success' => false,
            'message' => 'OTP not verified. Please verify your OTP first.',
        ], 400);
  }
  /**
 * @OA\Post(
 *     path="/user/register",
 *     tags={"Authentication"},
 *     summary="Register a new user",
 *     description="Create a new user account",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email","phone","password","password_confirmation","role"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", example="user22@gmail.com"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="password_confirmation", type="string", example="password123"),
 *             @OA\Property(property="phone", type="string", example="01654263557"),
 *             @OA\Property(property="role", type="string", example="user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User created successfully"),
 *             @OA\Property(property="user", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Invalid input")
 *         )
 *     )
 * )
 */


  public function register(Request $request)
  {

      // Validate the registration data
      $request->validate([
          'name'     => 'required|string|max:255',
          'email'    => 'required|string|email|max:255|unique:users|unique:admins',
          'password' => 'required|string|min:8',
          'role'     => 'required|string|in:admin,user', // Admin or user role
      ]);


    // Determine the authenticated user (either from 'admin' or 'user' guard)
    if (Auth::guard('admin')->check()) {
        $currentUser = Auth::guard('admin')->user();
        $creatorType = Admin::class;
        
    } elseif (Auth::guard('user')->check()) {
        $currentUser = Auth::guard('user')->user();
        $creatorType = User::class;
        
    } else {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

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
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->creator()->associate($currentUser);
        $user->updater()->associate($currentUser);
        $user->save();

       // return response()->json($user,200);status: 
        $token = $user->createToken('UserToken')->plainTextToken;

        return response()->json([
            'token'  => $token,
            'user'   => $user,
            'role'   => 'user',
        ]);
      }
  }








}