<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\BreakDownProblemNote;
use App\Models\Cause;
use App\Models\Company;
use App\Models\Factory;
use App\Models\FishboneCategory;
use App\Models\Floor;
use App\Models\Group;
use App\Models\Line;
use App\Models\MachineStatus;
use App\Models\MechineAssing;
use App\Models\Parse;
use App\Models\ProblemNote;
use App\Models\ProductModel;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class DynamicDataController extends Controller
{
    public function getCompanies(Request $request)
    {
        // Get the authenticated user
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Get the creator type (admin or user)
        $creatorType = $this->getCreatorType();
    
        // Get the search term and limit from the request, with defaults
        $search = $request->query('search', ''); // Default search is an empty string
        $limit = (int) $request->query('limit', 5); // Default limit is 5
    
        // Validate the limit to ensure it's a positive integer
        if ($limit <= 0) {
            $limit = 5; // Fallback to default if invalid
        }
    
        // Build the query for the Company model
        $companyQuery = Company::query()
            ->where('creator_id', $currentUser->id)
            ->where('creator_type', $creatorType);
    
        // Apply search filter if provided
        if ($search) {
            $companyQuery->where('name', 'like', '%' . $search . '%');
        }
    
        // Retrieve the companies with the limit applied
        $companies = $companyQuery->limit($limit)->get();
    
        // Return the results as a JSON response
        return response()->json($companies, Response::HTTP_OK);
    }
    
    public function getCompanyWaysFactories(Request $request)
    {

         // Get the authenticated user
         $currentUser = $this->getAuthenticatedUser();
         if (!$currentUser) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
     
         // Get the creator type (admin or user)
         $creatorType = $this->getCreatorType();



      $search = $request->query('search', '');
      $limit = $request->query('limit', 5);
      $companyId = $request->query('company_id');



      $factoryQuery = Factory::query();
      $factoryQuery->where('creator_id', $currentUser->id)
      ->where('creator_type', $creatorType);

        if ($companyId) {
            $factoryQuery->where('company_id', $companyId);
        }

        if ($search) { 
            $factoryQuery->where('name', 'LIKE', '%'. $search . '%');
        }
      // Query factories by company and name
      $factories = $factoryQuery->limit($limit)
          ->get();

      return response()->json($factories);
    }

    public function getFactories(Request $request){

         // Get the authenticated user
         $currentUser = $this->getAuthenticatedUser();
         if (!$currentUser) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
     
         // Get the creator type (admin or user)
         $creatorType = $this->getCreatorType();


        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
       
       $factoryQuery = Factory::query();
       $factoryQuery->where('creator_id', $currentUser->id)
       ->where('creator_type', $creatorType);
       
       if ($search) {
        $factoryQuery->where('name', 'LIKE', '%'. $search . '%');

       }
       
        $factories =  $factoryQuery->with('company:id,name')
                      ->limit($limit)
                       ->get();



        // Return the factories as JSON
        return response()->json($factories);
    }

    public function getFloors(Request $request){

         // Get the authenticated user
         $currentUser = $this->getAuthenticatedUser();
         if (!$currentUser) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
     
         // Get the creator type (admin or user)
         $creatorType = $this->getCreatorType();



        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        
        
        $floorQyery = Floor::query();
        $floorQyery->where('creator_id', $currentUser->id)
         ->where('creator_type', $creatorType);
        

         if ($search) {
            $floorQyery->where('name','LIKE', '%'. $search . '%');
         }
        
        // Query to search for factories by name with a limit
        $floors =  $floorQyery->with(['factories.company:id,name'])
                    ->limit($limit)
                    ->get();


        // Return the factories as JSON
        return response()->json($floors);
    }
    public function getUnits(Request $request){
        $currentUser = $this->getAuthenticatedUser();

        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $creatorType = $this->getCreatorType();

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit = $request->query('limit', 5); // Default limit of 5

        // Build the query
        $unitQuery = Unit::query();
        $unitQuery->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType);

        if ($search) {
            $unitQuery->where('name', 'like', '%' . $search . '%');
        }

        // Retrieve the units with the selected columns
        $units = $unitQuery->select('id', 'name')
                        ->limit($limit)
                        ->get();

        // Return the units as JSON
        return response()->json($units);

    }
    public function getBrandAll(Request $request){


         // Get the authenticated user
         $currentUser = $this->getAuthenticatedUser();
         if (!$currentUser) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
     
         // Get the creator type (admin or user)
         $creatorType = $this->getCreatorType();


        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
       
        $brandQuery = Brand::query();
        $brandQuery->where('creator_id', $currentUser->id)
        ->where('creator_type', $creatorType);


        if ($search) {
            $brandQuery->where('name', 'like', '%' . $search . '%');
        }
       
        // Query to search for brands by name with a limit
        $brands  = $brandQuery->with(['company:id,name'])
                ->limit($limit)
                ->get();


        // Return the brands as JSON
        return response()->json($brands);
    }
    public function getModels(Request $request)
    {

      // Get the authenticated user
      $currentUser = $this->getAuthenticatedUser();
      if (!$currentUser) {
          return response()->json(['error' => 'Unauthorized'], 401);
      }
  
      // Get the creator type (admin or user)
      $creatorType = $this->getCreatorType();


      $search   = $request->query('search', '');
      $limit    = $request->query('limit', 5); // Default limit of 5
      $brand_id = $request->query('brand_id');

      $query = ProductModel::query();

      $query->where('creator_id', $currentUser->id)
        ->where('creator_type', $creatorType);


      if ($brand_id) {
          $query->where('brand_id', $brand_id); // Filter by brand_id
      }

      $models = $query->where('name', 'like', '%' . $search . '%')
                      ->limit($limit)
                      ->get();

      return response()->json($models);
    }

    public function getBrands(Request $request)
    {

         // Get the authenticated user
      $currentUser = $this->getAuthenticatedUser();
      if (!$currentUser) {
          return response()->json(['error' => 'Unauthorized'], 401);
      }
  
      // Get the creator type (admin or user)
      $creatorType = $this->getCreatorType();


      // Get search term and limit from the request, with defaults
      $search = $request->query('search', '');
      $limit = $request->query('limit', 5); // Default limit of 5

      $brandsQuery = Brand::query();
      $brandsQuery->where('creator_id', $currentUser->id)
        ->where('creator_type', $creatorType);

      // Query to search for brands by name with a limit
      $brands = $brandsQuery->where('name', 'like', '%' . $search . '%')
          ->limit($limit)
          ->get();

      // Return the brands as JSON
      return response()->json($brands);
    }
    public function getGroups(Request $request)
        {

        // Get the authenticated user
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Get the creator type (admin or user)
        $creatorType = $this->getCreatorType();


        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit = $request->query('limit', 5); // Default limit of 5


        $groupsQuery = Group::query();

        $groupsQuery->where('creator_id', $currentUser->id)
            ->where('creator_type', $creatorType);

        // Query to search for brands by name with a limit
        $groups = $groupsQuery->where('name', 'like', '%' . $search . '%')
            ->limit($limit)
            ->get();

        // Return the groups as JSON
        return response()->json($groups);
    }
    public function getParts(Request $request)
    {
        // Get the authenticated user
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the creator type (admin or user)
        $creatorType = $this->getCreatorType();

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit = $request->query('limit', 5); // Default limit of 5


        $ParseQuery = Parse::query();
        $ParseQuery->where('creator_id', $currentUser->id)
            ->where('creator_type', $creatorType);

        // Query to search for brands by name with a limit
        $parts = $ParseQuery->where('name', 'like', '%' . $search . '%')
            ->limit($limit)
            ->get();

        // Return the parts as JSON
        return response()->json($parts);
    }

    public function getMachineStatus(Request $request){


          // Get the authenticated user
          $currentUser = $this->getAuthenticatedUser();
          if (!$currentUser) {
              return response()->json(['error' => 'Unauthorized'], 401);
          }
  
          // Get the creator type (admin or user)
          $creatorType = $this->getCreatorType();


        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 20); // Default limit of 10
       
       $Query = MachineStatus::query();
       $Query->where('creator_id', $currentUser->id)
       ->where('creator_type', $creatorType);
       
        // Query to search for brands by name with a limit
        $machineStatus  = $Query->where('name', 'like', '%' . $search . '%')
                      ->limit($limit)
                     ->get();
        // Return the machineStatus as JSON
        return response()->json($machineStatus);
    }
    public function getLinesByMachine(Request $request)
    {

        // Get the authenticated user
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the creator type (admin or user)
        $creatorType = $this->getCreatorType();



        $machineId = $request->query('machine_id');
        if (!$machineId) {
            return response()->json(['error' => 'Machine ID is required'], 400);
        }



        $query = Line::query();
        $query->where('creator_id', $currentUser->id)
            ->where('creator_type', $creatorType);

        // Fetch lines associated with the factory of the selected machine
        $lines = $query->whereHas('units.floors.factories', function ($query) use ($machineId) {
            $query->where('id', MechineAssing::find($machineId)->factory_id);
        })->get(['id', 'name']);

        return response()->json($lines);
    }
    public function getLinesByFactory(Request $request)
    {

        // Get the authenticated user
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the creator type (admin or user)
        $creatorType = $this->getCreatorType();


        $factoryId = $request->input('factory_id');
 

        $query = Line::query();
        $query->where('creator_id', $currentUser->id)
        ->where('creator_type', $creatorType);


        // Fetch lines through the relationship chain
        $lines = $query->whereHas('unit.floor.factories', function ($query) use ($factoryId) {
            $query->where('id', $factoryId);
        })
        ->select('id', 'name') // Select only necessary columns
        ->get();

        return response()->json($lines);
    }

    public function getMachineCodes(Request $request)
    {


          // Get the authenticated user
          $currentUser = $this->getAuthenticatedUser();
          if (!$currentUser) {
              return response()->json(['error' => 'Unauthorized'], 401);
          }
  
          // Get the creator type (admin or user)
          $creatorType = $this->getCreatorType();


        // Get search term and limit from the request, with defaults
        $search      = $request->query('search', '');
        $limit       = $request->query('limit', 5); // Default limit of 10
       
          $query = MechineAssing::query();
          $query->where('creator_id', $currentUser->id)
          ->where('creator_type', $creatorType);
       
    
       
        // Query to search for machines by code with a limit
        $machineCodes = $query->selectRaw('
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

    public function searchMachine(Request $request, $id = 0)
    {


          // Get the authenticated user
          $currentUser = $this->getAuthenticatedUser();
          if (!$currentUser) {
              return response()->json(['error' => 'Unauthorized'], 401);
          }
  
          // Get the creator type (admin or user)
          $creatorType = $this->getCreatorType();




        $search      = $request->query('search', '');
        $limit       = $request->query('limit', 1);

        $query = MechineAssing::query();
        $query->where('creator_id', $currentUser->id)
        ->where('creator_type', $creatorType);


        //when edit
        if($id){

            $machine = $query->where('id', $id)
                ->limit($limit)
                ->get();
            }else{
                $machine = $query->where('machine_code', 'like', '%' . $search . '%')
                ->limit($limit)
                ->get();
            }
        
        return response()->json($machine);
    }

    public function searchUser(Request $request, $id = 0)
    {


         // Get the authenticated user
         $currentUser = $this->getAuthenticatedUser();
         if (!$currentUser) {
             return response()->json(['error' => 'Unauthorized'], 401);
         }
 
         // Get the creator type (admin or user)
         $creatorType = $this->getCreatorType();

        $search      = $request->query('search', '');
        $limit       = $request->query('limit', 1);

         $query = User::query();
         $query->where('creator_id', $currentUser->id)
            ->where('creator_type', $creatorType);



        //when edit
        if($id){
            $user = $query->where('id', $id)
                ->limit($limit)
                ->get();
            }else{
                $user = $query->where('name', 'like', '%' . $search . '%')
                ->limit($limit)
                ->get();
            }
        
        return response()->json($user);
    }

    public function getManuallyApiMachineDetails(Request $request)
    {

        // Get the authenticated user
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the creator type (admin or user)
        $creatorType = $this->getCreatorType();

        $query = MechineAssing::query();
        $query->where('creator_id', $currentUser->id)
        ->where('creator_type', $creatorType);



        // Fetch the machine assignment using the machine code
        $machine = $query->where('machine_code', $request->machine_code)
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
        $machineId = $request->get('mechine_assing_id');
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

    public function getBreakdownProblemNotes(Request $request, $ids="")
    {
        $search = $request->query('search', '');
        $limit = $request->query('limit', 5); // Default limit of 5

        // Query to search for brands by name with a limit
        $breakDownProblemNotes = BreakDownProblemNote::where('note', 'like', '%' . $search . '%');
        if($ids){
            $breakDownProblemNotes = BreakDownProblemNote::whereIn('id', explode(',', $ids))
            ->limit($limit)
            ->get();

        }else{

            $breakDownProblemNotes = BreakDownProblemNote::where('note', 'like', '%' . $search . '%')
            ->limit($limit)
            ->get();
        }

        // Return the BreakDownProblemNotes as JSON
        return response()->json($breakDownProblemNotes);
    }
    public function get_actions(Request $request, $ids = "")
    {

            // Get the authenticated user
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Get the creator type (admin or user)
        $creatorType = $this->getCreatorType();

        $search = $request->query('search', ''); // Search parameter
        $limit = $request->query('limit', 5);    // Default limit of 5




        // Base query for the Action model
        $query = Action::query();


        $query->where('creator_id', $currentUser->id)
            ->where('creator_type', $creatorType);
        // Apply search filter if provided
        if ($search) {
            $actions = $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is a column in the Action model
        }
        // If IDs are provided, filter by IDs
        if ($ids) {
            $actions = $query->whereIn('id', explode(',', $ids));
        }

       
        // Limit the results and get the data
        $actions = $actions->limit($limit)->get();

        // Return the data as JSON
        return response()->json($actions);
    }

    public function get_problemNotes(Request $request, $ids = "")
    {
        // Get the authenticated user
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the creator type (admin or user)
        $creatorType = $this->getCreatorType();

        $search = $request->query('search', ''); // Search parameter
        $limit = $request->query('limit', 5);    // Default limit of 5

        // Base query for the Action model
        $query = ProblemNote::query();

        $query->where('creator_id', $currentUser->id)
        ->where('creator_type', $creatorType);


        // Apply search filter if provided
        if ($search) {
            $problemNotes = $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is a column in the Action model
        }
        // If IDs are provided, filter by IDs
        if ($ids) {
            $problemNotes = $query->whereIn('id', explode(',', $ids));
        }
        // Limit the results and get the data
        $problemNotes = $problemNotes->with(["company:id,name"])->limit($limit)->get();

        // Return the data as JSON
        return response()->json($problemNotes);
    }
    public function get_causes(Request $request, $ids = "")
    {
        // Get the authenticated user
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get the creator type (admin or user)
        $creatorType = $this->getCreatorType();


        $search = $request->query('search', ''); // Search parameter
        $limit = $request->query('limit', 5);    // Default limit of 5
        $ids = $request->query('ids', '');       // Assuming 'ids' parameter might be passed

        // Base query for the Cause model
        $query = Cause::query();
        $query->where('creator_id', $currentUser->id)
        ->where('creator_type', $creatorType);

        // Apply search filter if provided
        if ($search) {
            $causes = $query->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is a column in the Cause model
        }

        // If IDs are provided, filter by IDs
        if ($ids) {
            $causes = $query->whereIn('id', explode(',', $ids));
        }

        // Limit the results and get the data
        $causes = $causes->with(["company:id,name", "problemNote.company:id,name"]) // Assuming relationships for "company" and "problemNote"
                        ->limit($limit)
                        ->get();

        // Return the data as JSON
        return response()->json($causes);

    }

    public function fishboneDigrame(Request $request,   $ids = "")
    {
        
        $currentUser = $this->getAuthenticatedUser();

        if (!$currentUser) {
          return response()->json(["message"=> "Unauthenticated"],0);
        }
        $creatorType = $this->getCreatorType(); 


        $search = $request->query('search', ''); // Search parameter
        $limit = $request->query('limit', 5);    // Default limit of 5
        $ids = $request->query('ids', '');  

       

        $problemNotes = ProblemNote::query();

        if ($search) {
            $problemNotes = $problemNotes->where('name', 'like', '%' . $search . '%'); // Assuming 'name' is a column in the ProblemNote model
            
        }

        $problemNotes = $problemNotes->where("creator_id", $currentUser->id)
        ->where("creator_type", $creatorType)
        ->with(["fishbone_categories.causes"])
        ->limit($limit)
        ->get();

        return response()->json($problemNotes,Response::HTTP_OK);
    }

    public function getFishboneCategory(Request $request, $ids = '')
    {
        $currentUser = $this->getAuthenticatedUser();
        
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $creatorType = $this->getCreatorType();
    
        // Get query parameters
        $search = $request->query('search', ''); // Search parameter
        $limit = $request->query('limit', 5);    // Default limit of 5
        $ids = $request->query('ids', $ids);     // Use either the passed 'ids' or query parameter
    
        // Begin query
        $categoryQuery = FishboneCategory::query();
        $categoryQuery->where("creator_id", $currentUser->id)
                      ->where("creator_type", $creatorType);
    
        // Apply search filter if provided
        if ($search) {
            $categoryQuery = $categoryQuery->where("name", "like", "%" . $search . "%");
        }
    
        // Apply 'ids' filter if provided (ensure it's not empty)
        if (!empty($ids)) {
            $idsArray = explode(',', $ids); // Assuming 'ids' is a comma-separated list of IDs
            $categoryQuery = $categoryQuery->whereIn('id', $idsArray);
        }
    
        // Fetch categories with relationships and apply limit
        $categories = $categoryQuery->with(['problemNote.company'])->limit($limit)->get();
    
        // Return the response
        return response()->json([
            'items' => $categories
        ], Response::HTTP_OK);
    }

    public function problemByFishboneCategory($id){
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $creatorType = $this->getCreatorType();

        if (!$id) {
            return response()->json(['error' => 'Problem Id not found'], 404);  
        }
        $problem  = ProblemNote::where('id', $id)
        ->where("creator_id", $currentUser->id)
        ->where("creator_type", $creatorType)
        ->with(["fishbone_categories.causes"])
        ->first();
        return response()->json($problem,Response::HTTP_OK);

    }


    public function get_lines(Request $request)  {
        
        // Get the authenticated user
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // Get the creator type (admin or user)
        $creatorType = $this->getCreatorType();
    
        $search = $request->query('search', ''); 
        $limit = (int) $request->query( 'limit', 5); 


        if ($limit <= 0) {
            $limit = 5; 
        }

        $query = Line::query();
        $query->where('creator_id', $currentUser->id)
        ->where('creator_type', $creatorType);

        if ($search) {
            $query->where('name','like','%'. $search .'%');
        }

        $lines =  $query->with('company')
                    ->limit($limit)
                    ->get();

        return response()->json($lines,Response::HTTP_OK);
    }
    
    // Helper method to get authenticated user
    private function getAuthenticatedUser()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            return Auth::guard('user')->user();
        }
        return null;
    }
    
    // Helper method to get creator type
    private function getCreatorType()
    {
        return Auth::guard('admin')->check() ? Admin::class : (Auth::guard('user')->check() ? User::class : null);
    }
    
}
