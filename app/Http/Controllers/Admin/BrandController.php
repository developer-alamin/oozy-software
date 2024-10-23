<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrandController extends Controller
{


    public function index(Request $request)
    {
        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by name
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is ascending
        $search       = $request->input('search', ''); // Search term, default is empty

        // Query brands with pagination, sorting, and search
        $brandsQuery = Brand::query();
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

        Brand::create($validatedData);
        return response()->json(['success' => true,'message' => 'Brand created successfully.'], 201);
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
        return response()->json([
            'success' => true,
            'brand' => $brand
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validatedData = $request->validate(Brand::validationRules());

        // Update the product model with the validated data
        $brand->update($validatedData);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully.',
            // 'category' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
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
