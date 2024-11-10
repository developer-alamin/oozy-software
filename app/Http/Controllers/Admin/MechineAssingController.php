<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MechineAssing;
use App\Models\Brand;
use App\Models\Factory;
use App\Models\MechineStock;
use App\Models\MechineType;
use App\Models\ProductModel;
use App\Models\Rent;
use Carbon\Carbon;
use App\Models\Source;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // Check which authentication guard is in use and set the creator
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'company_id'              => 'required|integer',
            'factory_id'              => 'required|integer',
            'brand_id'                => 'required|integer',
            'model_id'                => 'required|integer',
            'mechine_type_id'         => 'required|integer',
            'mechine_source_id'       => 'required|integer',
            'supplier_id'             => 'nullable',
            'rent_id'                 => 'nullable|integer',
            'rent_date'               => 'nullable',
            'name'                    => 'required|string|max:255',
            'mechine_code'            => 'required|string|max:255',
            'serial_number'           => 'nullable|string|max:255',
            'preventive_service_days' => 'nullable',
            'purchace_price'          => 'nullable|numeric',
            'purchase_date'           => 'nullable',
            'status'                  => 'nullable',  // Example: assumes "status" has specific values
            'note'                    => 'nullable|string',
        ]);

        // Process dates to handle timezone issues and format them properly
        if (isset($validatedData['purchase_date'])) {
            $validatedData['purchase_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $validatedData['purchase_date'])
            )->format('Y-m-d');
        }

        if (isset($validatedData['rent_date'])) {
            $validatedData['rent_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $validatedData['rent_date'])
            )->format('Y-m-d');
        }

        // Create the new MechineAssing instance with validated data
        $mechineAssing       = new MechineAssing($validatedData);
        // Associate the creator and updater polymorphically
        $mechineAssing->uuid = HelperController::generateUuid();
        $mechineAssing->creator()->associate($creator);
        $mechineAssing->updater()->associate($creator);

        // Save the MechineAssing record
        $mechineAssing->save();

        // Save data to the Stock table
        $stock = new MechineStock([
            'mechine_assing_id' => $mechineAssing->id,
            'quantity'          => 1,
            'type'              => "mechine",
            'status'            => 'in_stock',  // Adjust status as needed
        ]);
        
        $stock->creator()->associate($creator);
        $stock->updater()->associate($creator);
        $stock->save();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'MechineAssing created successfully.',
            'mechine_assing' => $mechineAssing
        ], 200);
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