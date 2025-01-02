<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Admin;
use App\Models\BreakdownService;
use App\Models\BreakdownServiceDetail;
use App\Models\MechineAssing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BreakdownServiceController extends Controller
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
                $BreakdownServiceQuery = BreakdownService::query();

            } else {
                // If not superadmin, filter by creator type and id
                $BreakdownServiceQuery = BreakdownService::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $BreakdownServiceQuery = BreakdownService::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty

        // if (!empty($search)) {
        //     $BreakdownServiceQuery->where('name', 'LIKE', '%' . $search . '%');
        // }

        if ($dateRange) {
            $dates = explode(',', $dateRange);
            $startDate = Carbon::parse($dates[0])->startOfDay(); // Ensure start of the day for $startDate
            $endDate = Carbon::parse(end($dates))->endOfDay();   // Ensure end of the day for $endDate
        
            $BreakdownServiceQuery =$BreakdownServiceQuery->whereDate('date_time', '>=', $startDate)
                                  ->whereDate('date_time', '<=', $endDate);
        }

        // Apply sorting
        $BreakdownServiceQuery->orderBy($sortBy, $sortOrder);

        $BreakdownServiceQuery->leftJoin('breakdown_service_details', function ($join) {
            $join->on('breakdown_service_details.breakdown_service_id', '=', 'breakdown_services.id')
                 ->whereRaw('breakdown_service_details.created_at = (SELECT MAX(created_at) FROM breakdown_service_details WHERE breakdown_service_id = breakdown_services.id)');
        })
        ->select('breakdown_services.*', 'breakdown_service_details.technician_status', 'breakdown_service_details.id as detail_id');



      





        // Paginate results
        $BreakdownService = $BreakdownServiceQuery->with(['mechine_assing:id,machine_code,name'])
                        ->paginate($itemsPerPage);


        // Return the response as JSON
        return response()->json([
            'items' => $BreakdownService->items(),
            'total' => $BreakdownService->total(),
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
        $service = new BreakdownService();
        $service->uuid = HelperController::generateUuid();
        $service->mechine_assing_id = $request->mechine_assing_id;

        $mechineAssing = MechineAssing::find($request->mechine_assing_id);
        $service->company_id = $mechineAssing ? $mechineAssing->company_id : 0;

        $service->date_time = $request->service_date . " " . $request->service_time;
        $service->service_status = $validatedData['service_status'] ?? 'Pending';
        $service->supervisor_problem_note_id = $request->supervisor_problem_note_id ? json_encode($request->supervisor_problem_note_id) : NULL;
        $service->supervisor_note = $request->supervisor_note;
        $service->creator()->associate($creator);
        $service->updater()->associate($creator);

        // Save the service record
        try {
            $service->save();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create Breakdown Service.'], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Breakdown Service created successfully.',
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
        $BreakdownService = BreakdownService::where('uuid', $uuid)->firstOrFail();

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any BreakdownService
                return response()->json([
                    'success'     => true,
                    'BreakdownService' => $BreakdownService
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the BreakdownService belongs to the current user or admin
        if ($BreakdownService->creator_type !== $creatorType || $BreakdownService->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this BreakdownService.'], 403);
        }
        // Return the BreakdownService data if authorized
        return response()->json([
            'success' => true,
            'BreakdownService' => $BreakdownService
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        // Fetch the BreakdownService by UUID
        $BreakdownService = BreakdownService::where('uuid', $uuid)->firstOrFail();

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
                if ($BreakdownService->creator_type !== $creatorType || $BreakdownService->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this BreakdownService.'], 403);
                }
            }
        }
        // Check if the authenticated user is from the 'user' guard
        elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            if ($BreakdownService->creator_type !== $creatorType || $BreakdownService->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this BreakdownService.'], 403);
            }
        }
        // If no user is authenticated, return unauthorized response
        else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Fetch the MechineAssing model to update company_id
        $mechineAssing = MechineAssing::find($request->mechine_assing_id);
        $BreakdownService->mechine_assing_id = $request->mechine_assing_id;
        $BreakdownService->company_id = $mechineAssing ? $mechineAssing->company_id : 0;
        $BreakdownService->date_time = $request->service_date . " " . $request->service_time;
        $BreakdownService->service_status = $validatedData['service_status'] ?? 'Pending';

        $BreakdownService->supervisor_problem_note_id = $request->supervisor_problem_note_id ? json_encode($request->supervisor_problem_note_id) : NULL;
        $BreakdownService->supervisor_note = $request->supervisor_note;

        // Associate the current user as the updater
        $BreakdownService->updater()->associate($currentUser);
        $BreakdownService->save();

        // Return a success response with the updated BreakdownService
        return response()->json([
            'success' => true,
            'message' => 'Breakdown Service updated successfully.',
            'BreakdownService' => $BreakdownService
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
        // Find BreakdownService by UUID
        $breakdownService = BreakdownService::where('uuid', $uuid)->first();

        if (!$breakdownService) {
            return response()->json(['success' => false, 'message' => 'BreakdownService not found.'], 404);
        }

        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any BreakdownService without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check using uuid for BreakdownService
                if ($breakdownService->creator_type !== $creatorType || $breakdownService->creator_uuid !== $currentUser->uuid) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this BreakdownService.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check using uuid for BreakdownService
            if ($breakdownService->creator_type !== $creatorType || $breakdownService->creator_uuid !== $currentUser->uuid) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this BreakdownService.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the BreakdownService
            $breakdownService->delete();
            return response()->json([
                'success' => true,
                'message' => 'BreakdownService deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting BreakdownService: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    
    public function get_assign_to_technician($uuid)
    {
        $BreakdownService = BreakdownService::where('uuid', $uuid)->firstOrFail();

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any BreakdownService
                return response()->json([
                    'success'     => true,
                    'BreakdownService' => $BreakdownService
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the BreakdownService belongs to the current user or admin
        if ($BreakdownService->creator_type !== $creatorType || $BreakdownService->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this BreakdownService.'], 403);
        }
        // Return the BreakdownService data if authorized
        return response()->json([
            'success' => true,
            'BreakdownService' => $BreakdownService
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
        $service = new BreakdownServiceDetail();

        $service->breakdown_service_id = BreakdownService::where('uuid', $uuid)->first()?->id??0;
        $service->technician_id = $request->technician_id;
        $service->status = 'Processing';
        $service->technician_status = 'Acknowledge';

        $service->creator()->associate($creator);
        $service->updater()->associate($creator);

        // Save the service record
        try {
            $service->save();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save technician assign.'], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Technician assign successfully.',
            'service' => $service
        ], 200);
    }


    public function technician_breakdown_service_acknowledge($detail_id)
    {
        // Retrieve the BreakdownServiceDetail by its ID, or fail if not found
        $BreakdownServiceDetail = BreakdownServiceDetail::where('id', $detail_id)->firstOrFail();

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
        $BreakdownServiceDetail->technician_status = 'Acknowledged';
        $BreakdownServiceDetail->acknowledge_date_time = now();
        $BreakdownServiceDetail->updater()->associate($creator);

        try {
            $BreakdownServiceDetail->save();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save.'], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'I am Acknowledged.',
            'service' => $BreakdownServiceDetail
        ], 200);
    }

    public function breakdown_service_start(Request $request, $detail_id){
        $BreakdownServiceDetail = BreakdownServiceDetail::where('id', $detail_id)->firstOrFail();


        // Retrieve the machine assignment ID from the related BreakdownService
        $mechine_assing_id = BreakdownService::where('id', $BreakdownServiceDetail->breakdown_service_id)
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
        $BreakdownServiceDetail->technician_status = 'Start Service';
        $BreakdownServiceDetail->service_start_date_time = now();
        $BreakdownServiceDetail->updater()->associate($creator);

        try {
            $BreakdownServiceDetail->save();

            //update BreakdownService service_status
            $BreakdownService = BreakdownService::where('id', $BreakdownServiceDetail->breakdown_service_id)->firstOrFail(); 
            $BreakdownService->service_status = 'Processing';
            $BreakdownService->updater()->associate($creator);
            $BreakdownService->save();


        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save.'], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'I am Acknowledged.',
            'service' => $BreakdownServiceDetail
        ], 200);
    }


    public function breakdown_service_start_get_details($detail_id){
        $BreakdownServiceDetail = BreakdownServiceDetail::where('id', $detail_id)->firstOrFail();
        $BreakdownService = BreakdownService::where('id', $BreakdownServiceDetail->breakdown_service_id)->first();

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any BreakdownServiceDetail
                return response()->json([
                    'success'     => true,
                    'BreakdownServiceDetail' => $BreakdownServiceDetail,
                    'BreakdownService' => $BreakdownService
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the BreakdownServiceDetail belongs to the current user or admin
        if ($BreakdownServiceDetail->creator_type !== $creatorType || $BreakdownServiceDetail->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this BreakdownServiceDetail.'], 403);
        }
        // Return the BreakdownServiceDetail data if authorized
        return response()->json([
            'success' => true,
            'BreakdownServiceDetail' => $BreakdownServiceDetail,
            'BreakdownService' => $BreakdownService
        ], Response::HTTP_OK);
    }

    public function breakdown_service_start_save_details(Request $request, $detail_id){
        $BreakdownServiceDetail = BreakdownServiceDetail::where('id', $detail_id)->firstOrFail();

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
        $BreakdownServiceDetail->technician_status = $request->technician_status;
        $BreakdownServiceDetail->status = ($request->technician_status == 'Failed') ? 'Cancel' : $request->technician_status;
        $BreakdownServiceDetail->service_end_date_time = now();
        $BreakdownServiceDetail->problem_note_id = $request->problem_note_id ? json_encode($request->problem_note_id) : NULL;
        $BreakdownServiceDetail->note = $request->note;
        $BreakdownServiceDetail->parts_info = $request->parts_info ? json_encode($request->parts_info) : NULL;
        $BreakdownServiceDetail->updater()->associate($creator);

        try {
            $BreakdownServiceDetail->save();


            //update BreakdownService service_status
            $BreakdownService = BreakdownService::where('id', $BreakdownServiceDetail->breakdown_service_id)->firstOrFail(); 
            $BreakdownService->service_status = ($request->technician_status == 'Failed') ? 'Cancel' : $request->technician_status;
            $BreakdownService->updater()->associate($creator);
            $BreakdownService->save();


        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save.'], 500);
        }

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Data has been saved.',
            'service' => $BreakdownServiceDetail
        ], 200);
    }

    public function trashed(Request $request)
    {
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Initialize the query
        $query = BreakdownService::onlyTrashed();

        // Apply creator filter for non-superadmin users
        if ($currentUser->role !== 'superadmin') {
            $query->where('creator_id', $currentUser->id)
                ->where('creator_type', get_class($currentUser));
        }

        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
        $sortOrder    = $request->input('sortOrder', 'desc');    // Default order is descending
        $search       = $request->input('search', '');          // Search term

        // Apply search filter
        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);

        // Paginate results
        $brands = $query->with(['mechine_assing'])->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $brands->items(),
            'total' => $brands->total(),
        ]);
    }
    public function trashed_count(){
        $trashedCount = BreakdownService::onlyTrashed()->count();
        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }
    public function restore($uuid)
    {
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = BreakdownService::onlyTrashed()->where('uuid', $uuid);

        // Apply creator filter for non-superadmin users
        if ($currentUser->role !== 'superadmin') {
            $query->where('creator_id', $currentUser->id)
                ->where('creator_type', get_class($currentUser));
        }

        $restored = $query->restore();

        return $restored
            ? response()->json(['message' => 'Breakdown Service restored successfully'], Response::HTTP_OK)
            : response()->json(['message' => 'Breakdown Service not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }
    public function forceDelete($uuid)
    {
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = BreakdownService::onlyTrashed()->where('uuid', $uuid);

        // Apply creator filter for non-superadmin users
        if ($currentUser->role !== 'superadmin') {
            $query->where('creator_id', $currentUser->id)
                ->where('creator_type', get_class($currentUser));
        }

        $breakdownService = $query->firstOrFail();

        try {
            $breakdownService->forceDelete();
            return response()->json(['success' => true, 'message' => 'Breakdown Service permanently deleted successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting Breakdown Service: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getAuthenticatedUser()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            return Auth::guard('user')->user();
        }
        return null;
    }
}
