<?php

namespace App\Http\Controllers;

use App\Models\MachineStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\HelperController;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Response;

class MachineStatusController extends Controller
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
                $machinesQuery = MachineStatus::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $machinesQuery = MachineStatus::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $machinesQuery = MachineStatus::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $machinesQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $machinesQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $machines = $machinesQuery->with('creator:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $machines->items(), // Current page items
            'total' => $machines->total(), // Total number of records
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
        $validatedData = $request->validate(MachineStatus::validationRules());
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
         $machine       = new MachineStatus($validatedData);
         $machine->uuid = HelperController::generateUuid();
         $machine->creator()->associate($creator);  // Assign creator polymorphically
         $machine->updater()->associate($creator);  // Associate the updater
         $machine->save(); // Save the technician to the database
         // Return a success response
         return response()->json(['success' => true, 'message' => 'parse parse machine created successfully.'], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(MachineStatus $machineStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $machine = MachineStatus::where('uuid', $uuid)->firstOrFail();
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any machine
                return response()->json([
                    'success' => true,
                    'machine' => $machine
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the machine belongs to the current user or admin
        if ($machine->creator_type !== $creatorType || $machine->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this parse machine.'], 403);
        }
        // Return the machine data if authorized
        return response()->json([
            'success' => true,
            'machine'   => $machine
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $uuid)
    {
        $machine = MachineStatus::where('uuid', $uuid)->firstOrFail();
         // Validate the incoming request data
        $validatedData = $request->validate(MachineStatus::validationRules());

         // Determine the authenticated user (either from 'admin' or 'user' guard)
         if (Auth::guard('admin')->check()) {
             $currentUser = Auth::guard('admin')->user();
             $creatorType = Admin::class;

             // Check if the admin is a superadmin
             if ($currentUser->role === 'superadmin') {
                 // Superadmin can update without additional checks
             } else {
                 // Regular admin authorization check
                 if ($machine->creator_type !== $creatorType || $machine->creator_id !== $currentUser->id) {
                     return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this parse machine.'], 403);
                 }
             }

         } elseif (Auth::guard('user')->check()) {
             $currentUser = Auth::guard('user')->user();
             $creatorType = User::class;

             // Regular user authorization check
             if ($machine->creator_type !== $creatorType || $machine->creator_id !== $currentUser->id) {
                 return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this parse machine.'], 403);
             }
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }
         // Update the machine's details
         $machine->fill($validatedData);
         $machine->updater()->associate($currentUser); // Associate the updater
         $machine->save();

         // Return a success response
         return response()->json(['success' => true, 'message' => 'Parse machine updated successfully.', 'machine' => $machine], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineStatus $machineStatus)
    {
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any machine without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($machineStatus->creator_type !== $creatorType || $machineStatus->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this parse machine.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($machineStatus->creator_type !== $creatorType || $machineStatus->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this parse machine.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $machineStatus->delete();
            return response()->json([
                'success' => true,
                'message' => 'parse machine deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting parse machine: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


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
                $machinesQuery = MachineStatus::onlyTrashed();
            } else {
                // Regular admin authorization check
                $machinesQuery = MachineStatus::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $machinesQuery = MachineStatus::onlyTrashed()
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
            $machinesQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your machine fields
        }

        // Apply sorting
        $machinesQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $machines = $machinesQuery->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $machines->items(), // Current page items
            'total' => $machines->total(), // Total number of trashed records
        ]);


    }
    public function trashedMachineStatusCount()
    {
        // Get the count of soft-deleted machines
        $trashedCount = MachineStatus::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }
     // Permanently delete a machine from trash
    public function forceDelete($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {

            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $machine = MachineStatus::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $machine = MachineStatus::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

            } elseif (Auth::guard('user')->check()) {
                $currentUser = Auth::guard('user')->user();
                $creatorType = User::class;

                // Regular user authorization check
                $machine = MachineStatus::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $machine->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Parse machine permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting parse machine: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
    // Restore a soft-deleted machine
    public function restore($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $restored = MachineStatus::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = MachineStatus::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = MachineStatus::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if ($restored) {
            return response()->json(['message' => 'Parse machine restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Parse machine not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }
}