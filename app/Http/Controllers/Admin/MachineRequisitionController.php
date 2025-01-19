<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Factory;
use App\Models\Line;
use App\Models\MechineAssing;
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

        $machine  = MechineAssing::first();




        $line = '1';  
        $month = '2025-01';  
       
        // Set start and end dates for the given month
        $startDate = Carbon::parse($month . '-01');
        $endDate = $startDate->copy()->endOfMonth();
        $dates = [];
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            // Format the date to show only the day
            $dates[] = $date->format('d'); // Only the day part
        }

        // Query to calculate total required, total assigned, and shortage for each machine type
        $result = DB::table('requisition_details')
            ->join('requisitions', 'requisitions.id', '=', 'requisition_details.requisition_id') // Join requisitions table
            ->leftJoin('mechine_assings', function ($join) {
                $join->on('requisition_details.machine_type_id', '=', 'mechine_assings.machine_type_id')
                    ->whereColumn('requisitions.line_id', '=', 'mechine_assings.line_id'); // Match line_id
            })
            ->select(
                'requisition_details.machine_type_id', // Machine type
                DB::raw('DATE(requisitions.created_at) as date'), // Date
                DB::raw('SUM(requisition_details.mc) as total_required'), // Sum mc for each requisition_id
                DB::raw('COUNT(DISTINCT mechine_assings.id) as total_assigned'), // Count unique assignments
                DB::raw('SUM(requisition_details.mc) - COUNT(DISTINCT mechine_assings.id) as shortage') // Calculate shortage
            )
            ->whereRaw('DATE_FORMAT(requisitions.created_at, "%Y-%m") = ?', [$month]) // Filter by year-month
            ->where('requisitions.line_id', '=', $line) // Filter by line
            ->groupBy('requisition_details.machine_type_id', 'date') // Group by machine type and date
            ->get();

        // Map results to include all dates
        $machineData = [];
        foreach ($result as $row) {
            // Convert the date to day format (e.g., 11 from 2025-01-11)
            $dateOnlyDay = Carbon::parse($row->date)->format('d');
            
            $machineData[$row->machine_type_id][$dateOnlyDay] = [
                'total_required' => $row->total_required,
                'total_assigned' => $row->total_assigned,
                'shortage' => $row->shortage,
            ];
        }

        // Prepare JSON response data
        $responseData = [
            'headers' => $dates, // All dates (now only days) for table headers
            'rows' => [], // Machine type data
        ];

        foreach ($machineData as $machineTypeId => $dateData) {
            $row = [
                'machine_type' => $machineTypeId,
                'data' => [],
            ];

            foreach ($dates as $date) {
                $row['data'][] = [
                    'total_required' => $dateData[$date]['total_required'] ?? 0,
                    'total_assigned' => $dateData[$date]['total_assigned'] ?? 0,
                    'shortage' => $dateData[$date]['shortage'] ?? 0,
                ];
            }

            $responseData['rows'][] = $row;
        }

        return response()->json($responseData,200);
        
        $page           = $request->input('page', 1);
        $itemsPerPage   = $request->input('itemsPerPage', 5);
        $sortBy         = $request->input('sortBy', 'created_at'); 
        $sortOrder      = $request->input('sortOrder', 'desc');
        $lineId = $request->input('line', '');


        $datePicker = $request->input('datePicker','');

        if (!empty($datePicker)) {
            // Use Carbon to parse the date and extract the month and year
            $date = Carbon::parse($datePicker);

            // Extract the month as a 2-digit number (01, 02, ..., 12)
            $month = $date->format('m');  // 'm' gives the 2-digit month

             // Extract the full month name (e.g., January, February)
             $monthName = $date->format('F');  // 'F' gives the full month name
             return response()->json($monthName,200);

        }



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


    public function lineWiseMachineTypes(Request $request)
    {   
        $currentUser = $this->getAuthentiCreator();
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $className = get_class($currentUser);
        $line_id = $request->input("line",'');


        $line = Line::with('machines.mechineType')->findOrFail(intval($line_id));

        $groupedMachines = $line->machines->groupBy(function ($machine) {
            return $machine->mechineType->id;
        });

        $result = [
            'machine_types' => $groupedMachines->map(function ($machines, $machineTypeId) {
                $machineType = $machines->first()->mechineType;
                return [
                    'id' => $machineType->id,
                    'name' => $machineType->name,
                    "mc" => 0,
                ];
            })->values(),
            'machines' => $groupedMachines->map(function ($machines, $machineTypeId) {
                return $machines->values();
            })->values()
        ];

        return response()->json($result, 200);
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
