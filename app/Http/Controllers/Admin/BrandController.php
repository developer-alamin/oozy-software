<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     // Validate incoming request parameters

    //     $request->validate([
    //         'search'       => 'nullable|string|max:255',
    //         'itemsPerPage' => 'nullable|integer|min:1|max:100',
    //     ]);

    //     $query = Brand::query();

    //     // Search functionality
    //     if ($request->filled('search')) {
    //         $query->where('name', 'like', '%' . $request->search . '%')
    //             ->orWhere('description', 'like', '%' . $request->search . '%');
    //     }

    //     // Sorting
    //     if ($request->filled('sortBy')) {
    //         $query->orderBy($request->sortBy, $request->sortDesc ? 'desc' : 'asc');
    //     }

    //     // Pagination
    //     $itemsPerPage = $request->input('itemsPerPage', 15); // Default items per page
    //     $brands   = $query->paginate($itemsPerPage);

    //     return response()->json([
    //         'brands'     => $brands->items(),
    //         'total'      => $brands->total(), // Total count for pagination
    //     ]);
    // }

    public function index(Request $request)
    {
        // Validate incoming request parameters
        $request->validate([
            'search'       => 'nullable|string|max:255',
            'itemsPerPage' => 'nullable|integer|min:1|max:100',
           
        ]);

        // Create a base query for the Brand model
        $query = Brand::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Sorting
        if ($request->filled('sortBy')) {
            $query->orderBy($request->sortBy, $request->sortDesc ? 'desc' : 'asc');
        }

        // Pagination
        $itemsPerPage = $request->input('itemsPerPage', 15); // Default items per page
        $brands = $query->paginate($itemsPerPage);

        // Return the paginated results as a JSON response
        return response()->json([
            'brands'     => $brands->items(), // Array of brand items
            'total'      => $brands->total(), // Total count for pagination
            'currentPage' => $brands->currentPage(),
            'lastPage'   => $brands->lastPage(),
            'perPage'    => $brands->perPage(),
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        //
    }
}
