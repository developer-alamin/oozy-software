<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Models\Category;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
/**
 * @OA\Tag(
 *     name="Technicians",
 *     description="API Endpoints for managing technicians"
 * )
 */
class TechnicianController extends Controller
{
    /**
   * @OA\Get(
   *     path="/technician",
   *     tags={"Technicians"},
   *     summary="Retrieve a list of technicians",
   *     description="Returns a paginated list of technicians.",
   *     security={{"bearerAuth": {}}},
   *     @OA\Parameter(
  *         name="page",
  *         in="query",
  *         description="Page number for pagination",
  *         required=false,
  *         @OA\Schema(type="integer", example=1)
  *     ),
  *     @OA\Parameter(
  *         name="itemsPerPage",
  *         in="query",
  *         description="Number of items per page",
  *         required=false,
  *         @OA\Schema(type="integer", example=10)
  *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Successful operation",
   *         @OA\JsonContent(
   *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
   *             @OA\Property(property="links", type="object"),
   *             @OA\Property(property="meta", type="object")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthorized"
   *     )
   * )
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
     * @OA\Post(
     *     path="/technician",
     *     tags={"Technicians"},
     *     summary="Create a new technician",
     *     description="Adds a new technician to the system. The request requires authentication and certain fields to be validated before creating the technician.Technician types can be 'General', 'Special', or 'Manager'.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_id", "factory_id", "group_id", "name", "type"},
     *
     *             @OA\Property(property="company_id", type="integer", example=1, description="ID of the company"),
     *             @OA\Property(property="factory_id", type="integer", example=2, description="ID of the factory"),
     *             @OA\Property(property="group_id", type="integer", example=3, description="ID of the group"),
     *             @OA\Property(property="name", type="string", example="John Doe", description="Name of the technician"),
     *             @OA\Property(property="type", type="string", enum={"General", "Special", "Manager"}, example="General",description="Type of the technician"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com", description="Technician's email address"),
     *             @OA\Property(property="phone", type="string", example="1234567890", description="Technician's phone number"),
     *             @OA\Property(property="photo", type="string", example="photo-url.jpg", description="Photo URL of the technician"),
     *             @OA\Property(property="address", type="string", example="123 Street, City", description="Technician's address"),
     *             @OA\Property(property="description", type="string", example="A skilled technician", description="Brief description"),
     *             @OA\Property(property="status", type="string", example="Available", description="Status of the technician"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Technician created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Technician created successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="uuid", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *                 @OA\Property(property="name", type="string", example="John Doe")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation errors."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error.")
     *         )
     *     )
     * )
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

    /**
     * @OA\Get(
     *     path="/technician/{uuid}/edit",
     *     tags={"Technicians"},
     *     summary="Get a technician for editing",
     *     description="Retrieve a technician's details by UUID to edit. Only authorized users can access this endpoint. Super admins can edit any technician.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *         description="The UUID of the technician to edit",
     *         @OA\Schema(type="string", example="cbc2fea1-2ede-4285-82bb-ec4004073344")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Technician data retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="technician",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="uuid", type="string", example="cbc2fea1-2ede-4285-82bb-ec4004073344"),
     *                 @OA\Property(property="name", type="string", example="Jane Doe"),
     *                 @OA\Property(property="type", type="string", example="General"),
     *                 @OA\Property(property="creator_type", type="string", example="Admin"),
     *                 @OA\Property(property="creator_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="active"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T15:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized or Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Technician not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Technician not found")
     *         )
     *     )
     * )
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
    /**
 * @OA\Delete(
 *     path="/technician/{id}",
 *     tags={"Technicians"},
 *     summary="Delete a technician",
 *     description="Delete a technician by ID. Superadmins can delete any technician. Regular admins and users can only delete technicians they created.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the technician to delete",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Technician deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Technician deleted successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized or Forbidden",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Forbidden: You are not authorized to delete this technician.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Technician not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Technician not found.")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
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

    /**
 * @OA\Get(
 *     path="/technician/trashed",
 *     tags={"Technicians"},
 *     summary="List trashed (soft-deleted) technicians",
 *     description="Retrieve a list of soft-deleted technicians. Superadmins can view all trashed technicians, while regular admins and users can only view the ones they created.",
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         description="Page number for pagination",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="itemsPerPage",
 *         in="query",
 *         required=false,
 *         description="Number of items per page",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Parameter(
 *         name="sortBy",
 *         in="query",
 *         required=false,
 *         description="Column to sort by (default: created_at)",
 *         @OA\Schema(type="string", example="name")
 *     ),
 *     @OA\Parameter(
 *         name="sortOrder",
 *         in="query",
 *         required=false,
 *         description="Sort order (asc or desc)",
 *         @OA\Schema(type="string", example="desc")
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         required=false,
 *         description="Search term",
 *         @OA\Schema(type="string", example="John")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of trashed technicians",
 *         @OA\JsonContent(
 *             @OA\Property(property="items", type="array", @OA\Items(type="object")),
 *             @OA\Property(property="total", type="integer", example=50)
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Unauthorized")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
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
    /**
 * @OA\Delete(
 *     path="/technicians/trashed/{id}",
 *     tags={"Technicians"},
 *     summary="Permanently delete a trashed technician",
 *     description="Permanently delete a soft-deleted technician by ID. Superadmins can delete any trashed technician. Regular admins and users can only delete technicians they created.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the trashed technician to permanently delete",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Technician permanently deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Technician permanently deleted")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized or Forbidden",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Forbidden: You are not authorized to delete this technician.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Technician not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Technician not found.")
 *         )
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */

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