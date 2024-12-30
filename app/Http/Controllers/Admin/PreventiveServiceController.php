<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\PreventiveService;
use App\Models\PreventiveServiceDetail;
use App\Models\MechineAssing;
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
        $search       = $request->input('search', '');

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
        // Apply search if the search term is not empty

        // if (!empty($search)) {
        //     $PreventiveServiceQuery->where('name', 'LIKE', '%' . $search . '%');
        // }

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
    public function destroy(string $id)
    {
        //
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
            return response()->json(['success' => false, 'message' => 'Failed to save technician assign.'], 500);
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



    public function trashed_count(){
        $trashedCount = PreventiveService::onlyTrashed()->count();
        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }
}
