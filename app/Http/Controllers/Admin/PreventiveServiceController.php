<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Action;
use App\Models\Admin;
use App\Models\BreakDownProblemNote;
use App\Models\PreventiveService;
use App\Models\PreventiveServiceDetail;
use App\Models\MechineAssing;
use App\Models\Parse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PreventiveServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc'); 
        $dateRange    = $request->input('dateRange', '');

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // If superadmin, retrieve all technicians
                $PreventiveServiceQuery = PreventiveService::query();

            } else {
                // If not superadmin, filter by creator type and id
                $PreventiveServiceQuery = PreventiveService::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $PreventiveServiceQuery = PreventiveService::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
       

        if ($dateRange) {
            // Parse the date to match the format 'YYYY-MM-DD'
            $date = Carbon::createFromFormat('Y-m-d', $dateRange)->startOfDay(); // Start of the day (00:00:00)

            // Filter the query for records where the date portion of `date_time` matches the given date
            $PreventiveServiceQuery = $PreventiveServiceQuery->whereDate('date_time', '=', $date);
        }

        // Apply sorting
        $PreventiveServiceQuery->orderBy($sortBy, $sortOrder);

        $PreventiveServiceQuery->leftJoin('preventive_service_details', function ($join) {
            $join->on('preventive_service_details.preventive_service_id', '=', 'preventive_services.id')
                 ->whereRaw('preventive_service_details.created_at = (SELECT MAX(created_at) FROM preventive_service_details WHERE preventive_service_id = preventive_services.id)');
        })
        ->select('preventive_services.*', 'preventive_service_details.technician_status', 'preventive_service_details.id as detail_id');



        // Paginate results
        $PreventiveService = $PreventiveServiceQuery->with(['mechine_assing:id,machine_code,name'])
                        ->paginate($itemsPerPage);


        // Return the response as JSON
        return response()->json([
            'items' => $PreventiveService->items(),
            'total' => $PreventiveService->total(),
        ]);
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
        $creator = null;
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        }

        if (!$creator) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'mechine_assing_id'   => 'required',
            'service_date' => 'required|date',
            'service_time' => 'required|date_format:H:i',
            'service_status'      => 'required|in:Pending,Processing,Done,Cancel',
        ]);

        // Create the new service instance with validated data
        $service = new PreventiveService();
        $service->uuid = HelperController::generateUuid();
        $service->mechine_assing_id = $request->mechine_assing_id;

        $mechineAssing = MechineAssing::find($request->mechine_assing_id);
        $service->company_id = $mechineAssing ? $mechineAssing->company_id : 0;

        $service->date_time = $request->service_date . " " . $request->service_time;
        $service->service_status = $validatedData['service_status'] ?? 'Pending';
        $service->creator()->associate($creator);
        $service->updater()->associate($creator);

        // Save the service record
        try {
            $service->save();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create preventive service.'], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Preventive service created successfully.',
            'service' => $service
        ], 200);
    }

    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $PreventiveService = PreventiveService::where('uuid', $uuid)->firstOrFail();

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any PreventiveService
                return response()->json([
                    'success'     => true,
                    'PreventiveService' => $PreventiveService
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the PreventiveService belongs to the current user or admin
        if ($PreventiveService->creator_type !== $creatorType || $PreventiveService->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this PreventiveService.'], 403);
        }
        // Return the PreventiveService data if authorized
        return response()->json([
            'success' => true,
            'PreventiveService' => $PreventiveService
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        // Fetch the PreventiveService by UUID
        $PreventiveService = PreventiveService::where('uuid', $uuid)->firstOrFail();

        // Validate the incoming request data
        $validatedData = $request->validate([
            'mechine_assing_id' => 'required',
            'service_date' => 'required|date',
            'service_time' => 'required|date_format:H:i:s',
            'service_status' => 'required|in:Pending,Processing,Done,Cancel',
        ]);

        // Initialize variables for the current authenticated user and creator type
        $currentUser = null;
        $creatorType = null;

        // Check if the authenticated user is from the 'admin' guard
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // If the admin is a superadmin, they can update any service
            if ($currentUser->role === 'superadmin') {
                // No further checks needed for superadmin
            } else {
                // Regular admin authorization check
                if ($PreventiveService->creator_type !== $creatorType || $PreventiveService->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this PreventiveService.'], 403);
                }
            }
        }
        // Check if the authenticated user is from the 'user' guard
        elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            if ($PreventiveService->creator_type !== $creatorType || $PreventiveService->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this PreventiveService.'], 403);
            }
        }
        // If no user is authenticated, return unauthorized response
        else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Fetch the MechineAssing model to update company_id
        $mechineAssing = MechineAssing::find($request->mechine_assing_id);
        $PreventiveService->mechine_assing_id = $request->mechine_assing_id;
        $PreventiveService->company_id = $mechineAssing ? $mechineAssing->company_id : 0;
        $PreventiveService->date_time = $request->service_date . " " . $request->service_time;
        $PreventiveService->service_status = $validatedData['service_status'] ?? 'Pending';

        // Associate the current user as the updater
        $PreventiveService->updater()->associate($currentUser);
        $PreventiveService->save();

        // Return a success response with the updated PreventiveService
        return response()->json([
            'success' => true,
            'message' => 'Preventive Service updated successfully.',
            'PreventiveService' => $PreventiveService
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        try {
            // Find the PreventiveService record by UUID
            $preventiveService = PreventiveService::where('uuid', $uuid)->first();

            // If the record is not found, return a 404 response
            if (!$preventiveService) {
                return response()->json([
                    'success' => false,
                    'message' => 'Preventive Service not found.',
                ], 404);
            }

            // Check user authorization
            if (Auth::guard('admin')->check()) {
                $currentUser = Auth::guard('admin')->user();
                
                if ($currentUser->role === 'superadmin') {
                    // Superadmin can delete without additional checks
                } else {
                    // Regular admin authorization check
                    if (
                        $preventiveService->creator_type !== Admin::class || 
                        $preventiveService->creator_id !== $currentUser->id
                    ) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Forbidden: You are not authorized to delete this Preventive Service.',
                        ], 403);
                    }
                }
            } elseif (Auth::guard('user')->check()) {
                $currentUser = Auth::guard('user')->user();

                if (
                    $preventiveService->creator_type !== User::class || 
                    $preventiveService->creator_id !== $currentUser->id
                ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Forbidden: You are not authorized to delete this Preventive Service.',
                    ], 403);
                }
            } else {
                // If no authorized user is found
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            // Attempt to delete the record
            $preventiveService->delete();

            return response()->json([
                'success' => true,
                'message' => 'Preventive Service deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Preventive Service: ' . $e->getMessage(),
            ], 500);
        }
    }


    
    public function get_assign_to_technician($uuid)
    {
        $PreventiveService = PreventiveService::where('uuid', $uuid)->firstOrFail();

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any PreventiveService
                return response()->json([
                    'success'     => true,
                    'PreventiveService' => $PreventiveService
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the PreventiveService belongs to the current user or admin
        if ($PreventiveService->creator_type !== $creatorType || $PreventiveService->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this PreventiveService.'], 403);
        }
        // Return the PreventiveService data if authorized
        return response()->json([
            'success' => true,
            'PreventiveService' => $PreventiveService
        ], Response::HTTP_OK);
    }

    public function save_assign_to_technician(Request $request, $uuid)
    {

        // Check which authentication guard is in use and set the creator
        $creator = null;
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        }

        if (!$creator) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'mechine_assing_id'     => 'required|numeric',
            'technician_id'         => 'required|numeric',
        ]);
    
        // Create the new service instance with validated data
        $service = new PreventiveServiceDetail();
        $service->uuid = HelperController::generateUuid();
        $service->preventive_service_id = PreventiveService::where('uuid', $uuid)->first()?->id??0;
        $service->technician_id = $request->technician_id;
        $service->status = 'Processing';
        $service->technician_status = 'Acknowledge';

        $service->creator()->associate($creator);
        $service->updater()->associate($creator);

        // Save the service record
        try {
            $service->save();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save technician assign.'.$e], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Technician assign successfully.',
            'service' => $service
        ], 200);
    }


    public function technician_preventive_service_acknowledge($detail_id)
    {
        // Retrieve the PreventiveServiceDetail by its ID, or fail if not found
        $PreventiveServiceDetail = PreventiveServiceDetail::where('id', $detail_id)->firstOrFail();

        // Check which authentication guard is in use and set the creator
        $creator = null;
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        }

        // If no creator (user/admin) is found, return Unauthorized response
        if (!$creator) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Update the status and acknowledge date time
        $PreventiveServiceDetail->technician_status = 'Acknowledged';
        $PreventiveServiceDetail->acknowledge_date_time = now();
        $PreventiveServiceDetail->updater()->associate($creator);

        try {
            $PreventiveServiceDetail->save();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save.'], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'I am Acknowledged.',
            'service' => $PreventiveServiceDetail
        ], 200);
    }

    public function preventive_service_start(Request $request, $detail_id){
        $PreventiveServiceDetail = PreventiveServiceDetail::where('id', $detail_id)->firstOrFail();


        // Retrieve the machine assignment ID from the related PreventiveService
        $mechine_assing_id = PreventiveService::where('id', $PreventiveServiceDetail->preventive_service_id)
            ->value('mechine_assing_id'); // Directly retrieve the value of 'mechine_assing_id'

        // Check if the machine assignment exists
        if ($mechine_assing_id != $request->mechine_assing_id) {
            return response()->json(['success' => false, 'message' => 'You have selected the wrong machine.'], 400);
        }


        // Check which authentication guard is in use and set the creator
        $creator = null;
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        }

        // If no creator (user/admin) is found, return Unauthorized response
        if (!$creator) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Update the status and acknowledge date time
        $PreventiveServiceDetail->technician_status = 'Start Service';
        $PreventiveServiceDetail->service_start_date_time = now();
        $PreventiveServiceDetail->updater()->associate($creator);

        try {
            $PreventiveServiceDetail->save();

            //update PreventiveService service_status
            $PreventiveService = PreventiveService::where('id', $PreventiveServiceDetail->preventive_service_id)->firstOrFail(); 
            $PreventiveService->service_status = 'Processing';
            $PreventiveService->updater()->associate($creator);
            $PreventiveService->save();


        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save.'], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'I am Acknowledged.',
            'service' => $PreventiveServiceDetail
        ], 200);
    }


    public function preventive_service_start_get_details($detail_id){
        $PreventiveServiceDetail = PreventiveServiceDetail::where('id', $detail_id)->firstOrFail();
        $PreventiveService = PreventiveService::where('id', $PreventiveServiceDetail->preventive_service_id)->first();

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any PreventiveServiceDetail
                return response()->json([
                    'success'     => true,
                    'PreventiveServiceDetail' => $PreventiveServiceDetail,
                    'PreventiveService' => $PreventiveService
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the PreventiveServiceDetail belongs to the current user or admin
        if ($PreventiveServiceDetail->creator_type !== $creatorType || $PreventiveServiceDetail->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this PreventiveServiceDetail.'], 403);
        }
        // Return the PreventiveServiceDetail data if authorized
        return response()->json([
            'success' => true,
            'PreventiveServiceDetail' => $PreventiveServiceDetail,
            'PreventiveService' => $PreventiveService
        ], Response::HTTP_OK);
    }

    public function preventive_service_start_save_details(Request $request, $detail_id){
        $PreventiveServiceDetail = PreventiveServiceDetail::where('id', $detail_id)->firstOrFail();

        // Check which authentication guard is in use and set the creator
        $creator = null;
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        }

        // If no creator (user/admin) is found, return Unauthorized response
        if (!$creator) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Update the status and acknowledge date time
        $PreventiveServiceDetail->technician_status = $request->technician_status;
        $PreventiveServiceDetail->status = ($request->technician_status == 'Failed') ? 'Cancel' : $request->technician_status;
        $PreventiveServiceDetail->service_end_date_time = now();
        $PreventiveServiceDetail->problem_note_id = $request->problem_note_id ? json_encode($request->problem_note_id) : NULL;
        $PreventiveServiceDetail->action_id = $request->action_id ? json_encode($request->action_id) : NULL;
        $PreventiveServiceDetail->helper_technician_id = $request->technician_id ? json_encode($request->technician_id) : NULL;

        $PreventiveServiceDetail->note = $request->note;
        $PreventiveServiceDetail->parts_info = $request->parts_info ? json_encode($request->parts_info) : NULL;
        $PreventiveServiceDetail->updater()->associate($creator);

        try {
            $PreventiveServiceDetail->save();


            //update PreventiveService service_status
            $PreventiveService = PreventiveService::where('id', $PreventiveServiceDetail->preventive_service_id)->firstOrFail(); 
            $PreventiveService->service_status = ($request->technician_status == 'Failed') ? 'Cancel' : $request->technician_status;
            $PreventiveService->updater()->associate($creator);
            $PreventiveService->save();


        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save.'], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Data has been saved.',
            'service' => $PreventiveServiceDetail
        ], 200);
    }


    public function trashed_count(){
        $trashedCount = PreventiveService::onlyTrashed()->count();
        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }

    public function preventiveservicedetailslist(Request $request, $uuid)
    {
        // Default pagination settings
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc'); 
        $search       = $request->input('search', '');

        // Fetch the PreventiveService by UUID
        $preventiveService = PreventiveService::where('uuid', $uuid)->first();

        if (!$preventiveService) {
            return response()->json(['success' => false, 'message' => 'Preventive service not found'], 404);
        }

       // Get the preventive service by UUID
        $preventiveService = PreventiveService::with(['serviceDetails' => function ($query) use ($sortBy, $sortOrder) {
            // Apply sorting to service details
            $query->orderBy($sortBy, $sortOrder);
        }])->where('uuid', $uuid)->first();

        if (!$preventiveService) {
            return response()->json(['success' => false, 'message' => 'Preventive Service not found'], 404);
        }

        // Paginate service details
        $serviceDetailsPaginated = $preventiveService->serviceDetails()->paginate($itemsPerPage);

        return response()->json([
            'items' => $serviceDetailsPaginated->items(),
            'total' => $serviceDetailsPaginated->total(),
        ]);
    }
    public function preventivesignleservicedetails($uuid){
        $singleService = PreventiveServiceDetail::where('uuid',$uuid)
        ->with(['creator','preventiveService.mechine_assing','preventiveService.company',"user"])
        ->firstOrFail();

        // Decode the parts_info if available
        $decodedPartsInfo = [];
        $parsedParts = []; // To store parts with name and qty
       
        if (!empty($singleService->parts_info)) {
            $decodedPartsInfo = json_decode($singleService->parts_info, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON format in parts_info.',
                ], 400);
            }
    
            // Fetch part names from the Parse model
            $partIds = array_column($decodedPartsInfo, 'parts_id'); // Extract parts_id from JSON
            $parts = Parse::whereIn('id', $partIds)->pluck('name', 'id'); // Get parts name by id
    
            // Combine the name and qty
            foreach ($decodedPartsInfo as $part) {
                $parsedParts[] = [
                    'name' => $parts[$part['parts_id']] ?? 'Unknown Part', // Part name or fallback
                    'qty'  => $part['qty'], // Quantity from parts_info
                ];
            }
        }
        // Decode the problem_note_id if available
        $decodedProblemNoteIds = [];
        $parsedProblemNotes = []; // To store problem notes

        if (!empty($singleService->problem_note_id)) {
            $decodedProblemNoteIds = json_decode($singleService->problem_note_id, true);
    
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON format in problem_note_id.',
                ], 400);
            }
    
            // Fetch problem notes from the BreakDownProblemNote model
            $parsedProblemNotes = BreakDownProblemNote::whereIn('id', $decodedProblemNoteIds)->get(); // Get notes by id
    
            
        }


        // Decode the action_ids if available
        $decodedActionIds = [];
        $parsedActions = []; // To store actions

        if (!empty($singleService->action_id)) {
            $decodedActionIds = json_decode($singleService->action_id, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON format in action_id.',
                ], 400);
            }

            // Fetch actions from the Action model
            $parsedActions = Action::whereIn('id', $decodedActionIds)->get(); // Get actions by id
        }

         // Add the parsed parts info and problem notes to the response
        $singleService->parsed_parts_info = $parsedParts;
        $singleService->problem_notes = $parsedProblemNotes;
        $singleService->actions = $parsedActions;
       
        return response()->json($singleService,200);
    }
    public function preventiveservicetrashed(Request $request){
        
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all soft-deleted preventive services
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed preventive services without additional checks
                $preventiveServicesQuery = PreventiveService::onlyTrashed();
            } else {
                // Regular admin authorization check
                $preventiveServicesQuery = PreventiveService::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $preventiveServicesQuery = PreventiveService::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this user

        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is descending
        $dateRange       = $request->input('dateRange', ''); // Search term, default is empty

        // Apply search if the search term is not empty
        if (!empty($search)) {
            $preventiveServicesQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your preventive service fields
        }


        // Apply sorting
        $preventiveServicesQuery->orderBy($sortBy, $sortOrder);
        if ($dateRange) {
            $dates = explode(',', $dateRange);
            $startDate = Carbon::parse($dates[0])->startOfDay(); // Ensure start of the day for $startDate
            $endDate = Carbon::parse(end($dates))->endOfDay();   // Ensure end of the day for $endDate
        
            $preventiveServicesQuery = $preventiveServicesQuery->whereDate('date_time', '>=', $startDate)
                                  ->whereDate('date_time', '<=', $endDate);
        }
        // Paginate results
        $preventiveServices = $preventiveServicesQuery->with(['mechine_assing'])->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $preventiveServices->items(), // Current page items
            'total' => $preventiveServices->total(), // Total number of trashed records
        ]);
    }
    public function preventiveserviceforceDelete($uuid)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed preventive services
            if ($currentUser->role === 'superadmin') {
                $preventiveService = PreventiveService::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
            } else {
                // Regular admin authorization check
                $preventiveService = PreventiveService::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->where('uuid', $uuid)
                    ->firstOrFail();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $preventiveService = PreventiveService::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->where('uuid', $uuid)
                ->firstOrFail();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
            // Force delete the preventive service
            $preventiveService->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Preventive service permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting preventive service: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function preventiveserviceRestore($uuid)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
    
            // Superadmin check: Allow access to all trashed preventive services
            if ($currentUser->role === 'superadmin') {
                $restored = PreventiveService::onlyTrashed()->where('uuid', $uuid)->firstOrFail()->restore();
            } else {
                // Regular admin authorization check
                $restored = PreventiveService::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->where('uuid', $uuid)
                    ->firstOrFail()
                    ->restore();
            }
    
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
    
            // Regular user authorization check
            $restored = PreventiveService::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->where('uuid', $uuid)
                ->firstOrFail()
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    
        if ($restored) {
            return response()->json(['message' => 'Preventive service restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Preventive service not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }
    
}
