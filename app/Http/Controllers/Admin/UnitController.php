<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UnitController extends Controller
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

        $query = Unit::query();

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
        $units        = $query->paginate($itemsPerPage);

        return response()->json([
            'units' => $units->items(),
            'total' => $units->total(), // Total count for pagination
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
        $validatedData = $request->validate(Unit::validationRules());
        
        Unit::create($validatedData);
        return response()->json(['success' => true,'message' => 'Unit created successfully.'], 201);
    }
 

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return response()->json([
            'success' => true,
            'unit' => $unit
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validatedData = $request->validate(Unit::validationRules());

        // Update the product model with the validated data
        $unit->update($validatedData);

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Unit updated successfully.',
         
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        try {
            // Delete the supplier
            $unit->delete();
            return response()->json([
                'success' => true,
                'message' => 'Unit deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Unit: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
