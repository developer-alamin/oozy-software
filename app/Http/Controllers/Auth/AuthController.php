<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function fetchAdminAllUserInfo(Request $request)
    {
        // $currentUser = Auth::guard('admin')->user();
        // return response()->json([
        //     'items'   => $currentUser,
            
        // ]);
        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
        $sortOrder    = $request->input('sortOrder', 'desc');    // Default sort order is descending
        $search       = $request->input('search', '');           // Search term, default is empty

        // Determine the authenticated user (either from 'admin' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // retrieve all admin all users
            $adminAllUsersQuery = Admin::query(); // No filters applied
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $adminAllUsersQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $adminAllUsersQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $adminAllUsers = $adminAllUsersQuery->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $adminAllUsers->items(), // Current page items
            'total' => $adminAllUsers->total(), // Total number of records
        ]);
    }

    public function adminUserCreate(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users|unique:admins',
            'password' => 'required|string|min:8',
            'role'     => 'nullable|string', // Admin or user role
            'phone'    => 'nullable',
            'status'   => 'nullable',
        ]);
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($creator->role === 'superadmin') {
                // Superadmin can create user without additional checks
            } else {
                // Regular admin authorization check can be implemented here if needed
               
            }
    
        }else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $admin = Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'status'   => $request->status,
            'role'     => 'admin',
            'password' => Hash::make($request->password),
        ]);
        $token = $admin->createToken('AdminToken')->plainTextToken;

        return response()->json([
            'token'   => $token,
            'user'    => $admin,
            'role'    => 'admin',
            'success' => true, 
            'message' => 'Admin created successfully.'
        ]);
        
    }

    public function allUserInfo(Request $request)
    {
        // $currentUser = Auth::guard('admin')->user();
        // return response()->json([
        //     'items'   => $currentUser,
            
        // ]);
        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
        $sortOrder    = $request->input('sortOrder', 'desc');    // Default sort order is descending
        $search       = $request->input('search', '');           // Search term, default is empty

        // Determine the authenticated user (either from 'admin' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('user')->user();
            // retrieve all admin all users
            $allUsersQuery = User::query(); // No filters applied
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $allUsersQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $allUsersQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $allUsers = $allUsersQuery->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $allUsers->items(), // Current page items
            'total' => $allUsers->total(), // Total number of records
        ]);
    }

    public function userCreate(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users|unique:admins',
            'password' => 'required|string|min:8',
            'role'     => 'nullable|string', // user or user role
            'phone'    => 'nullable',
            'status'   => 'nullable',
        ]);
        // if (Auth::guard('admin')->check()) {
        //     $creator = Auth::guard('admin')->user();
        //     // Check if the admin is a superadmin
        //     if ($creator->role === 'superadmin') {
        //         // Superadmin can create user without additional checks
        //     } else {
        //         // Regular admin authorization check can be implemented here if needed
               
        //     }
    
        // }else {
        //     return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        // }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'status'   => $request->status,
            'role'     => 'user',
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('UserToken')->plainTextToken;

        return response()->json([
            'token'   => $token,
            'user'    => $user,
            'role'    => 'user',
            'success' => true, 
            'message' => 'Users created successfully.'
        ]);
        
    }
}