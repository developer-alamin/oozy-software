<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MechineAssing;
use App\Models\Brand;
use App\Models\Factory;
use App\Models\MechineType;
use App\Models\ProductModel;
use App\Models\Rent;
use App\Models\Source;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MechineAssingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MechineAssing $mechineAssing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MechineAssing $mechineAssing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MechineAssing $mechineAssing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MechineAssing $mechineAssing)
    {
        //
    }


    public function getFactories(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $factories = Factory::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the factories as JSON
        return response()->json($factories);
    }
    public function getBrands(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for brands by name with a limit
        $brands  = Brand::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the brands as JSON
        return response()->json($brands);
    }
    public function getModels(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for models by name with a limit
        $models  = ProductModel::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the models as JSON
        return response()->json($models);
    }
    public function getTypes(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for types by name with a limit
        $types  = MechineType::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the types as JSON
        return response()->json($types);
    }
    public function getSources(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for source by name with a limit
        $source  = Source::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the source as JSON
        return response()->json($source);
    }
    public function getSuppliers(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for suppliers by name with a limit
        $suppliers  = ProductModel::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the suppliers as JSON
        return response()->json($suppliers);
    }
    public function getRents(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for rents by name with a limit
        $rents  = Rent::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the rents as JSON
        return response()->json($rents);
    }
}
