<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function fetchUserAuthRoleInfo()
    {

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Return all data for the superadmin
                return response()->json([
                    'superadmin' => true, // Mark as superadmin
                    'user'       => $currentUser, // Return the entire user object
                ]);
            } else {
                // Return all data for regular admins
                return response()->json([
                    'superadmin' => false,
                    'admin'      => true,
                    'user'       => $currentUser, // Return the entire user object
                ]);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();

            // Return all data for the regular user
            return response()->json([
                'user_role' => true,
                'user' => $currentUser, // Return the entire user object
            ]);
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    }
}