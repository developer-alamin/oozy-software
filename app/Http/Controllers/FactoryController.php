<?php

namespace App\Http\Controllers;

use App\Http\Requests\FactoryStoreRequest;
use App\Http\Requests\FactoryUpdateRequest;
use App\Models\Admin;
use App\Models\Factory;
use App\Models\Floor;
use App\Models\Line;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Import the DB facade
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class FactoryController extends Controller
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
        $factoryCode  = $request->input('factory_code', '');

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // If superadmin, retrieve all technicians
                $factoriesQuery = Factory::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $factoriesQuery = Factory::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $factoriesQuery = Factory::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($factoryCode)) {
            $factoriesQuery->where('factory_code', $factoryCode);
        }elseif(!empty($search)){
            $factoriesQuery->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                      ->orWhere('email', 'LIKE', "%search%")
                      ->orWhere('factory_code', 'LIKE', "%$search%")
                      ->orWhere('phone', 'LIKE', "%search%")
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'LIKE', "%$search%");
                      });
            });
        }
        // Apply sorting
        $factoriesQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $factories = $factoriesQuery
        ->with(['creator:id,name','company:id,name']) // Eager load relationships and creator's name
        ->paginate($itemsPerPage, ['*'], 'page', $page);
        // Return the response as JSON
        return response()->json([
            'items' => $factories->items(), // Current page items
            'total' => $factories->total(), // Total number of records
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
    public function store(FactoryStoreRequest $request)
    {
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
            // Additional checks can be implemented here for admin roles if needed
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
            // User-specific checks can be added here if needed
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        try {


        

            DB::beginTransaction();
            $validated = $request->validated();
            // $factory         = Factory::create($factoryData);
            $factory = new Factory([
                'uuid'             => HelperController::generateUuid(),
                'company_id'       => $validated['company_id'], // Convert array to JSON
                'name'             => $validated['name'],
                'factory_code'     => $validated['factory_code'],
                'factory_owner'    => $validated['factory_owner'],
                'factory_size'     => $validated['factory_size'],
                'factory_capacity' => $validated['factory_capacity'],
                'email'            => $validated['email'],
                'phone'            => $validated['phone'],
                'location'         => $validated['location'],
                'note'             => $validated['note'],
                'status'           => $validated['status'],
            ]);
            // Associate creator and updater with the factory
            $factory->creator()->associate($creator);
            $factory->updater()->associate($creator);
            $factory->save();

            // $factory->floors()->sync($request->floor_ids);
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Factory created successfully.','factory' => $factory],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create factory', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(Factory $factory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {

        $factory = Factory::where('uuid', $uuid)->firstOrFail();
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any factory
                return response()->json([
                    'success' => true,
                    'factory' => $factory
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the factory belongs to the current user or admin
        if ($factory->creator_type !== $creatorType || $factory->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this factory.'], 403);
        }
        // Return the factory data if authorized
        return response()->json([
            'success' => true,
            'factory' => $factory
        ], Response::HTTP_OK);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(FactoryUpdateRequest $request, $uuid)
    {
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $factory = Factory::where('uuid', $uuid)->firstOrFail();

            // Update factory attributes
            $factory->fill([
                'company_id'       => $validated['company_id'],
                'name'             => $validated['name'],
                'factory_code'     => $validated['factory_code'],
                'factory_owner'    => $validated['factory_owner'],
                'factory_size'     => $validated['factory_size'],
                'factory_capacity' => $validated['factory_capacity'],
                'email'            => $validated['email'],
                'phone'            => $validated['phone'],
                'location'         => $validated['location'],
                'note'             => $validated['note'],
                'status'           => $validated['status'],
            ]);

            // Update the updater relationship
            $factory->updater()->associate($creator);
            $factory->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Factory updated successfully.',
                'factory' => $factory,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update factory',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factory $factory)
    {
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any factory without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($factory->creator_type !== $creatorType || $factory->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this factory.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($factory->creator_type !== $creatorType || $factory->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this factory.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $factory->delete();
            return response()->json([
                'success' => true,
                'message' => 'factory deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting factory: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function trashedFactoriesCount()
    {
        // Get the count of soft-deleted Factorys
        $trashedCount = Factory::onlyTrashed()->count();
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

            // Superadmin check: Allow access to all soft-deleted factorys
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed factorys without additional checks
                $factorysQuery = Factory::onlyTrashed();
            } else {
                // Regular admin authorization check
                $factorysQuery = Factory::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $factorysQuery = Factory::onlyTrashed()
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
            $factorysQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your Operator fields
        }

        // Apply sorting
        $factorysQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $factorys = $factorysQuery->with('creator:id,name','company:id,name')->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $factorys->items(), // Current page items
            'total' => $factorys->total(), // Total number of trashed records
        ]);
    }

    public function forceDelete($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed factorys
            if ($currentUser->role === 'superadmin') {
                $operator = Factory::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $operator = Factory::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $operator = Factory::onlyTrashed()
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

            // Superadmin check: Allow access to all trashed factorys
            if ($currentUser->role === 'superadmin') {
                $restored = Factory::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = Factory::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = Factory::onlyTrashed()
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

    public function getCompanys(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for users by name with a limit
        $users = User::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the users as JSON
        return response()->json($users);
    }
    public function getFloors(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for floors by name with a limit
        $floors = Floor::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the floors as JSON
        return response()->json($floors);
    }
    public function getUnits(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for units by name with a limit
        $units  = Unit::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the units as JSON
        return response()->json($units);
    }
    public function getLines(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for lines by name with a limit
        $lines  = Line::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the lines as JSON
        return response()->json($lines);
    }
}
