<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc');
        $search       = $request->input('search', '');
        $query = Company::query();
        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        $query->orderBy($sortBy, $sortOrder);
        $categorys = $query->paginate($itemsPerPage);
        return response()->json([
            'items' => $categorys->items(),
            'total' => $categorys->total(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function trash(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc');
        $search       = $request->input('search', '');
        $query = Company::onlyTrashed();
        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        $query->orderBy($sortBy, $sortOrder);
        $categorys = $query->paginate($itemsPerPage);
        return response()->json([
            'items' => $categorys->items(),
            'total' => $categorys->total(),
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
        $validatedData = $request->validate(Company::validationRules());
        $company       = new Company($validatedData);
        $company->uuid = HelperController::generateUuid();
        $company->save();
        return response()->json(['success' => true, 'message' => 'Company created successfully.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $company = Company::where('id', $id)->firstOrFail();
        return response()->json([
            'success' => true,
            'item'=> $company
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $company = Company::where('id', $id)->firstOrFail();
        $validatedData = $request->validate(Company::validationRules());
        $company->fill($validatedData);
        $company->save();

         return response()->json(['success' => true, 'message' => 'Company updated successfully.', 'item' => $company], 200);
    }

    public function trashed_count(Request $request){
        // Get the count of soft-deleted brands
        $trashedCount = Company::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }


    // Restore a soft-deleted operator
    public function restore($id)
    {
        $restored = Company::onlyTrashed()
            ->findOrFail($id)
            ->restore();
        return response()->json(['message' => 'Company restored successfully'], Response::HTTP_OK);
    }

    public function forceDelete($id)
    {
        $operator = Company::onlyTrashed()
            ->findOrFail($id);
        $operator->forceDelete();
        return response()->json(['message' => 'Company permanently deleted'], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        try {
            $company->delete();
            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting factory: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
