<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Http\Response;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by name
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is ascending
        $search       = $request->input('search', ''); // Search term, default is empty
    
        // Query brands with pagination, sorting, and search
        $groupsQuery = Group::query();
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $groupsQuery->where('name', 'LIKE', '%' . $search . '%');
        }
    
        // Apply sorting
        $groupsQuery->orderBy($sortBy, $sortOrder);
    
        // Paginate results
        $groups = $groupsQuery->paginate($itemsPerPage);
    
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
        
        Group::create($validatedData);
        return response()->json(['success' => true,'message' => 'Group created successfully.'], 200);
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
         return response()->json([
            'success' => true,
            'group' => $group
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
         $validatedData = $request->validate(Group::validationRules());

        // Update the product model with the validated data
        $group->update($validatedData);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Group updated successfully.',
            // 'category' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
         
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
                'message' => 'Error deleting gropu: ' . $e->getMessage()
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
        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by name
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is descending
        $search       = $request->input('search', ''); // Search term, default is empty

        // Query only soft deleted brands with pagination, sorting, and search
        $groupsQuery = Group::onlyTrashed(); // Fetch only soft-deleted records

        // Apply search if the search term is not empty
        if (!empty($search)) {
            $groupsQuery->where('name', 'LIKE', '%' . $search . '%');
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
        // Attempt to restore the brand using the static method on the model
        $restored = Group::restoreGroup($id);

        if ($restored) {
            return response()->json(['message' => 'Group restored successfully'], 200);
        }

        return response()->json(['message' => 'Group not found or is not trashed'], 404);
    }

    // Permanently delete a group from trash
     public function groupsforceDelete($id)
     {
         $group = Group::onlyTrashed()->findOrFail($id);
         $group->forceDelete(); // Permanent delete
 
         return response()->json(['message' => 'Group permanently deleted']);
     }
}
