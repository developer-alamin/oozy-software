<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{


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
                $brandsQuery = Brand::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $brandsQuery = Brand::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $brandsQuery = Brand::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $brandsQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $brandsQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $brands = $brandsQuery->with('creator:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $brands->items(), // Current page items
            'total' => $brands->total(), // Total number of records
        ]);
    }

    public function trashed(Request $request)
    {
        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by name
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is descending
        $search       = $request->input('search', ''); // Search term, default is empty

        // Query only soft deleted brands with pagination, sorting, and search
        $brandsQuery = Brand::onlyTrashed(); // Fetch only soft-deleted records
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $brandsQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $brandsQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $brands = $brandsQuery->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $brands->items(), // Current page items
            'total' => $brands->total(), // Total number of trashed records
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
        $validatedData = $request->validate(Brand::validationRules());
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
         $brand = new Brand($validatedData);
         $brand->creator()->associate($creator);  // Assign creator polymorphically
         $brand->updater()->associate($creator);  // Associate the updater
         $brand->save(); // Save the technician to the database
         // Return a success response
         return response()->json(['success' => true, 'message' => 'Brand created successfully.'], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        // return response()->json([
        //     'success' => true,
        //     'brand' => $brand
        // ], Response::HTTP_OK);

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any brand
                return response()->json([
                    'success' => true,
                    'brand' => $brand
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the brand belongs to the current user or admin
        if ($brand->creator_type !== $creatorType || $brand->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this brand.'], 403);
        }
        // Return the brand data if authorized
        return response()->json([
            'success' => true,
            'brand'   => $brand
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {

         // Validate the incoming request data
         $validatedData = $request->validate(Brand::validationRules());

         // Determine the authenticated user (either from 'admin' or 'user' guard)
         if (Auth::guard('admin')->check()) {
             $currentUser = Auth::guard('admin')->user();
             $creatorType = Admin::class;

             // Check if the admin is a superadmin
             if ($currentUser->role === 'superadmin') {
                 // Superadmin can update without additional checks
             } else {
                 // Regular admin authorization check
                 if ($brand->creator_type !== $creatorType || $brand->creator_id !== $currentUser->id) {
                     return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this brand.'], 403);
                 }
             }

         } elseif (Auth::guard('user')->check()) {
             $currentUser = Auth::guard('user')->user();
             $creatorType = User::class;

             // Regular user authorization check
             if ($brand->creator_type !== $creatorType || $brand->creator_id !== $currentUser->id) {
                 return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this brand.'], 403);
             }
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }
         // Update the brand's details
         $brand->fill($validatedData);
         $brand->updater()->associate($currentUser); // Associate the updater
         $brand->save();

         // Return a success response
         return response()->json(['success' => true, 'message' => 'Brand updated successfully.', 'brand' => $brand], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {

        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any brand without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($brand->creator_type !== $creatorType || $brand->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this brand.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($brand->creator_type !== $creatorType || $brand->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this brand.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $brand->delete();
            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Brand: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function trashedBrandsCount()
    {
        // Get the count of soft-deleted brands
        $trashedCount = Brand::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }
     // Permanently delete a brand from trash
     public function forceDelete($id)
     {
         $brand = Brand::onlyTrashed()->findOrFail($id);
         $brand->forceDelete(); // Permanent delete

         return response()->json(['message' => 'Brand permanently deleted']);
     }

     // Restore a soft-deleted brand
     public function restore($id)
    {
        // Attempt to restore the brand using the static method on the model
        $restored = Brand::restoreBrand($id);

        if ($restored) {
            return response()->json(['message' => 'Brand restored successfully'], 200);
        }

        return response()->json(['message' => 'Brand not found or is not trashed'], 404);
    }

}