<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Factory;
use App\Models\Floor;
use App\Models\Line;
use App\Models\MechineAssing;
use App\Models\MechineType;
use App\Models\Rent;
use App\Models\Requisition;
use App\Models\RequisitionDetails;
use App\Models\Unit;
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
        $unitId       = $request->input('unit', '');


            
       
            
        $result = Line::with('unit:id,name') // Eager load the unit relationship
        ->select('lines.id', 'lines.name','lines.unit_id', DB::raw('SUM(requisitions.total) as total_sum'))
        ->join('requisitions', 'lines.id', '=', 'requisitions.line_id')
        ->where('lines.unit_id', $unitId)
        ->groupBy('lines.id', 'lines.name', 'lines.unit_id')
        ->paginate($itemsPerPage);

        // Add serial number to each item
        $itemsWithSrNumber = collect($result->items())->map(function ($item, $index) use ($result) {
            $item->sr_number = ($result->currentPage() - 1) * $result->perPage() + ($index + 1); // Calculate SR number
            return $item;
        });


        return response()->json([
            'items' => $result->items(), // Current page items
            'total' => $result->total(), // Total number of records
        ]);
    }

    public function machineCalender(Request $request)  {
        
        $currentUser = $this->getAuthentiCreator();
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        $className = get_class($currentUser);

        $lineId = $request->input('line', '');
        $factoryId = $request->input('factory', '');

        $month = $request->input('month', Carbon::now()->month); 
        $year = $request->input('year', Carbon::now()->year);    
        
        // Get the last day of the given month
        $lastDayOfMonth = Carbon::createFromFormat('Y-m', $year . '-' . $month)->endOfMonth();
        // Get the day of the last day
        $lastDay = $lastDayOfMonth->day;
        
        // Merge year and month into "Y-m" format
        $mergeYearMonth = Carbon::create($year, $month, 1)->format('Y-m');
        
        // Format as "Jan-2025"
        $monthYearLabel = Carbon::create($year, $month, 1)->format('M-Y');
        
        // Assign values
        $yearLabel = $monthYearLabel; // "Jan-2025"
        $yearAndMonth = $mergeYearMonth; // "2025-01"
        $total_days_in_month = $lastDay; // Last day [e.g., 31]


       $machineTypes = MechineType::latest()->with('machines')->get();

        //return response()->json( $factoryId,200);



    //    $machine_type = DB::table('requisitions')
    //     ->join('requisition_details', 'requisitions.id', '=', 'requisition_details.requisition_id')
    //     ->join('mechine_types', 'requisition_details.machine_type_id', '=', 'mechine_types.id')
    //     ->when($lineId, function ($query) use ($lineId) {
    //         return $query->where('requisitions.line_id', $lineId);
    //     })
    //     ->whereRaw("DATE_FORMAT(requisitions.startDate, '%Y-%m') = ?", [$mergeYearMonth])
    //     ->select(
    //         'mechine_types.name as machine_type_name',
    //         'requisition_details.machine_type_id',
    //         DB::raw('SUM(requisition_details.mc) as total_mc')
    //     )
    //     ->groupBy('requisition_details.machine_type_id', 'mechine_types.name')
    //     ->get()
    //     ->toArray();


        $totalMachineCountSum = 0;
        $totalByDays = [];
        $result = '';

        $result .= '
            <style>
            .red-col { background-color: #e7bbbb !important; }
            .green-col { background-color: #c8e6f7 !important; }
            .tbl-wrapper { width: 100%; overflow-x: auto; margin: 20px 0; }
            .tbl { width: 100%; border-collapse: collapse; margin: 10px 0; font-family: Arial, sans-serif; }
            .tbl th, .tbl td { padding: 5px; text-align: left; border: 1px solid #a5a5a5; font-size: 11px; }
            .tbl th { background-color: #f2f2f2; font-weight: normal; }
            table .txt-center { text-align: center; }
            table .txt-right { text-align: right; }
            @media (max-width: 768px) { .tbl { font-size: 12px; } .tbl th, .tbl td { padding: 5px; } }
            @media (max-width: 480px) { .tbl { font-size: 10px; } .tbl th, .tbl td { padding: 5px; } .tbl-wrapper { overflow-x: scroll; } }
            </style>';

        $result .= '<div class="tbl-wrapper"><table class="tbl"><thead><tr><th class="txt-center" rowspan="4">No</th><th class="txt-center" rowspan="4">Machine Type</th><th class="txt-center" rowspan="4">Total <br> Active Machine</th></tr><tr><th class="txt-center" colspan="100">' . $yearLabel . '</th></tr><tr>';

        for ($day = 1; $day <= $total_days_in_month; $day++) {
            $class = "";
            if ($day == 1 || $day == $total_days_in_month) {
                $class = "red-col";
            }
            if ($day == 10 || $day == 20) {
                $class = "green-col";
            }

            $result .= '<th class="txt-center ' . $class . '" colspan="2">' . $day . '</th>';
        }

        $result .= '</tr><tr>';

        for ($day = 1; $day <= $total_days_in_month; $day++) {
            $class = "";
            if ($day == 1 || $day == $total_days_in_month) {
                $class = "red-col";
            }
            if ($day == 10 || $day == 20) {
                $class = "green-col";
            }

            $result .= '<th class="' . $class . '">R</th><th  class="' . $class . '">S</th>';
        }

        $result .= '</tr></thead><tbody>';

        foreach ($machineTypes as $index => $type) {

            $query = MechineAssing::where("machine_type_id", $type->id);

            if (!empty($factoryId)) {
                $query->where("factory_id", $factoryId);
            }

            $machineCount = $query->count() ?? 0;

            $result .= '<tr>';
            $result .= '<td class="txt-right">' . ($index + 1) . '</td>';
            $result .= '<td>' . ($type->name ?? '--') . '</td>';
            $result .= '<td class="txt-right">' . ($machineCount ?? '--') . '</td>';

            $totalMachineCountSum += $machineCount ?? 0;

            for ($day = 1; $day <= $total_days_in_month; $day++) {

                $date = $yearAndMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);

                $dateByRequisitionCount = DB::table('requisitions')
                    ->join('requisition_details', 'requisitions.id', '=', 'requisition_details.requisition_id')
                    ->when($lineId, function ($query) use ($lineId) {
                        return $query->where('requisitions.line_id', $lineId);
                    })
                    ->where('requisition_details.machine_type_id', $type->id)
                    ->whereDate('requisitions.startDate', $date)
                    ->sum('requisition_details.mc') ?? 0;

                $dateByMachineCount = DB::table('mechine_assings')
                    ->when($lineId, function ($query) use ($lineId) {
                        return $query->where('line_id', $lineId);
                    })
                    ->where('machine_type_id', $type->id)
                    ->whereDate('created_at', $date)
                    ->count() ?? 0;

                $class = "";
                if ($day == 1 || $day == $total_days_in_month) {
                    $class = "red-col";
                }
                if ($day == 10 || $day == 20) {
                    $class = "green-col";
                }
                $dateByMachineCount = ($dateByRequisitionCount > 0) ? $machineCount : 0;
                $aa =  $dateByMachineCount - $dateByRequisitionCount;

                $difference =   $dateByRequisitionCount - $dateByMachineCount;

                // Ensure the $totalByDays[$day] is initialized before using it
                if (!isset($totalByDays[$day])) {
                    $totalByDays[$day] = ['requisitionCount' => 0, 'difference' => 0];
                }

                // Add daily data to $totalByDays
                $totalByDays[$day]['requisitionCount'] += $dateByRequisitionCount;
                $totalByDays[$day]['difference'] += ($difference > 0) ? $difference : 0;

                $result .= '<td class="' . $class . '">' . $dateByRequisitionCount . '</td><td class="' . $class . '">' . $aa . '</td>';
            }

            $result .= '</tr>';
        }

        $result .= '</tbody><tfoot><tr><td colspan="2" class="txt-right"><strong>Total:</strong></td><td class="txt-right">' . $totalMachineCountSum . '</td>';

        for ($day = 1; $day <= $total_days_in_month; $day++) {
            // Check if the day index exists before accessing
            $requisitionCount = isset($totalByDays[$day]['requisitionCount']) ? $totalByDays[$day]['requisitionCount'] : 0;
            $difference = isset($totalByDays[$day]['difference']) ? $totalByDays[$day]['difference'] : 0;

            $result .= '<td class="txt-right">' . $requisitionCount . '</td>';
            $result .= '<td class="txt-right">' . $difference . '</td>';
        }

        $result .= '</tr></tfoot></table></div>';

        return response()->json($result, 200);

        


    }

    public function machineChange(Request $request){
        $currentUser = $this->getAuthentiCreator();
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        $className = get_class($currentUser);

        $month = $request->input('month', Carbon::now()->month); 
        $year = $request->input('year', Carbon::now()->year);  





        // $factory = $request->input("factory",'');
        // $floor = $request->input('floor','');

        // $lineIds = MechineAssing::where('factory_id', $factory)
        // ->groupBy('line_id')
        // ->pluck('line_id');

        // $floor = $request->input('floor','');

        // $unit = $request->input('unit','');

        // if ($floor) {
        //     $floorData = Floor::where('id', $floor)
        //     ->with(['units.lines' => function ($query) {
        //         $query->select('id', 'unit_id', 'name'); // only necessary columns
        //     }])
        //     ->first();
    
        //     // Extract unique lines from units
        //     $lines = $floorData->units->flatMap->lines->unique('id'); // Group by 'id'
            
        //     return response()->json($lines,200);
        // }

        // if ($unit) {
        //     $lines = Unit::with(['lines' => function ($query) {
        //         $query->select('id', 'unit_id'); 
        //     }])->findOrFail($unit);
        //     return response()->json($lines,200);
        // }

        // return response()->json($lineIds,200);



        $factory = $request->input("factory", '');
        $floor = $request->input('floor', '');
        $unit = $request->input('unit', '');

        // Get all line IDs grouped by factory
        $lineIds = MechineAssing::where('factory_id', $factory)
            ->groupBy('line_id')
            ->pluck('line_id')
            ->toArray(); // Convert to array for easier manipulation

        // Initialize an array to hold all line IDs
        $allLines = $lineIds; // Start with factory line IDs

        // If floor is selected
        if ($floor) {
            $floorData = Floor::where('id', $floor)
                ->with(['units.lines' => function ($query) {
                    $query->select('id', 'unit_id', 'name'); // Only necessary columns
                }])
                ->first();

            if ($floorData) {
                // Extract unique lines from units and add to the array
                $floorLineIds = $floorData->units->flatMap->lines->pluck('id')->toArray();
                $allLines = array_merge($allLines, $floorLineIds);
            }
        }

        // If unit is selected
        if ($unit) {
            $unitData = Unit::with(['lines' => function ($query) {
                $query->select('id', 'unit_id'); 
            }])->findOrFail($unit);

            // Add these lines' IDs to the array
            $unitLineIds = $unitData->lines->pluck('id')->toArray();
            $allLines = array_merge($allLines, $unitLineIds);
        }

        // Remove duplicate line IDs using array_unique
        $allLines = array_unique($allLines);

    
        // Get the last day of the given month
        $lastDayOfMonth = Carbon::createFromFormat('Y-m', $year . '-' . $month)->endOfMonth();
        // Get the day of the last day
        $lastDay = $lastDayOfMonth->day;
        
        // Merge year and month into "Y-m" format
        $mergeYearMonth = Carbon::create($year, $month, 1)->format('Y-m');
        
        // Format as "Jan-2025"
        $monthYearLabel = Carbon::create($year, $month, 1)->format('M-Y');
        




        $yearLabel = $monthYearLabel;
        $yearAndMonth = $mergeYearMonth;
        $total_days_in_month = $lastDay;



        $lineQuery  = Line::with("unit","machines")->findMany($allLines);


        $lines = $lineQuery;


       // return response()->json($lineQuery,200);



        // $lineId = $request->input('line', '');

        // $linesQuery = Line::with(['unit:id,name', 'requisitions:id,total,line_id'])
        // ->whereHas('requisitions', function($query) use ($yearAndMonth) {
        //     $query->where('startDate', 'like', $yearAndMonth . '%');
        // });   

        // if ($lineId) {
        //     // If a specific lineId is provided, filter by that lineId
        //     $linesQuery->where('id', $lineId);
        // }

        
        // // Fetch the lines based on the condition
        // $lines = $linesQuery->get();

        
        // $lineByRequisitions = $lines->map(function ($line) {
        //     return [
        //         'id' => $line->id,
        //         'name' => $line->name,
        //         'unit' => $line->unit, // Unit relationship
        //         'total_requisition_sum' => $line->requisitions->sum('total'), // Sum of 'total' from requisitions
        //     ];
        // });


        $totalRequisitionSum = 0; 
        $totalByDays = []; 
        
        $result = "";
        
        $result .= '
            <style>
            .red-col {
                background-color: #e7bbbb !important;
            }
            .green-col {
                background-color: #c8e6f7 !important;
            }
            .tbl-wrapper {
                width: 100%;
                overflow-x: auto; /* This allows horizontal scrolling on smaller screens */
                margin: 20px 0;
            }
        
            .tbl {
                width: 100%;
                border-collapse: collapse; /* Ensures borders are collapsed for a clean look */
                margin: 10px 0;
                font-family: Arial, sans-serif;
            }
        
            .tbl th, .tbl td {
                padding: 5px;
                text-align: left;
                border: 1px solid #a5a5a5; /* Adds a light gray border around the table cells */
                font-size: 11px;
            }
        
            .tbl th {
                background-color: #f2f2f2; /* Light gray background for header */
                font-weight: normal;
            }
        
            table .txt-center{
                text-align: center;
            }
            table .txt-right{
                text-align: right;
            }
        
            /* Responsive styling */
            @media (max-width: 768px) {
                .tbl {
                    font-size: 12px; /* Smaller font size for mobile devices */
                }
        
                .tbl th, .tbl td {
                    padding: 5px; /* Less padding for smaller screens */
                }
            }
        
            @media (max-width: 480px) {
                .tbl {
                    font-size: 10px; /* Even smaller font size on very small screens */
                }
        
                .tbl th, .tbl td {
                    padding: 5px; /* Minimal padding on small screens */
                }
        
                .tbl-wrapper {
                    overflow-x: scroll; /* Ensure horizontal scroll works for small devices */
                }
            }
        </style>
        
        <div class="tbl-wrapper">
            <table class="tbl">
                <thead>
                    <tr>
                        <th class="txt-center" rowspan="4">No</th>
                        <th class="txt-center" rowspan="4">Units</th>
                        <th class="txt-center" rowspan="4">Location</th>
                        <th class="txt-center" rowspan="4">Allocated </th>
                    </tr>
                    <tr>
                        <th class="txt-center" colspan="100">'. $yearLabel .'</th>
                    </tr>
                    <tr>';
        
                    for ($day = 1; $day <= $total_days_in_month; $day++) {
                        $class="";
                        if($day == 1 || $day == $total_days_in_month){
                            $class="red-col";
                        }
                        if($day == 10 || $day == 20){
                            $class="green-col";
                        }
        
                        $result .= '<th class="txt-center '.$class.'" colspan="2">' . $day . '</th>';
                    }
                        
                    $result .= '</tr>
                    <tr>';
        
                    for ($day = 1; $day <= $total_days_in_month; $day++) {
                        $class="";
                        if($day == 1 || $day == $total_days_in_month){
                            $class="red-col";
                        }
                        if($day == 10 || $day == 20){
                            $class="green-col";
                        }
        
                        $result .= '<th class="'. $class .'">R</th>
                        <th  class="'. $class .'">C</th>';
                    }
        
                    $result .= '</tr>
                </thead>
                <tbody>';
        
                if (empty($lines)) {
                    $totalColumns = 4 + ($total_days_in_month * 2); // 4 for the main columns + 2 for each day's "R" and "C"
                    $result .= '<tr><td colspan="' . $totalColumns . '" class="txt-center">No data available</td></tr>';
                } else {
                    foreach ($lines as $index => $line) { 
                        $result .= '<tr>';
                        $result .= '<td class="txt-right">' . ($index + 1) . '</td>'; 
                        $result .= '<td>' . $line['unit']['name'] . '</td>';
                        $result .= '<td>' . $line['name'] . '</td>';
                        $result .= '<td class="txt-right">' . count($line['machines']) . '</td>';
        
                        $totalRequisitionSum += count($line['machines']);

                        $typesId = [];
                       if($line['machines']){
                            foreach ($line['machines'] as $key => $machine) {
                                array_push($typesId,$machine->machine_type_id);
                            }
                        }


                        for ($day = 1; $day <= $total_days_in_month; $day++) {


                            // Ensure the day is in two-digit format (e.g., "01", "02")
                            $date = $yearAndMonth . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                           
                            $startOfDay = Carbon::parse($date)->startOfDay(); // 2025-01-18 00:00:00
                            $endOfDay = Carbon::parse($date)->endOfDay(); // 2025-01-18 23:59:59
        
                        
                           
                        


                            // Fetch requisition count
                            $dateByRequisitionCount = Requisition::where("line_id", $line['id'])
                               ->join('requisition_details','requisitions.id','=','requisition_details.requisition_id')
                               ->whereIn('machine_type_id', $typesId)
                                ->where('startDate', $date)
                                
                                ->sum('mc');



                            
        
                            $machineCount = MechineAssing::where('line_id',$line->id)
                                ->whereDate('created_at',$date)
                                ->whereIn('machine_type_id',$typesId)->count();


        
        
                            $class = "";
                            if ($day == 1 || $day == $total_days_in_month) {
                                $class = "red-col";
                            }
                            if ($day == 10 || $day == 20) {
                                $class = "green-col";
                            }
        
                            $difference = $machineCount - $dateByRequisitionCount;
        
                            // Add to daily totals
                            if (!isset($totalByDays[$day])) {
                                $totalByDays[$day] = ['requisitionCount' => 0, 'difference' => 0];
                            }
                            $totalByDays[$day]['requisitionCount'] += $dateByRequisitionCount;
                            $totalByDays[$day]['difference'] += $difference;
        
                            $result .= '<td class="' . $class . '">' . $dateByRequisitionCount . '</td><td class="' . $class . '">' . $difference . '</td>';
                        }
        
                        $result .= '</tr>';
                    }
                }
        
                // Add totals to the footer
                $result .= '
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="txt-right"><strong>Total:</strong></td>
                        <td class="txt-right">' . $totalRequisitionSum . '</td>';
        
                    for ($day = 1; $day <= $total_days_in_month; $day++) {
                        if (isset($totalByDays[$day])) {
                            $result .= '<td class="txt-right">' . $totalByDays[$day]['requisitionCount'] . '</td>';
                            $result .= '<td class="txt-right">' . $totalByDays[$day]['difference'] . '</td>';
                        } else {
                            $result .= '<td class="txt-right">0</td>';
                            $result .= '<td class="txt-right">0</td>';
                        }
                    }
        
                $result .= '
                    </tr>
                </tfoot>
            </table>
        </div>';
        
        return response()->json($result, 200);
        
       
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
