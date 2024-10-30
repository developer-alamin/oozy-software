<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Line;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LineController extends Controller
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
                $linesQuery = Line::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $linesQuery = Line::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $linesQuery = Line::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $linesQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $linesQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $lines = $linesQuery->with('creator:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $lines->items(), // Current page items
            'total' => $lines->total(), // Total number of records
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


        $validatedData = $request->validate(Line::validationRules());
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



         $line = new Line($validatedData);
         $line->creator()->associate($creator);  // Assign creator polymorphically
         $line->updater()->associate($creator);  // Associate the updater
         $line->save(); // Save the technician to the database
        return response()->json(['success' => true,'message' => 'Line created successfully.'], 200);
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
    public function edit(Line $line)
    {
        //  return response()->json([
        //     'success' => true,
        //     'line' => $line
        // ], Response::HTTP_OK);

         if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any line
                return response()->json([
                    'success' => true,
                    'line'    => $line
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the line belongs to the current user or admin
        if ($line->creator_type !== $creatorType || $line->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this line.'], 403);
        }
        // Return the line data if authorized
        return response()->json([
            'success' => true,
            'line'   => $line
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Line $line)
    {
        // Validate the incoming request data
         $validatedData = $request->validate(Line::validationRules());

         // Determine the authenticated user (either from 'admin' or 'user' guard)
         if (Auth::guard('admin')->check()) {
             $currentUser = Auth::guard('admin')->user();
             $creatorType = Admin::class;

             // Check if the admin is a superadmin
             if ($currentUser->role === 'superadmin') {
                 // Superadmin can update without additional checks
             } else {
                 // Regular admin authorization check
                 if ($line->creator_type !== $creatorType || $line->creator_id !== $currentUser->id) {
                     return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this line.'], 403);
                 }
             }

         } elseif (Auth::guard('user')->check()) {
             $currentUser = Auth::guard('user')->user();
             $creatorType = User::class;

             // Regular user authorization check
             if ($line->creator_type !== $creatorType || $line->creator_id !== $currentUser->id) {
                 return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this line.'], 403);
             }
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }
         // Update the line's details
         $line->fill($validatedData);
         $line->updater()->associate($currentUser); // Associate the updater
         $line->save();

         // Return a success response
         return response()->json(['success' => true, 'message' => 'Line updated successfully.', 'line' => $line], 200);




        //  $validatedData = $request->validate(Line::validationRules());

        //  // Update the product model with the validated data
        //  $line->update($validatedData);

        // // Return a success response
        //  return response()->json([
        //    'success' => true,
        //     'message' => 'Line updated successfully.',
        //  ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Line $line)
    {
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any brand without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($line->creator_type !== $creatorType || $line->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this brand.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($line->creator_type !== $creatorType || $line->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this brand.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $line->delete();
            return response()->json([
                'success' => true,
                'message' => 'Line deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Line: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function lineTrashedCount()
    {
         // Get the count of soft-deleted brands
        $trashedCount = Line::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }
      public function lineTrashed(Request $request)
    {

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all soft-deleted technicians
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed technicians without additional checks
                $linesQuery = Line::onlyTrashed();
            } else {
                // Regular admin authorization check
                $linesQuery = Line::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $linesQuery = Line::onlyTrashed()
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
            $linesQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your brand fields
        }

        // Apply sorting
        $linesQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $lines = $linesQuery->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $lines->items(), // Current page items
            'total' => $lines->total(), // Total number of trashed records
        ]);
    }

    public function lineRestore($id)
    {

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $restored = Line::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = Line::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = Line::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if ($restored) {
            return response()->json(['message' => 'Brand restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Brand not found or is not trashed'], Response::HTTP_NOT_FOUND);





        // Attempt to restore the brand using the static method on the model
        $lineRestored = Line::onlyTrashed()->find($id);

        if ($lineRestored) {
            $lineRestored->restore();
            return response()->json(['message' => 'Brand restored successfully'], 200);
        }

        return response()->json(['message' => 'Brand not found or is not trashed'], 404);
    }

    public function LineForceDelete($id)
     {


        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {

            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $line = Line::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $line = Line::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

            } elseif (Auth::guard('user')->check()) {
                $currentUser = Auth::guard('user')->user();
                $creatorType = User::class;

                // Regular user authorization check
                $line = line::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $line->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Line permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Brand: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

         return response()->json(['message' => 'Line permanently deleted']);
     }
}