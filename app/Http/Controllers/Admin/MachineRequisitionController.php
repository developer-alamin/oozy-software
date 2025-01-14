<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Factory;
use App\Models\Line;
use App\Models\MechineType;
use App\Models\Rent;
use App\Models\Requisition;
use App\Models\RequisitionDetails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PHPUnit\TextUI\Help;
use Illuminate\Support\Facades\DB;
class MachineRequisitionController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = $this->getAuthentiCreator();
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        $className = get_class($currentUser);
        
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc');
        $factorySearchTerm = $request->input('factory', '');
        
        // Fetch factories based on creator type and ID
        $factoryQuery = Factory::query()
            ->where('creator_type', $className)
            ->where('creator_id', $currentUser->id);
        
        // Apply search term if provided
        if ($factorySearchTerm) {
            $factoryQuery->where('id', $factorySearchTerm);
        }
        
        // Get factories with relationships
        $factories = $factoryQuery->with(['floors.units.lines'])->get();
        
        // Check if factories exist
        if ($factories->isEmpty()) {
            return response()->json(['message' => 'No factories found'], 404);
        }
        
        // Extract line IDs
        $lineIds = $factories->pluck('floors')
            ->flatten()
            ->pluck('units')
            ->flatten()
            ->pluck('lines')
            ->flatten()
            ->pluck('id')
            ->toArray();

        $lines = Line::whereIn('lines.id', $lineIds)
        ->with(['unit', 'requisitions' => function($query) {
            $query->selectRaw('line_id, SUM(total) as total')
                ->groupBy('line_id');
        }])
        ->orderBy($sortBy, $sortOrder)
        ->paginate($itemsPerPage);

        // Access the 'requisition_total' after pagination
        $lines->getCollection()->transform(function ($line) {
            // If there is a requisition, set the total sum from the aggregated data
            $line->total = $line->requisitions->isEmpty() ? 0 : $line->requisitions->first()->total;
            return $line;
        });
        
        // Return paginated lines
        return response()->json([
            'items' => $lines->items(),
            'total' => $lines->total(),
        ]);        

    }

    public function machineCalender(Request $request)  {
        
        $currentUser = $this->getAuthentiCreator();
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        $className = get_class($currentUser);
        
        $page           = $request->input('page', 1);
        $itemsPerPage   = $request->input('itemsPerPage', 5);
        $sortBy         = $request->input('sortBy', 'created_at'); 
        $sortOrder      = $request->input('sortOrder', 'desc');
        $lineId = $request->input('line', '');
        
        if ($lineId) {
            $machineTypesWithSum = Requisition::where('line_id', $lineId) 
                ->with(['requisitionDetails.machineType'])  
                ->get()
                ->flatMap(function ($requisition) {

                    return $requisition->requisitionDetails->map(function ($detail) {
                        return [
                            'machine_type_id' => $detail->machine_type_id, 
                            'mc' => $detail->mc, 
                            'machineType' => $detail->machineType->name, 
                        ];
                    });
                })
                ->groupBy('machine_type_id')
                ->map(function ($items, $machineTypeId) {
                    
                    $mcSum = $items->sum('mc');  
                    $name = $items->first()['machineType'];  
            
                    return [
                        'machine_type_id' => $machineTypeId,
                        'mc_sum' => $mcSum,  
                        'name' => $name,  
                    ];
                })
                ->values();
        } else {
            $machineTypesWithSum = Requisition::with(['requisitionDetails.machineType']) // requisitionDetails এবং machineType সম্পর্ক লোড করা
                ->get()
                ->flatMap(function ($requisition) {
                    return $requisition->requisitionDetails->map(function ($detail) {
                        return [
                            'machine_type_id' => $detail->machine_type_id,  
                            'mc' => $detail->mc, 
                            'machineType' => $detail->machineType->name, 
                        ];
                    });
                })
                ->groupBy('machine_type_id')
                ->map(function ($items, $machineTypeId) {
                   
                    $mcSum = $items->sum('mc'); 
                    $name = $items->first()['machineType'];  
            
                    return [
                        'machine_type_id' => $machineTypeId,
                        'mc_sum' => $mcSum,  
                        'name' => $name,  
                    ];
                })
                ->values();
        }
        
        // Manually Paginate the results
        $machineTypesWithSumCollection = collect($machineTypesWithSum); // Convert to Collection
        $totalMcSum = $machineTypesWithSum->sum('mc_sum');
        $paginatedItems = $machineTypesWithSumCollection->forPage($page, $itemsPerPage); // Manually paginate
        
        // Return paginated response
        return response()->json([
            "total_sum" => $totalMcSum,
            'items' => $paginatedItems,
            'total' => $machineTypesWithSumCollection->count(), 
        ]);
    }


    public function MachineTypes(Request $request)
    {   
        $currentUser = $this->getAuthentiCreator();
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $className = get_class($currentUser);
        $line_id = $request->input("line",'');

       // $line = Line::with(["requisitions.requisition_details"])->findOrFail($line_id);


       // return response()->json($line,200);

        $machineTypes = MechineType::query();
        $machineTypes = $machineTypes->where('creator_type', $className)
        ->where('creator_id', $currentUser->id)
        ->select('id', 'name')
        ->get()
        ->toArray();
        
        return response()->json($machineTypes, 200);
    }


    public function Lines(Request $request)  {
        
        $search = $request->query('search', ''); 
        $limit = (int) $request->query( 'limit', 5); 

        if ($limit <= 0) {
            $limit = 5; 
        }

        $lines = Line::where('name', 'like', '%' . $search . '%')
                    ->limit($limit)
                    ->with('company')
                    ->get();

        return response()->json($lines,Response::HTTP_OK);
    }


    public function StoreRequisition(Request $request) {
        $currentUser = $this->getAuthentiCreator();
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $className = get_class($currentUser);


        $validate = Validator::make($request->all(), [
            'line' => 'required|exists:lines,id',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validate->errors(),
            ], 422);
        }


        $dateRange = $request->dateRange;
        if (!empty($dateRange) && is_array($dateRange)) {
            $startDate = Carbon::parse($dateRange[0])->startOfDay()->format('Y-m-d');
        } else {
            $startDate = Carbon::now()->startOfDay()->format('Y-m-d');
        }
        
        // Optional: Handle end date if it's a range
        if (!empty($dates) && is_array($dateRange) && isset($dateRange[1])) {
            $endDate = Carbon::parse($dateRange[1])->endOfDay()->format('Y-m-d');
        } else {
            $endDate = Carbon::now()->endOfDay()->format('Y-m-d');
        }

        $line = Line::find($request->line);

        $requisition = Requisition::create([
            'uuid' => HelperController::generateUuid(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'style' => $request->style,
            'line_id' => $request->line,
            'company_id' => $line->company_id,
            'total' => $request->total,
            'creator_type' => $className,
            'creator_id' => $currentUser->id,
            'updater_type' => $className,
            'updater_id' => $currentUser->id,
        ]);


        $types = json_decode($request->types, true);
        foreach ($types as $type) {
            $requisitionDetails = RequisitionDetails::create([
                'uuid' => HelperController::generateUuid(),
                'requisition_id' => $requisition->id,
                'machine_type_id' => $type['id'],
                'mc' => $type['mc'],
                'creator_type' => $className,
                'creator_id' => $currentUser->id,
                'updater_type' => $className,
                'updater_id' => $currentUser->id,
            ]);
        }


        return response()->json([
            'success' => true,
            'message' => 'Requisition created successfully',
            'requisition' => $requisition,
        ], Response::HTTP_CREATED);

       
       
    }

  
    private function getAuthentiCreator()  {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            return Auth::guard('user')->user();
        }
        return null;
    }
}
