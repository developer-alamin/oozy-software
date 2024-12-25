<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BreakDownProblemNote;
use App\Models\Factory;
use App\Models\Floor;
use App\Models\Group;
use App\Models\Line;
use App\Models\MachineStatus;
use App\Models\MechineAssing;
use App\Models\Parse;
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
    public function getCompanyWaysFactories(Request $request)
    {
      $search = $request->query('search', '');
      $limit = $request->query('limit', 5);
      $companyId = $request->query('company_id');

      // Query factories by company and name
      $factories = Factory::where('company_id', $companyId)
          ->where('name', 'like', '%' . $search . '%')
          ->limit($limit)
          ->get();

      return response()->json($factories);
    }

    public function getFactories(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $factories = Factory::with('user:id,name')->where('name', 'like', '%' . $search . '%')
                    //  ->limit($limit)
                     ->get();
        // Return the factories as JSON
        return response()->json($factories);
    }

    public function getFloors(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $factories = Floor::with(['factories.user:id,name'])->where('name', 'like', '%' . $search . '%')
                    //  ->limit($limit)
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
                    //  ->limit($limit)
                     ->get();
        // Return the factories as JSON
        return response()->json($factories);
    }
    public function getBrandAll(Request $request){

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



    // public function getModels(Request $request)
    // {
    //     // Get search term, limit, and brand_id from the request
    //     $search = $request->query('search', '');
    //     $limit  = $request->query('limit', 5); // Default limit of 5
    //     $brandId = $request->query('brand_id');

    //     if (!$brandId) {
    //         return response()->json([], 400); // Return empty if no brand_id is provided
    //     }

    //     // Query to search for models by brand_id and name with a limit
    //     $models  = ProductModel::where('brand_id', $brandId)
    //                  ->where('name', 'like', '%' . $search . '%')
    //                  ->limit($limit)
    //                  ->get();

    //     // Return the models as JSON
    //     return response()->json($models);
    // }

    // public function getModels(Request $request)
    // {
    //     $search = $request->query('search', '');
    //     $limit = $request->query('limit', 5);

    //     $models = ProductModel::where('name', 'like', '%' . $search . '%')
    //         ->limit($limit)
    //         ->get();
    //     return response()->json($models);
    // }
    public function getModels(Request $request)
    {
      $search   = $request->query('search', '');
      $limit    = $request->query('limit', 5); // Default limit of 5
      $brand_id = $request->query('brand_id');

      $query = ProductModel::query();

      if ($brand_id) {
          $query->where('brand_id', $brand_id); // Filter by brand_id
      }

      $models = $query->where('name', 'like', '%' . $search . '%')
                      ->limit($limit)
                      ->get();

      return response()->json($models);
    }

    /**
     * Fetch brands based on the selected model.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBrands(Request $request)
    {
      // Get search term and limit from the request, with defaults
      $search = $request->query('search', '');
      $limit = $request->query('limit', 5); // Default limit of 5

      // Query to search for brands by name with a limit
      $brands = Brand::where('name', 'like', '%' . $search . '%')
          ->limit($limit)
          ->get();

      // Return the brands as JSON
      return response()->json($brands);
  }
  public function getGroups(Request $request)
    {
      // Get search term and limit from the request, with defaults
      $search = $request->query('search', '');
      $limit = $request->query('limit', 5); // Default limit of 5

      // Query to search for brands by name with a limit
      $groups = Group::where('name', 'like', '%' . $search . '%')
          ->limit($limit)
          ->get();

      // Return the groups as JSON
      return response()->json($groups);
  }
  public function getParts(Request $request)
  {
      // Get search term and limit from the request, with defaults
      $search = $request->query('search', '');
      $limit = $request->query('limit', 5); // Default limit of 5

      // Query to search for brands by name with a limit
      $parts = Parse::where('name', 'like', '%' . $search . '%')
          ->limit($limit)
          ->get();

      // Return the parts as JSON
      return response()->json($parts);
  }

    // public function getBrands(Request $request)
    // {
    //     $modelId = $request->query('model_id');
    //     $search = $request->query('search', '');
    //     $limit = $request->query('limit', 5);

    //     if (!$modelId) {
    //         return response()->json([]);
    //     }

    //     $model = ProductModel::find($modelId);

    //     if (!$model) {
    //         return response()->json([]);
    //     }

    //     $query = Brand::where('id', $model->brand_id);

    //     if ($search) {
    //         $query->where('name', 'like', '%' . $search . '%');
    //     }

    //     $brands = $query->limit($limit)->get();

    //     return response()->json($brands);
    // }

    public function getMachineStatus(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 20); // Default limit of 10
        // Query to search for brands by name with a limit
        $machineStatus  = MachineStatus::where('name', 'like', '%' . $search . '%')
                    //  ->limit($limit)
                     ->get();
        // Return the machineStatus as JSON
        return response()->json($machineStatus);
    }
    public function getLinesByMachine(Request $request)
    {
        $machineId = $request->query('machine_id');
        if (!$machineId) {
            return response()->json(['error' => 'Machine ID is required'], 400);
        }

        // Fetch lines associated with the factory of the selected machine
        $lines = Line::whereHas('units.floors.factories', function ($query) use ($machineId) {
            $query->where('id', MechineAssing::find($machineId)->factory_id);
        })->get(['id', 'name']);

        return response()->json($lines);
    }
    public function getLinesByFactory(Request $request)
    {
        $factoryId = $request->input('factory_id');
        // dd( $factoryId);

        // Fetch lines through the relationship chain
        $lines = Line::whereHas('units.floors.factories', function ($query) use ($factoryId) {
            $query->where('id', $factoryId);
        })
        ->select('id', 'name') // Select only necessary columns
        ->get();

        return response()->json($lines);
    }

    /**
     * @OA\Get(
     *     path="/get_machine_codes",
     *     tags={"Machine Management"},
     *     summary="Retrieve machine codes based on search criteria",
     *     description="Fetch machine codes that match a search term and filter by location status 'Sewing Line'. Supports grouping by unique machine codes and limiting results.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Partial or full machine code to filter results. This field is optional and editable.",
     *         required=false,
     *         @OA\Schema(type="string", example="MC123")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Maximum number of results to return. Default is 5.",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", description="Unique ID of the record", example=1),
     *                 @OA\Property(property="machine_code", type="string", description="Unique code of the machine", example="MC123"),
     *                 @OA\Property(property="location_status", type="string", description="Current status of the machine's location", example="Sewing Line"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of the machine record creation", example="2024-01-01T12:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request due to invalid input.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid search parameter.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized access.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred.")
     *         )
     *     )
     * )
     */

    public function getMachineCodes(Request $request)
    {
        // Get search term and limit from the request, with defaults
        $search      = $request->query('search', '');
        $limit       = $request->query('limit', 5); // Default limit of 10
        // Query to search for machines by code with a limit
        $machineCodes = MechineAssing::selectRaw('
                            MAX(id) as id,
                            machine_code,
                            line_id,
                            MAX(location_status) as location_status,
                            MAX(created_at) as created_at
                        ')
                        ->where('machine_code', 'like', '%' . $search . '%')
                        ->where('location_status', 'Sewing Line')
                        ->groupBy('machine_code','line_id') // Group by machine_code to ensure uniqueness
                        ->limit($limit)
                        ->get();
        // Return the machineCodes as JSON
        return response()->json($machineCodes);
    }

    /**
     * @OA\Get(
     *     path="/get-machine-code-ways/details/{machine_code}",
     *     tags={"MachineCodes"},
     *     summary="Retrieve machine details by machine code",
     *     description="Fetch machine data based on the provided machine code.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="machine_code",
     *         in="path",
     *         description="Machine code to fetch details",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="OZ-00000006"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Machine data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="machine_code", type="string", example="OZ-00000006"),
     *             @OA\Property(property="unit_id", type="integer", example=1),
     *              @OA\Property(property="floor_id", type="integer", example=1),
     *             @OA\Property(property="line_id", type="integer", example=1),
     *             @OA\Property(property="location", type="string", example="Sewing Line"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Machine not found or not assigned to the sewing line"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing API token"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function getManuallyApiMachineDetails(Request $request)
    {
        // Fetch the machine assignment using the machine code
        $machine = MechineAssing::where('machine_code', $request->machine_code)
            ->where('location_status', 'Sewing Line') // Ensure the machine is assigned to the correct location
            ->with(['line.unit', 'line.unit.floor']) // Eager load related models
            ->first([
                'id',
                'machine_code',
                'line_id',
                'location_status',
                'created_at'
            ]);

        // If the machine is not found or not assigned to the sewing line, return 404
        if (!$machine) {
            return response()->json(['message' => 'Machine not found or not assigned to the sewing line.'], 404);
        }

        // Get additional data from relationships
        $unit_id = $machine->line ? $machine->line->unit_id : null;
        $unit_name = $machine->line && $machine->line->unit ? $machine->line->unit->name : null;

        $floor_id = $machine->line && $machine->line->unit && $machine->line->unit->floor ? $machine->line->unit->floor_id : null;
        $floor_name = $machine->line && $machine->line->unit && $machine->line->unit->floor ? $machine->line->unit->floor->name : null;

        $line_name = $machine->line ? $machine->line->name : null;

        // Add all the required data to the response
        $machineData = [
            'id' => $machine->id,
            'machine_code' => $machine->machine_code,
            'line_id' => $machine->line_id,
            'line_name' => $line_name,
            'location_status' => $machine->location_status,
            'created_at' => $machine->created_at->toIso8601String(),
            'unit_id' => $unit_id,
            'unit_name' => $unit_name,
            'floor_id' => $floor_id,
            'floor_name' => $floor_name,
        ];

        // Return the machine details with the additional names
        return response()->json($machineData);
    }


     /**
     * @OA\Get(
     *     path="/machine/details/{machine_code}",
     *     tags={"MachineCodes"},
     *     summary="Retrieve machine details by machine code",
     *     description="Fetch machine data based on the provided machine code.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="machine_code",
     *         in="path",
     *         description="Machine code to fetch details",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="MC123"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Machine data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="machine_code", type="string", example="MC123"),
     *             @OA\Property(property="location_status", type="string", example="Sewing Line"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Machine not found or not assigned to the sewing line"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing API token"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */


    public function getApiMachineDetails($machine_code)
    {
        // Fetch the machine assignment using the machine code
        $machine = MechineAssing::where('machine_code', $machine_code)
            ->where('location_status', 'Sewing Line')
            ->first();

        // If the machine is not found or not assigned to the sewing line, return 404
        if (!$machine) {
            return response()->json(['message' => 'Machine not found or not assigned to the sewing line.'], 404);
        }

        // Return machine details
        return response()->json($machine);
    }
    public function getMachineLines(Request $request)
    {
        $machineId = $request->get('machine_id');
        // Validate machine_id
        if (!$machineId) {
            return response()->json(['error' => 'Machine ID is required.'], 400);
        }
        // Fetch assigned line IDs from MachineAssign
        $assignedLineIds = MechineAssing::where('id', $machineId)->pluck('line_id');
        // Fetch the lines corresponding to these IDs
        $lines = Line::whereIn('id', $assignedLineIds)->get();

        return response()->json($lines);
    }

    public function getBreakdownProblemNotes(Request $request)
    {
        $search = $request->query('search', '');
        $limit = $request->query('limit', 5); // Default limit of 5

        // Query to search for brands by name with a limit
        $breakDownProblemNotes = BreakDownProblemNote::where('break_down_problem_note', 'like', '%' . $search . '%')
            ->limit($limit)
            ->get();

        // Return the BreakDownProblemNotes as JSON
        return response()->json($breakDownProblemNotes);
    }

}
