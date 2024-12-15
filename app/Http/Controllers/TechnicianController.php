<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Models\Category;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class TechnicianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
        $sortOrder    = $request->input('sortOrder', 'desc');    // Default sort order is descending
        $search       = $request->input('search', '');           // Search term, default is empty

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // If superadmin, retrieve all technicians
                $techniciansQuery = Technician::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $techniciansQuery = Technician::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // For regular users, filter by creator type and id
            $techniciansQuery = Technician::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $techniciansQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $techniciansQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $technicians = $techniciansQuery->with('creator:id,name','user:id,name','factory:id,name','group:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $technicians->items(), // Current page items
            'total' => $technicians->total(), // Total number of records
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate(Technician::validationRules());

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();

            // Check if the admin is a superadmin
            if ($creator->role === 'superadmin') {
                // Superadmin can create technician without additional checks
            } else {
                // Regular admin authorization check can be implemented here if needed
            }

        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();

            // If you want users to have specific restrictions, implement checks here
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Create the technician and associate it with the creator
        $technician       = new Technician($validatedData);
        $technician->uuid = HelperController::generateUuid();
        $technician->creator()->associate($creator);  // Assign creator polymorphically
        $technician->updater()->associate($creator);  // Associate the updater
        $technician->save(); // Save the technician to the database

        // Return a success response
        return response()->json(['success' => true, 'message' => 'Technician created successfully.'], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Technician $technician)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $technician = Technician::where('uuid', $uuid)->firstOrFail();
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any technician
                return response()->json([
                    'success' => true,
                    'technician' => $technician
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the technician belongs to the current user or admin
        if ($technician->creator_type !== $creatorType || $technician->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this technician.'], 403);
        }
        // Return the technician data if authorized
        return response()->json([
            'success' => true,
            'technician' => $technician
        ], Response::HTTP_OK);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$uuid)
    {
        // Validate the incoming request data
        $validatedData = $request->validate(Technician::validationRules());
        $technician = Technician::where('uuid', $uuid)->firstOrFail();
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can update without additional checks
            } else {
                // Regular admin authorization check
                if ($technician->creator_type !== $creatorType || $technician->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this technician.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            if ($technician->creator_type !== $creatorType || $technician->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this technician.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Update the technician's details
        $technician->fill($validatedData);
        $technician->updater()->associate($currentUser); // Associate the updater
        $technician->save();

        // Return a success response
        return response()->json(['success' => true, 'message' => 'Technician updated successfully.', 'technician' => $technician], 200);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Technician $technician)
    {
    // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any technician without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($technician->creator_type !== $creatorType || $technician->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this technician.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            if ($technician->creator_type !== $creatorType || $technician->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this technician.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Delete the technician
        $technician->delete();
        // Return a success response
        return response()->json(['success' => true, 'message' => 'Technician deleted successfully.'], 200);
    }


    public function trashed(Request $request)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all soft-deleted technicians
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed technicians without additional checks
                $techniciansQuery = Technician::onlyTrashed();
            } else {
                // Regular admin authorization check
                $techniciansQuery = Technician::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $techniciansQuery = Technician::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this user

        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is descending
        $search       = $request->input('search', ''); // Search term, default is empty

        // Apply search if the search term is not empty
        if (!empty($search)) {
            $techniciansQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your Technician fields
        }

        // Apply sorting
        $techniciansQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $technicians = $techniciansQuery->with('creator:id,name','user:id,name')->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $technicians->items(), // Current page items
            'total' => $technicians->total(), // Total number of trashed records
        ]);
    }

    public function trashedTechniciansCount()
    {
        // Get the count of soft-deleted technicians
        $trashedCount = Technician::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }

    // Permanently delete a technician from trash
    public function forceDelete($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $technician = Technician::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $technician = Technician::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $technician = Technician::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Permanently delete the technician
        $technician->forceDelete(); // Permanent delete

        return response()->json(['message' => 'Technician permanently deleted'], Response::HTTP_OK);
    }

    // Restore a soft-deleted technician
    public function restore($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $restored = Technician::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = Technician::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = Technician::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($restored) {
            return response()->json(['message' => 'Technician restored successfully'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Technician not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }



}