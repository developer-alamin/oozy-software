<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Models\Admin;
use App\Models\User;

class OperatorController extends Controller
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
                // If superadmin, retrieve all operators
                $operatorsQuery = Operator::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $operatorsQuery = Operator::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // For regular users, filter by creator type and id
            $operatorsQuery = Operator::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Apply search if the search term is not empty
        if (!empty($search)) {
            $operatorsQuery->where('name', 'LIKE', '%' . $search . '%');
        }

        // Apply sorting
        $operatorsQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $operators = $operatorsQuery->with('creator:id,name','user:id,name')->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $operators->items(), // Current page items
            'total' => $operators->total(), // Total number of records
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate(Operator::validationRules());

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();

            // Check if the admin is a superadmin
            if ($creator->role === 'superadmin') {
                // Superadmin can create Operator without additional checks
            } else {
                // Regular admin authorization check can be implemented here if needed
            }

        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();

            // If you want users to have specific restrictions, implement checks here
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Create the Operator and associate it with the creator
        $operator       = new Operator($validatedData);
        $operator->uuid = HelperController::generateUuid();
        $operator->creator()->associate($creator);  // Assign creator polymorphically
        $operator->updater()->associate($creator);  // Associate the updater
        $operator->save(); // Save the Operator to the database

        // Return a success response
        return response()->json(['success' => true, 'message' => 'Operator created successfully.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Operator $operator)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $operator = Operator::where('uuid', $uuid)->firstOrFail();
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any operator
                return response()->json([
                    'success' => true,
                    'operator' => $operator
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the operator belongs to the current user or admin
        if ($operator->creator_type !== $creatorType || $operator->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this operator.'], 403);
        }
        // Return the operator data if authorized
        return response()->json([
            'success' => true,
            'operator' => $operator
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$uuid)
    {
        // Validate the incoming request data
        $validatedData = $request->validate(Operator::validationRules());
        $operator = Operator::where('uuid', $uuid)->firstOrFail();
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can update without additional checks
            } else {
                // Regular admin authorization check
                if ($operator->creator_type !== $creatorType || $operator->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this operator.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            if ($operator->creator_type !== $creatorType || $operator->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this operator.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Update the operator's details
        $operator->fill($validatedData);
        $operator->updater()->associate($currentUser); // Associate the updater
        $operator->save();

        // Return a success response
        return response()->json(['success' => true, 'message' => 'operator updated successfully.', 'operator' => $operator], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operator $operator)
    {
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any operator without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($operator->creator_type !== $creatorType || $operator->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this operator.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            if ($operator->creator_type !== $creatorType || $operator->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this operator.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Delete the operator
        $operator->delete();
        // Return a success response
        return response()->json(['success' => true, 'message' => 'operator deleted successfully.'], 200);
    }

    public function trashedOperatorsCount()
    {
        // Get the count of soft-deleted Operators
        $trashedCount = Operator::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }
    public function trashed(Request $request)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all soft-deleted operators
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed operators without additional checks
                $operatorsQuery = Operator::onlyTrashed();
            } else {
                // Regular admin authorization check
                $operatorsQuery = Operator::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $operatorsQuery = Operator::onlyTrashed()
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
            $operatorsQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your Operator fields
        }

        // Apply sorting
        $operatorsQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $operators = $operatorsQuery->with('creator:id,name','user:id,name')->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $operators->items(), // Current page items
            'total' => $operators->total(), // Total number of trashed records
        ]);
    }

    public function forceDelete($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed operators
            if ($currentUser->role === 'superadmin') {
                $operator = Operator::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $operator = Operator::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $operator = Operator::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Permanently delete the operator
        $operator->forceDelete(); // Permanent delete

        return response()->json(['message' => 'Operator permanently deleted'], Response::HTTP_OK);
    }
    // Restore a soft-deleted operator
    public function restore($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed operators
            if ($currentUser->role === 'superadmin') {
                $restored = Operator::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = Operator::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = Operator::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($restored) {
            return response()->json(['message' => 'Operator restored successfully'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Operator not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }


}
