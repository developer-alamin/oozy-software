<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Admin;
use App\Models\BreakDownProblemNote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BreakDownProblemNoteController extends Controller
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
                $breakDownProblemNotesQuery = BreakDownProblemNote::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $breakDownProblemNotesQuery = BreakDownProblemNote::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $breakDownProblemNotesQuery = BreakDownProblemNote::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $breakDownProblemNotesQuery->where('break_down_problem_note', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $breakDownProblemNotesQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $breakDownProblemNotes = $breakDownProblemNotesQuery->with(['creator','company'])->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $breakDownProblemNotes->items(), // Current page items
            'total' => $breakDownProblemNotes->total(), // Total number of records
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
        $validatedData = $request->validate(BreakDownProblemNote::validationRules());
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
         $breakDownProblemNote       = new BreakDownProblemNote($validatedData);
         $breakDownProblemNote->uuid = HelperController::generateUuid();
         $breakDownProblemNote->company_id = $validatedData['company_id'];
         $breakDownProblemNote->creator()->associate($creator);  // Assign creator polymorphically
         $breakDownProblemNote->updater()->associate($creator);  // Associate the updater
         $breakDownProblemNote->save(); // Save the technician to the database
         // Return a success response
         return response()->json(['success' => true, 'message' => 'BreakDown Problem Note created successfully.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BreakDownProblemNote $breakDownProblemNote)
    {
        return response()->json($breakDownProblemNote,200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request,$uuid)
    {
        $breakdownproblem = BreakDownProblemNote::where('uuid',$uuid)->first();
         // Determine the authenticated user (either from 'admin' or 'user' guard)
         if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any category
                return response()->json([
                    'success'  => true,
                    'item' => $breakdownproblem
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the category belongs to the current user or admin
        if ($breakdownproblem->creator_type !== $creatorType || $category->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this category.'], 403);
        }
       // Return the brand data if authorized
       return response()->json([
            'success' => true,
            'item'   => $breakdownproblem
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $breakdownproblem = BreakDownProblemNote::where('uuid',$uuid)->first();
        $validatedData = $request->validate(BreakDownProblemNote::validationRules());
        
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can update without additional checks
            } else {
                // Regular admin authorization check
                if ($breakdownproblem->creator_type !== $creatorType || $category->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this category.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            if ($breakdownproblem->creator_type !== $creatorType || $category->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this category.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }


        $breakdownproblem->fill($validatedData);
        $breakdownproblem->company_id = $validatedData['company_id'];
    
        $breakdownproblem->updater()->associate($currentUser); // Associate the updater
         // return response()->json($breakdownproblem->updater(),200);
        $breakdownproblem->save();

       return response()->json(['success' => true, 'message' => 'BreakdownProblem updated successfully.', 'item' => $breakdownproblem], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        $breakdownproblem = BreakDownProblemNote::where("uuid",$uuid)->first();

        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any brand without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($breakdownproblem->creator_type !== $creatorType || $breakdownproblem->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this brand.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($breakdownproblem->creator_type !== $creatorType || $breakdownproblem->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this brand.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $breakdownproblem->delete();
            return response()->json([
                'success' => true,
                'message' => 'Breakdownproblem deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Breakdownproblem: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function trashedProblemCount()
    {
        // Get the count of soft-deleted brands
        $trashedCount = BreakDownProblemNote::onlyTrashed()->count();

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

            // Superadmin check: Allow access to all soft-deleted technicians
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed technicians without additional checks
                $breakdownproblemQuery = BreakDownProblemNote::onlyTrashed();
            } else {
                // Regular admin authorization check
                $breakdownproblemQuery = BreakDownProblemNote::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $breakdownproblemQuery = BreakDownProblemNote::onlyTrashed()
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
            $breakdownproblemQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your brand fields
        }

        // Apply sorting
        $breakdownproblemQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $brands = $breakdownproblemQuery->with(['creator','company'])->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $brands->items(), // Current page items
            'total' => $brands->total(), // Total number of trashed records
        ]);


    }
    public function restoreTrashed(Request $request ,$uuid)
    {
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
    
            // Superadmin check: Allow access to all trashed records
            if ($currentUser->role === 'superadmin') {
                $restored = BreakDownProblemNote::onlyTrashed()->findOrFail($uuid)->restore();
            } else {
                // Regular admin authorization: Check creator_id and creator_type
                $restored = BreakDownProblemNote::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($uuid)
                    ->restore();
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
    
            // Regular user authorization: Check creator_id and creator_type
            $restored = BreakDownProblemNote::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($uuid)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }
    
        if ($restored) {
            return response()->json(['success' => true, 'message' => 'BreakDownProblemNote restored successfully'], Response::HTTP_OK);
        }
    
        return response()->json(['success' => false, 'message' => 'BreakDownProblemNote not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }
    public function forceDelete($uuid)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed Breakdown Problem Notes
            if ($currentUser->role === 'superadmin') {
                $breakDownProblemNote = BreakDownProblemNote::onlyTrashed()->findOrFail($uuid);
            } else {
                // Regular admin authorization check
                $breakDownProblemNote = BreakDownProblemNote::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($uuid);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $breakDownProblemNote = BreakDownProblemNote::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($uuid);
        } else {
            // If no valid guard is authenticated, return unauthorized response
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            // Permanently delete the Breakdown Problem Note
            $breakDownProblemNote->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Breakdown Problem Note permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Breakdown Problem Note: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
