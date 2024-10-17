<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductModelController extends Controller
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

        $query = ProductModel::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('model_number', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Sorting
        if ($request->filled('sortBy')) {
            $query->orderBy($request->sortBy, $request->sortDesc ? 'desc' : 'asc');
        }

        // Pagination
        $itemsPerPage = $request->input('itemsPerPage', 15); // Default items per page
        $models        = $query->paginate($itemsPerPage);

        return response()->json([
            'models' => $models->items(),
            'total' => $models->total(), // Total count for pagination
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
        $validatedData = $request->validate(ProductModel::validationRules());
        
        ProductModel::create($validatedData);
        return response()->json(['success' => true], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductModel $productModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductModel $model)
    {
        // Returning the product model for editing (e.g., in a JSON response for a frontend app)
        return response()->json([
            'success' => true,
            'model' => $model
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    

    public function update(Request $request, ProductModel $model)
    {
    // Validate the request data
        $validatedData = $request->validate([
            'name'         => 'required|string|max:255',
            'model_number' => 'required|string|max:255|unique:product_models,model_number,' . $model->id, // Ignore current model's ID for uniqueness check
            'description'  => 'nullable|string',
            'status'       => 'nullable',
        ]);

        // Update the product model with the validated data
        $model->update($validatedData);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Product model updated successfully.',
            'model' => $model
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductModel $model)
    {
        try {
            // Delete the supplier
            $model->delete();
            return response()->json([
                'success' => true,
                'message' => 'Model deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting model: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
