<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Factory;
use App\Models\Floor;
use App\Models\MachineStatus;
use App\Models\ProductModel;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class DynamicDataController extends Controller
{
    public function getCompanies(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for users by name with a limit
        $users = User::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the users as JSON
        return response()->json($users);
    }
    public function getFactories(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $factories = Factory::with('user:id,name')->where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the factories as JSON
        return response()->json($factories);
    }

    public function getFloors(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $factories = Floor::with(['factories.user'])->where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the factories as JSON
        return response()->json($factories);
    }
    public function getUnits(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $factories = Unit::with(['floors.factories.user'])->where('name', 'like', '%' . $search . '%')
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

    // public function getModels(Request $request){

    //     // Get search term and limit from the request, with defaults
    //     $search = $request->query('search', '');
    //     $limit  = $request->query('limit', 5); // Default limit of 10
    //     // Query to search for models by name with a limit
    //     $models  = ProductModel::where('name', 'like', '%' . $search . '%')
    //                  ->limit($limit)
    //                  ->get();
    //     // Return the models as JSON
    //     return response()->json($models);
    // }

    public function getModels(Request $request)
    {
        // Get search term, limit, and brand_id from the request
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 5
        $brandId = $request->query('brand_id');

        if (!$brandId) {
            return response()->json([], 400); // Return empty if no brand_id is provided
        }

        // Query to search for models by brand_id and name with a limit
        $models  = ProductModel::where('brand_id', $brandId)
                     ->where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();

        // Return the models as JSON
        return response()->json($models);
    }
    public function getMachineStatus(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for brands by name with a limit
        $machineStatus  = MachineStatus::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the machineStatus as JSON
        return response()->json($machineStatus);
    }


}