<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Requests\MovementStoreRequest;
use App\Models\Admin;
use App\Models\Movement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MachineMovementController extends Controller
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
                // Superadmin retrieves all movements
                $movementsQuery = Movement::query();
            } else {
                // Non-superadmin filters by creator
                $movementsQuery = Movement::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular users filter by creator
            $movementsQuery = Movement::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Apply search if the search term is not empty
        if (!empty($search)) {
            $movementsQuery->where('name', 'LIKE', '%' . $search . '%');
        }

        // Apply sorting
        $movementsQuery->orderBy($sortBy, $sortOrder);

        // Paginate results with eager loading for relationships
        $movements = $movementsQuery
            ->with(['machine:id,name', 'line.units.floors.factories.user:id,name'])->where('status','Assign')
            ->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $movements->items(), // Current page items
            'total' => $movements->total(), // Total number of records
        ]);
    }

     /**
     * Display a listing of the resource.
     */
    public function historyIndex(Request $request)
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
                // Superadmin retrieves all movements
                $movementsQuery = Movement::query();
            } else {
                // Non-superadmin filters by creator
                $movementsQuery = Movement::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular users filter by creator
            $movementsQuery = Movement::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Apply search if the search term is not empty
        if (!empty($search)) {
            $movementsQuery->where('name', 'LIKE', '%' . $search . '%');
        }

        // Apply sorting
        $movementsQuery->orderBy($sortBy, $sortOrder);

        // Paginate results with eager loading for relationships
        $movements = $movementsQuery
            ->with(['machine:id,name', 'line.units.floors.factories.user:id,name'])->where('status','Transferred')
            ->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $movements->items(), // Current page items
            'total' => $movements->total(), // Total number of records
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
    public function store(MovementStoreRequest $request)
    {
        $validatedData = $request->validated();
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
             $creator = Auth::guard('admin')->user();
             // Check if the admin is a superadmin
             if ($creator->role === 'superadmin') {
                 // Superadmin can create movement without additional checks
             } else {
                 // Regular admin authorization check can be implemented here if needed
             }

         } elseif (Auth::guard('user')->check()) {
             $creator = Auth::guard('user')->user();
             // If you want users to have specific restrictions, implement checks here
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }

         $existingMovement = Movement::where('machine_id', $validatedData['machine_id'] ?? null)
        ->where('status', 'Assign')
        ->first();
        
        if ($existingMovement) {
            // Update the status of the existing movement to "Transferred"
            $existingMovement->status = 'Transferred';
            $existingMovement->updater()->associate($creator); // Associate updater
            $existingMovement->save(); // Save the changes
        }

        // If such a movement exists, set the new movement's status to "Transferred"
        $status = $existingMovement ? 'Transferred' : "Assign";

         // Create the movement and associate it with the creator
        $movement         = new Movement($validatedData);
        $movement->uuid   = HelperController::generateUuid();
        $movement->status = "Assign"; 
        $movement->creator()->associate($creator);  // Assign creator polymorphically
        $movement->updater()->associate($creator);  // Associate the updater
        $movement->save(); // Save the movement to the database
        // Return a success response
        return response()->json(['success' => true, 'message' => 'movement created successfully.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Movement $movement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movement $movement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movement $movement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movement $movement)
    {
        //
    }
}
