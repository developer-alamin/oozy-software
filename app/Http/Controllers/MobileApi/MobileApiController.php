<?php

namespace App\Http\Controllers\MobileApi;

use App\Http\Controllers\Controller;
use App\Models\BreakDownProblemNote;
use App\Models\BreakdownService;
use App\Models\MechineAssing;
use App\Models\Admin;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelperController;
use App\Models\BreakdownServiceDetail;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
class MobileApiController extends Controller
{
    public function operatormachinecodebydata(Request $request)  {
        
        $currentUser = $this->getAuthentiCreator();

        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $className = get_class($currentUser); // Get the class name (Admin/User)
       
        $validator = Validator::make($request->all(), [
            'machinecode' => 'required|max:255',
        ], [
            'machinecode.required' => 'Machine code is required!',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // If validation passes
        $machinecode = $request->input('machinecode');

        // Check if data exists for the given machine code
        $data = MechineAssing::where('machine_code', $machinecode)
        ->where('creator_type', $className) // Match creator type
        ->where('creator_id', $currentUser->id) // Match creator ID
        ->with([
            'line:id,name,unit_id', // Retrieve line's id, name, and unit_id
            'line.unit:id,name,floor_id', // Retrieve unit's id, name, and floor_id
            'line.unit.floor:id,name' // Retrieve only id and name from floor
        ])
        ->select('id', 'machine_code', 'line_id', 'name', 'created_at')
        ->first();

        
        if (!$data) {
            // If data is not found, return an error response
            return response()->json([
                'success' => false,
                'message' => 'Machine code not found!',
            ], 404);
        }

        $problemsitems = BreakDownProblemNote::latest()
        ->where('creator_type', $className) // Match creator type
        ->where('creator_id', $currentUser->id) // Match creator ID
        ->get();

        // If data is found, return it in the response
        return response()->json([
            'success' => true,
            'message' => 'Machine code processed successfully!',
            'data' => $data, // Return the data
            'problemsitems' => $problemsitems
        ], 200);
    }
     public function operatormachinereport(Request $request){
        $currentUser = $this->getAuthentiCreator();

        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        $className = get_class($currentUser);
        
        // Validation rules and messages
        $validator = Validator::make($request->all(), [
            'mechine_assing_id' => 'required|exists:mechine_assings,id', // Existing validation
            'supervisor_problem_note_id' => 'required_without:supervisor_note|nullable',
            'supervisor_note' => 'required_without:supervisor_problem_note_id|nullable',
        ], [
            'mechine_assing_id.required' => 'Machine ID is required!',
            'mechine_assing_id.exists' => 'Machine ID does not exist!',
            'supervisor_problem_note_id.required_without' => 'Either Supervisor Problem Note ID or Supervisor Note must be provided.',
            'supervisor_note.required_without' => 'Either Supervisor Problem Note ID or Supervisor Note must be provided.',
        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get the first approved technician (if applicable)
        $technician = User::where('status', 'approved')->value('id');

        // Get current date and time in the desired format
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');

        // Find the machine assignment
        $mechineAssing = MechineAssing::find($request->mechine_assing_id);
        if (!$mechineAssing) {
            return response()->json([
                'success' => false,
                'message' => 'Machine Assignment not found!',
            ], 404);
        }

        // Encode problem_note_id if present
        $problemNoteId = $request->has('problem_note_id') ? json_encode($request->problem_note_id) : null;

        // Create Breakdown Service
        $serviceCreate = BreakdownService::create([
            'uuid' => HelperController::generateUuid(), // Ensure this method exists and is working correctly
            'mechine_assing_id' => $request->input('mechine_assing_id'),
            'date_time' => $currentDateTime,
            'company_id' => $mechineAssing->company_id,
            'technician_id' => $technician ?? null,
            'supervisor_problem_note_id' => $problemNoteId,
            'supervisor_note' => $request->input('problem_note', null),
            'creator_id' => $currentUser->id,
            'creator_type' => $className,
            'updater_id' => $currentUser->id,
            'updater_type' => $className
        ]);
        
        // Create Breakdown Service Details
        $serviceDetails = new BreakdownServiceDetail();
        $serviceDetails->uuid = HelperController::generateUuid();
        $serviceDetails->breakdown_service_id = $serviceCreate->id;
        $serviceDetails->technician_id = $technician ?? null;
        $serviceDetails->status = 'Processing';
        $serviceDetails->technician_status = 'Acknowledge';

        // Associate creator and updater for service details
        $serviceDetails->creator()->associate($currentUser);
        $serviceDetails->updater()->associate($currentUser);
        $serviceDetails->save();

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Breakdown service created successfully!',
            'service' => $serviceCreate,
            'serviceDetails' => $serviceDetails,
        ], 200);

        
    }
    public function operatorallmachinebreakdown(Request $request){
        $currentUser = $this->getAuthentiCreator();

        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        
        $className = get_class($currentUser);
       

        $services = BreakdownService::query();

        $services->leftJoin('breakdown_service_details', function ($join) {
            $join->on('breakdown_service_details.breakdown_service_id', '=', 'breakdown_services.id')
                ->whereRaw('breakdown_service_details.created_at = (SELECT MAX(created_at) FROM breakdown_service_details WHERE breakdown_service_id = breakdown_services.id)');
        })
        ->select('breakdown_services.*', 'breakdown_service_details.id as detail_id');

        $services = $services->where('breakdown_services.creator_type', $className) // Specify the table
            ->where('breakdown_services.creator_id', $currentUser->id) // Specify the table
            ->latest()
            ->with([
                'creator:id,name',           // Load creator's name
                'mechine_assing',             // Load machine assignment details
                'service_details',           // Load service details
            ])
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Breakdown service data fetched successfully!',
            'service' => $services,
        ], Response::HTTP_OK);
        
    }


