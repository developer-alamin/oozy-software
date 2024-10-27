<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Line;
use Illuminate\Http\Response;

class LineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by name
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is ascending
        $search       = $request->input('search', ''); // Search term, default is empty

       // Query brands with pagination, sorting, and search
        $linesQuery = Line::query();
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $linesQuery->where('name', 'LIKE', '%' . $search . '%');
        }
    
        // Apply sorting
        $linesQuery->orderBy($sortBy, $sortOrder);
    
        // Paginate results
        $lines = $linesQuery->paginate($itemsPerPage);
    
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

        Line::create($validatedData);

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
         return response()->json([
            'success' => true,
            'line' => $line
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Line $line)
    {
       
         $validatedData = $request->validate(Line::validationRules());

         // Update the product model with the validated data
         $line->update($validatedData);

        // Return a success response
         return response()->json([
           'success' => true,
            'message' => 'Line updated successfully.',
         ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Line $line)
    {
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
                'message' => 'Error deleting Brand: ' . $e->getMessage()
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
        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by name
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is descending
        $search       = $request->input('search', ''); // Search term, default is empty

        // Query only soft deleted brands with pagination, sorting, and search
        $linesQuery = Line::onlyTrashed(); // Fetch only soft-deleted records

        // Apply search if the search term is not empty
        if (!empty($search)) {
            $linesQuery->where('name', 'LIKE', '%' . $search . '%');
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
         $line = Line::onlyTrashed()->findOrFail($id);
         $line->forceDelete(); // Permanent delete
 
         return response()->json(['message' => 'Line permanently deleted']);
     }
}
