<?php

namespace App\Http\Controllers;

use App\Models\MachineTag;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MachineTagController extends Controller
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
              $tagsQuery = MachineTag::query(); // No filters applied
          } else {
              // If not superadmin, filter by creator type and id
              $tagsQuery = MachineTag::where('creator_type', $creatorType)
                  ->where('creator_id', $currentUser->id);
          }
      } elseif (Auth::guard('user')->check()) {
          $currentUser = Auth::guard('user')->user();
          $creatorType = User::class;
          // For regular users, filter by creator type and id
          $tagsQuery = MachineTag::where('creator_type', $creatorType)
              ->where('creator_id', $currentUser->id);
      } else {
          return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
      }
      // Apply search if the search term is not empty
      if (!empty($search)) {
          $tagsQuery->where('name', 'LIKE', '%' . $search . '%');
      }
      // Apply sorting
      $tagsQuery->orderBy($sortBy, $sortOrder);
      // Paginate results
      $tags = $tagsQuery->with(['creator','company'])->paginate($itemsPerPage);
      // Return the response as JSON
      return response()->json([
          'items' => $tags->items(), // Current page items
          'total' => $tags->total(), // Total number of records
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
        $validatedData = $request->validate(MachineTag::validationRules());

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        
            // Optional: Add specific checks for non-superadmin roles
            if ($creator->role !== 'superadmin') {
                // Add regular admin restrictions here if needed
                // For example: check permissions or restrict company assignments
            }
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        
            // Optional: Add restrictions or checks for users
            // For example: limit access to specific companies or tags
        } else {
            // Return unauthorized response if no valid user is authenticated
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        // Create the MachineTag and associate it with the creator
        try {
            $tag = new MachineTag($validatedData);
            $tag->uuid = HelperController::generateUuid(); // Generate a unique UUID
            $tag->company_id = $validatedData['company_id'];
        
            // Associate the creator polymorphically
            $tag->creator()->associate($creator);
        
            // Assign the updater (typically the creator during creation)
            $tag->updater()->associate($creator);
        
            // Save the MachineTag to the database
            $tag->save();
        
            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Machine Tag created successfully.',
                'data'    => $tag, // Optionally include the created tag in the response
            ], 201);
        } catch (\Exception $e) {
            // Handle potential errors during tag creation
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Machine Tag. ' . $e->getMessage(),
            ], 500);
        }
        
    }

    /**
     * Display the specified resource.
     */
    public function show(MachineTag $machineTag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        // Retrieve the machine tag by UUID
        $machineTag = MachineTag::where('uuid', $uuid)->first();

        // Check if the machine tag exists
        if (!$machineTag) {
            return response()->json(['success' => false, 'message' => 'Machine Tag not found'], Response::HTTP_NOT_FOUND);
        }

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any machine tag
                return response()->json([
                    'success' => true,
                    'machineTag' => $machineTag
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if the machine tag belongs to the current user or admin
        if ($machineTag->creator_type !== $creatorType || $machineTag->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this tag.'], 403);
        }

        // Return the machine tag data if authorized
        return response()->json([
            'success' => true,
            'machineTag' => $machineTag
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        // Validate the incoming request data
        $validatedData = $request->validate(MachineTag::validationRules());

        // Find the MachineTag by UUID
        $tag = MachineTag::where('uuid', $uuid)->first();

        if (!$tag) {
            return response()->json(['success' => false, 'message' => 'Machine Tag not found'], 404);
        }

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
            
            // Optional: Add specific checks for non-superadmin roles
            if ($creator->role !== 'superadmin') {
                // Add regular admin restrictions here if needed
                // For example: check permissions or restrict company assignments
            }
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
            
            // Optional: Add restrictions or checks for users
            // For example: limit access to specific companies or tags
        } else {
            // Return unauthorized response if no valid user is authenticated
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if the authenticated user is authorized to update the tag
        if ($tag->creator_type !== get_class($creator) || $tag->creator_id !== $creator->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this tag.'], 403);
        }

        // Start the update operation
        try {
            // Update the tag with the validated data
            $tag->fill($validatedData);
            
            // Optionally update the company_id if provided in the request
            if (isset($validatedData['company_id'])) {
                $tag->company_id = $validatedData['company_id'];
            }
            
            // Optionally, update the updater (usually the creator)
            $tag->updater()->associate($creator);
            
            // Save the updated tag to the database
            $tag->save();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Machine Tag updated successfully.',
                'data'    => $tag, // Include the updated tag in the response
            ], 200);
        } catch (\Exception $e) {
            // Handle potential errors during the update
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Machine Tag. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        // Find the MachineTag by UUID
        $tag = MachineTag::where('uuid', $uuid)->first();

        if (!$tag) {
            return response()->json(['success' => false, 'message' => 'Machine Tag not found'], 404);
        }

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
            
            // Optional: Add specific checks for non-superadmin roles
            if ($creator->role !== 'superadmin') {
                // Add regular admin restrictions here if needed
                // For example: check permissions or restrict company assignments
            }
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
            
            // Optional: Add restrictions or checks for users
            // For example: limit access to specific companies or tags
        } else {
            // Return unauthorized response if no valid user is authenticated
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if the authenticated user is authorized to delete the tag
        if ($tag->creator_type !== get_class($creator) || $tag->creator_id !== $creator->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this tag.'], 403);
        }

        // Start the delete operation
        try {
            // Delete the tag
            $tag->delete();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Machine Tag deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            // Handle potential errors during deletion
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Machine Tag. ' . $e->getMessage(),
            ], 500);
        }
    }
    public function trashedCount()
    {
        // Get the count of soft-deleted brands
        $trashedCount = MachineTag::onlyTrashed()->count();

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

            // Superadmin check: Allow access to all soft-deleted machine tags
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed machine tags without additional checks
                $machineTagsQuery = MachineTag::onlyTrashed();
            } else {
                // Regular admin authorization check
                $machineTagsQuery = MachineTag::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $machineTagsQuery = MachineTag::onlyTrashed()
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
            $machineTagsQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your MachineTag fields
        }

        // Apply sorting
        $machineTagsQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $machineTags = $machineTagsQuery->with(['creator','company'])->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $machineTags->items(), // Current page items
            'total' => $machineTags->total(), // Total number of trashed records
        ]);

    }
    public function forceDelete($uuid)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed machine tags
            if ($currentUser->role === 'superadmin') {
                $machineTag = MachineTag::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
            } else {
                // Regular admin authorization check
                $machineTag = MachineTag::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->where('uuid', $uuid)
                    ->firstOrFail();
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $machineTag = MachineTag::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->where('uuid', $uuid)
                ->firstOrFail();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Permanently delete the machine tag
            $machineTag->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Machine tag permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting machine tag: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function restoreTrashed($uuid)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed machine tags
            if ($currentUser->role === 'superadmin') {
                $restored = MachineTag::onlyTrashed()->where('uuid', $uuid)->firstOrFail()->restore();
            } else {
                // Regular admin authorization check
                $restored = MachineTag::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->where('uuid', $uuid)
                    ->firstOrFail()
                    ->restore();
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = MachineTag::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->where('uuid', $uuid)
                ->firstOrFail()
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Handle successful or failed restore operation
        if ($restored) {
            return response()->json(['message' => 'Machine Tag restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Machine Tag not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }


}