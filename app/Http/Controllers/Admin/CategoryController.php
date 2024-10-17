<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate incoming request parameters

        $request->validate([
            'search'       => 'nullable|string|max:255',
            'itemsPerPage' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Category::query();

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
        $categories   = $query->paginate($itemsPerPage);

        return response()->json([
            'categories' => $categories->items(),
            'total'      => $categories->total(), // Total count for pagination
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
        $validatedData = $request->validate(Category::validationRules());
        
        Category::create($validatedData);
        return response()->json(['success' => true,'message' => 'Category created successfully.'], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return response()->json([
            'success' => true,
            'category' => $category
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate(Category::validationRules());

        // Update the product model with the validated data
        $category->update($validatedData);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            // 'category' => $category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            // Delete the supplier
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Category: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}