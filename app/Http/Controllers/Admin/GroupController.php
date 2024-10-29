<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


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
                $groupsQuery = Group::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $groupsQuery = Group::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $groupsQuery = Group::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $groupsQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $groupsQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $groups = $groupsQuery->with('creator:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $groups->items(), // Current page items
            'total' => $groups->total(), // Total number of records
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


        $validatedData = $request->validate(Group::validationRules());

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
         $group = new Group($validatedData);
         $group->creator()->associate($creator);  // Assign creator polymorphically
         $group->updater()->associate($creator);  // Associate the updater
         $group->save(); // Save the technician to the database
         // Return a success response
         return response()->json(['success' => true, 'message' => 'Group created successfully.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any group
                return response()->json([
                    'success' => true,
                    'group' => $group
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the group belongs to the current user or admin
        if ($group->creator_type !== $creatorType || $group->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this group.'], 403);
        }
        // Return the brand data if authorized
        return response()->json([
            'success' => true,
            'group'   => $group
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {


        // Validate the incoming request data
          $validatedData = $request->validate(Group::validationRules());

         // Determine the authenticated user (either from 'admin' or 'user' guard)
         if (Auth::guard('admin')->check()) {
             $currentUser = Auth::guard('admin')->user();
             $creatorType = Admin::class;

             // Check if the admin is a superadmin
             if ($currentUser->role === 'superadmin') {
                 // Superadmin can update without additional checks
             } else {
                 // Regular admin authorization check
                 if ($group->creator_type !== $creatorType || $group->creator_id !== $currentUser->id) {
                     return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this group.'], 403);
                 }
             }

         } elseif (Auth::guard('user')->check()) {
             $currentUser = Auth::guard('user')->user();
             $creatorType = User::class;

             // Regular user authorization check
             if ($group->creator_type !== $creatorType || $group->creator_id !== $currentUser->id) {
                 return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this group.'], 403);
             }
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }
         // Update the brand's details
         $group->fill($validatedData);
         $group->updater()->associate($currentUser); // Associate the updater
         $group->save();

         // Return a success response
         return response()->json(['success' => true, 'message' => 'Group updated successfully.', 'group' => $group], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any brand without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($group->creator_type !== $creatorType || $group->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this group.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($group->creator_type !== $creatorType || $group->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this group.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $group->delete();
            return response()->json([
                'success' => true,
                'message' => 'Group deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Group: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }  
        
    }
    public function groupsTrashedCount()
    {
        // Get the count of soft-deleted brands
        $trashedCount = Group::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }
    public function groupsTrashed(Request $request)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all soft-deleted technicians
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed technicians without additional checks
                $groupsQuery = Group::onlyTrashed();
            } else {
                // Regular admin authorization check
                $groupsQuery = Group::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $groupsQuery = Group::onlyTrashed()
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
            $groupsQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your brand fields
        }

        // Apply sorting
        $groupsQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $groups = $groupsQuery->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $groups->items(), // Current page items
            'total' => $groups->total(), // Total number of trashed records
        ]);
    }
    // Restore a soft-deleted brand
     public function groupsRestore($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $restored = Group::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = Group::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = Group::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if ($restored) {
            return response()->json(['message' => 'Group restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Group not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }

    // Permanently delete a group from trash
     public function groupsforceDelete($id)
     {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
                
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $group = Group::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $group = Group::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

            } elseif (Auth::guard('user')->check()) {
                $currentUser = Auth::guard('user')->user();
                $creatorType = User::class;

                // Regular user authorization check
                $group = Group::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
            // Delete the supplier
            $group->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Group permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Group: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
     }
}