    public function operatorbreakdownservicecheck(Request $request,$status){
        $currentUser = $this->getAuthentiCreator();
        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        $className = get_class($currentUser);

           // Validate the status
        $validStatuses = ['Pending', 'Processing', 'Done', 'Cancel', 'Hold', 'Request'];
        
        if (!in_array($status, $validStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status provided. Valid statuses are: ' . implode(', ', $validStatuses),
            ], 422);
        }


         $service = BreakdownService::where('creator_type', $className)
            ->where('creator_id', $currentUser->id)
            ->where('service_status', $status)
            ->latest()
            ->with([
                'creator:id,name',
                'mechine_assing:id,name',
                'service_details:id,breakdown_service_id,status',
            ])
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Processing Breakdown service data fetched successfully!',
            'data' => $service,
        ], Response::HTTP_OK);

    }

    public function operatorbreakdownservicedecline(Request $request){
        $currentUser = $this->getAuthentiCreator();

        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'service_details_id' => 'required|exists:breakdown_service_details,id',
            'reason' => 'required',
        ], [
            'service_details_id.required' => 'Breakdown Service Detail ID is required!',
            'reason.required' => 'Reason is required!',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Find the breakdown service record by ID or fail
        try {
            $serviceDetails = BreakdownServiceDetail::findOrFail($request->input('service_details_id'));
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Breakdown Service Detail not found!',
            ], 404);
        }
        
        $breakdownservice = BreakdownService::findOrFail($serviceDetails->breakdown_service_id);
        
        // Update the reason field and status of BreakdownService
        $breakdownservice->service_status = 'Pending'; // Assuming you want to update this status
        $breakdownservice->updater()->associate($currentUser);
        $breakdownservice->save();
        
        $serviceDetails->note = $request->input('reason');
        $serviceDetails->status = "Processing";
        $serviceDetails->technician_status = "Start Service";
        $serviceDetails->updater()->associate($currentUser);
        $serviceDetails->save();
        
        // Return the updated data as response
        return response()->json([
            'success' => true,
            'message' => 'Breakdown service reason updated successfully!',
            'data' => $breakdownservice,
        ], Response::HTTP_OK);        

    }

    public function operatorbreakdownserviceresolve(Request $request){
         
        $currentUser = $this->getAuthentiCreator();

        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'service_details_id' => 'required|exists:breakdown_service_details,id',
        ], [
            'service_details_id.required' => 'Breakdown Service Detail ID is required!',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Find the breakdown service record by ID or fail
        try {
            $serviceDetails = BreakdownServiceDetail::findOrFail($request->input('service_details_id'));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Breakdown Service Detail not found!',
            ], 404);
        }
        
        $service = BreakdownService::findOrFail($serviceDetails->breakdown_service_id);
        
        // Update the breakdown service status field
        $service->service_status = "Done";
        $service->updater()->associate($currentUser);
        $service->save();
        
        // Update the breakdown service detail status field
        $serviceDetails->status = "Done";
        $serviceDetails->updater()->associate($currentUser);
        $serviceDetails->save();
        
        // Return the updated data as response
        return response()->json([
            'success' => true,
            'message' => 'Breakdown service status updated successfully!',
            'data' => $serviceDetails,
        ], Response::HTTP_OK);
        

    }

    public function technicianbreakdownservicelistshow(Request $request){
        $currentUser = $this->getAuthentiCreator(); 

        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $className = get_class($currentUser); 

        $serviceDetailsQuery = BreakdownServiceDetail::query();

        // Add where clause with table prefix and eager loading for relationships
        $serviceDetailsQuery->where("breakdown_service_details.technician_id", $currentUser->id)
            ->with(['creator:id,name', 'updater:id,name', 'breckdown_service.mechine_assing']);
        
        // Add the join logic
        $serviceDetailsQuery->leftJoin('breakdown_services', function ($join) {
            $join->on('breakdown_services.id', '=', 'breakdown_service_details.breakdown_service_id')
                ->whereRaw('breakdown_service_details.created_at = (SELECT MAX(created_at) FROM breakdown_service_details WHERE breakdown_service_id = breakdown_services.id)');
        });
        
        // Select the required columns
        $serviceDetailsQuery->select('breakdown_services.*', 'breakdown_service_details.*');
        
        // Execute the query
        $serviceDetails = $serviceDetailsQuery->get();
        
        // Return the response as JSON
        return response()->json([
            'success' => true,
            'serviceDetails' => $serviceDetails,
        ], 200);
    }
    public function technicianmachinecodesearch(Request $request)
    {
        $currentUser = $this->getAuthentiCreator(); // Retrieve authenticated user

        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403); // Unauthorized response
        }
        
        // Validation rules and messages
        $validator = Validator::make($request->all(), [
            'machine_code' => 'required|exists:mechine_assings,machine_code',
        ], [
            'machine_code.required' => 'Machine Code is required!',
            'machine_code.exists' => 'Machine Code does not exist!',
        ]);
        
        // Check for validation errors
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Fetch the machine and its related data
        $machine = MechineAssing::where('machine_code', $request->input('machine_code'))
            ->with([
                'company',
                'line',
                'machineStatus',
                'productModel',
                'brand',
                'mechineType',
                'factory',
                'source',
                'supplier',
                'creator:id,name',
                'updater:id,name',
                'movements',
                'preventive_services',
            ])
            ->first();
        
        if (!$machine) {
            return response()->json([
                'success' => false,
                'message' => 'Machine not found!',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'machine' => $machine,
        ]);
        
    }

    public function save_assign_to_technician(Request $request,$uuid){
    
        $currentUser = $this->getAuthentiCreator();

        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'mechine_assing_id' => 'required|exists:mechine_assings,id',
                'technician_id'     => 'required|exists:users,id',
            ]);
        } catch (ValidationException $e) {
            // Validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors() // Grab the validation error details
            ], 422);
        }

        // Find the breakdown service by UUID
        $breakdownService = BreakdownService::where('uuid', $uuid)->first();
        if (!$breakdownService) {
            return response()->json([
                'success' => false,
                'message' => 'Breakdown service not found.'
            ], 404);
        }

        // Create the new service instance with validated data
        $service = new BreakdownServiceDetail();
        $service->uuid = HelperController::generateUuid();
        $service->breakdown_service_id = $breakdownService->id;
        $service->technician_id = $request->technician_id;
        $service->status = 'Processing';
        $service->technician_status = 'Acknowledge';
        // Associate creator and updater
        $service->creator()->associate($currentUser);
        $service->updater()->associate($currentUser);
       // return response()->json($currentUser,200);
        // Save the service record with error handling
        try {
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save technician assign.'
            ], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Technician assigned successfully.',
            "service" => $service
        ], 200);

    }
    public function TechnicianAcknowedge($details_id)  {
        $currentUser = $this->getAuthentiCreator();

        // Check if the user is authenticated
        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            // Retrieve the BreakdownServiceDetail by its ID
            $breakdownServiceDetail = BreakdownServiceDetail::findOrFail($details_id);
           
            // Update the status and acknowledge date-time
            $breakdownServiceDetail->update([
                'technician_status' => 'Acknowledged',
                'acknowledge_date_time' => now(),
            ]);

            // Associate the updater
            $breakdownServiceDetail->updater()->associate($currentUser);
            $breakdownServiceDetail->save();

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Acknowledged successfully.',
                'service' => $breakdownServiceDetail,
            ], 200);

        } catch (ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'BreakdownServiceDetail not found.'
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function  TechnicianServiceStart(Request $request,$details_id)  {
        // Get the authenticated user
        $currentUser = $this->getAuthentiCreator();

        // Check if the user is authenticated
        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
           // Validate the incoming request data
            $request->validate([
                'mechine_assing_id' => "required|exists:mechine_assings,id"
            ]);
        } catch (ValidationException $e) {
            // Validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors() // Grab the validation error details
            ], 422);
        }


        // Retrieve BreakdownServiceDetail by its ID
        $BreakdownServiceDetail = BreakdownServiceDetail::findOrFail($details_id);

        // Retrieve the machine assignment ID from the BreakdownService
        $mechine_assing_id = BreakdownService::where('id', $BreakdownServiceDetail->breakdown_service_id)
            ->value('mechine_assing_id');

        // Check if the machine assignment matches the requested machine
        if ($mechine_assing_id != $request->mechine_assing_id) {
            return response()->json([
                'success' => false,
                'message' => 'You have selected the wrong machine.'
            ], 400);
        }

        // Update the BreakdownServiceDetail status and service start time
        $BreakdownServiceDetail->update([
            'technician_status' => 'Start Service',
            'service_start_date_time' => now(),
        ]);
        $BreakdownServiceDetail->updater()->associate($currentUser);

        // Update the BreakdownService status
        try {
            $BreakdownService = BreakdownService::findOrFail($BreakdownServiceDetail->breakdown_service_id);
            $BreakdownService->update([
                'service_status' => 'Processing',
            ]);
            $BreakdownService->updater()->associate($currentUser);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save the service status.'
            ], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Service has started successfully.',
            'service' => $BreakdownServiceDetail,
        ], 200);
    }
  
    public function TechnicianStartDetails(Request $request,$details_id){
        // Get the authenticated user
        $currentUser = $this->getAuthentiCreator();
        // Check if the user is authenticated
        if (!$currentUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }
    
        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Find BreakdownServiceDetail by ID
            $BreakdownServiceDetail = BreakdownServiceDetail::findOrFail($details_id);

            // Update BreakdownServiceDetail fields
            $BreakdownServiceDetail->technician_status = "Done";
            $BreakdownServiceDetail->status = "Request";
            $BreakdownServiceDetail->service_end_date_time = now();
            $BreakdownServiceDetail->problem_note_id = $request->problem_note_id ? json_encode($request->problem_note_id) : null;
            $BreakdownServiceDetail->action_id = $request->action_id ? json_encode($request->action_id) : null;
            $BreakdownServiceDetail->updater()->associate($currentUser);

            // Save BreakdownServiceDetail
            $BreakdownServiceDetail->save();

            // Update BreakdownService service_status
            $BreakdownService = BreakdownService::findOrFail($BreakdownServiceDetail->breakdown_service_id);
            $BreakdownService->service_status = "Request";
            $BreakdownService->updater()->associate($currentUser);
            $BreakdownService->save();

            // Commit the transaction
            DB::commit();

        } catch (\Exception $e) {
            // Rollback in case of error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save.',
                'error' => $e->getMessage()
            ], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Data has been saved successfully.',
            'service' => $BreakdownServiceDetail
        ], 200);

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
