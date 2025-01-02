<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\MechineAssing;
use App\Models\Operator;
use App\Models\Parse;
use App\Models\ParseStockIn;
use App\Models\Service;
use App\Models\ServiceHistory;
use App\Models\ServiceParse;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
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
            'company_id'          => 'required',
            'mechine_id'          => 'required',
            'service_time'        => 'nullable',
            'service_date'        => 'nullable',
            'service_type_status' => 'nullable',
            // 'status'     => 'required|in:pending,in_progress,completed,cancelled',
        ]);

        return response()->json( $validatedData,200);

        // Create the new service instance with validated data
        $service       = new Service($validatedData);
        // Associate the creator and updater polymorphically
        $service->uuid = HelperController::generateUuid();

        $service->creator()->associate($creator);
        $service->updater()->associate($creator);
        // Save the serv$service record
        $service->save();
        // Return a success response
        // return response()->json([
        //     'success'        => true,
        //     'message'        => 'service created successfully.',
        //     'service' => $service
        // ], 200);
    }

    public function storeHistory(Request $request)
    {

        // dd($request->all());
        // Step 1: Validate the incoming request data
        $validatedData = $request->validate([
            // Service History details
            'service_id'                    => 'required',
            'operator_id'                   => 'required',
            'operator_mechine_problem_note' => 'nullable|string',
            'operator_cell_time'            => 'nullable',
            'technician_id'                 => 'required',
            'technician_note'               => 'nullable|string',
            'technician_arrive_time'        => 'nullable',
            'technician_working_time'       => 'nullable',
            'technician_status'             => 'nullable',
            // 'history_status' => 'required|in:initiated,under_review,resolved,escalated',
            // Service Parse (Parts) details
            'parses'            => 'required|array', // Array of parts
            'parses.*.parse_id' => 'required|exists:parses,id',
            'parses.*.use_qty' => 'required|integer|min:1',
        ]);
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Step 2: Start a database transaction to ensure atomicity of all operations
        DB::beginTransaction();

        try {
           

            $serviceHistory             = new ServiceHistory($validatedData);
            // Associate the creator and updater polymorphically
            $serviceHistory->uuid       = HelperController::generateUuid();
            $serviceHistory->service_id = $request->service_id;
            $serviceHistory->creator()->associate($creator);
            $serviceHistory->updater()->associate($creator);
            $serviceHistory->save();

            // Step 4: Create the Service History record
            // $serviceHistory = ServiceHistory::create([
            //     'service_id'                    => $request->service_id,
            //     'operator_id'                   => $validatedData['operator_id'],
            //     'operator_mechine_problem_note' => $validatedData['operator_note'],
            //     'operator_call_time'            => $validatedData['operator_call_time'],
            //     'technician_id'                 => $validatedData['technician_id'],
            //     'technician_note'               => $validatedData['technician_note'],
            //     'technician_arrive_time'        => $validatedData['technician_arrive_time'],
            //     'technician_working_time'       => $validatedData['technician_working_time'],
            //     'technician_status'             => $validatedData['technician_status'],
            // ]);

            // Step 5: Loop through each part (parse) and create a ServiceParse record while deducting stock
            foreach ($validatedData['parses'] as $parseData) {
                // dd($parseData);
                // Find the parse stock record in the ParseInStock table
                $parseStock = ParseStockIn::where('parse_id', $parseData['parse_id'])->first();
                // Check if there is enough quantity in stock
                if (!$parseStock || $parseStock->quantity_in < $parseData['use_qty']) {
                    throw new \Exception("Not enough stock for part ID {$parseData['parse_id']} in ParseInStock");
                }

                // Deduct the quantity immediately
                $parseStock->quantity_in -= $parseData['use_qty'];
                $parseStock->save();

                // Step 6: Create the ServiceParse record for the used part
                // ServiceParse::create([
                //     'service_id' => $request->service_id,
                //     'parse_id'   => $parseData['parse_id'],
                //     'use_qty'    => $parseData['use_qty'],
                // ]);

                $serviceParse = new ServiceParse([
                    'service_id' => $request->service_id,
                    'parse_id'   => $parseData['parse_id'],
                    'use_qty'    => $parseData['use_qty'],  // Adjust status as needed
                ]);
        
                $serviceParse->creator()->associate($creator);
                $serviceParse->updater()->associate($creator);
                $serviceParse->save();
        
            }

            // Step 7: Commit the transaction if everything is successful
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Service created successfully.',
            ], 200);
            // Step 8: Return a success response with a message
            // return response()->json(['message' => 'Service and related records created successfully with stock deducted from ParseInStock!'], 201);

        } catch (\Exception $e) {
            // Step 9: If any error occurs, roll back the transaction to maintain data integrity
            DB::rollBack();

            // Step 10: Return an error response with the exception message
            return response()->json(['message' => 'Failed to create service: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }

    public function getMechins(Request $request)
    {
        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for mechines by name with a limit
        $mechines = MechineAssing::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the mechine as JSON
        return response()->json($mechines);
    }
    public function getOperators(Request $request)
    {
         // Get search term and limit from the request, with defaults
         $search = $request->query('search', '');
         $limit  = $request->query('limit', 5); // Default limit of 10
         // Query to search for operators by name with a limit
         $operators = Operator::where('name', 'like', '%' . $search . '%')
                      ->limit($limit)
                      ->get();
         // Return the operators as JSON
         return response()->json($operators);
    }
    public function getTechnicians(Request $request)
    {
         // Get search term and limit from the request, with defaults
         $search = $request->query('search', '');
         $limit  = $request->query('limit', 5); // Default limit of 10
         // Query to search for technicians by name with a limit
         $technicians = Technician::where('name', 'like', '%' . $search . '%')
                      ->limit($limit)
                      ->get();
         // Return the technicians as JSON
         return response()->json($technicians);
    }
    public function getParses(Request $request)
    {
        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for parses by name with a limit
        $parses = Parse::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the parses as JSON
        return response()->json($parses);
    }
}
