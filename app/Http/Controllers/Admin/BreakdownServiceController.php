<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Admin;
use App\Models\BreakdownService;
use App\Models\Parse;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class BreakdownServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page           = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
        $sortOrder    = $request->input('sortOrder', 'desc');    // Default sort order is descending
        $search       = $request->input('search', '');           // Search term, default is empty
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // If superadmin, retrieve all technicians
                $breakDownProblemNotesQuery = BreakdownService::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $breakDownProblemNotesQuery = BreakdownService::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $breakDownProblemNotesQuery = BreakdownService::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $breakDownProblemNotesQuery->where('break_down_problem_note', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $breakDownProblemNotesQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $breakDownProblemNotes = $breakDownProblemNotesQuery->with('mechineAssing:id,machine_code','creator:id,name','line:id,name','technician:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $breakDownProblemNotes->items(), // Current page items
            'total' => $breakDownProblemNotes->total(), // Total number of records
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
      //  dd($request->all());
        $validatedData = $request->validate(BreakdownService::validationRules());
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
             $creator = Auth::guard('admin')->user();
             // Check if the admin is a superadmin
             if ($creator->role === 'superadmin') {
                 // Superadmin can create technician without additional checks
             } else {
                 // Regular admin authorization check can be implemented here if needed
             }

         } elseif (Auth::guard('user')->check()) {
             $creator = Auth::guard('user')->user();
             // If you want users to have specific restrictions, implement checks here
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }

         $technicianId = Technician::where('status','Available')->pluck('id')->first();

         // Create the technician and associate it with the creator
         $breakDownProblem                = new BreakdownService($validatedData);
         $breakDownProblem->technician_id = $technicianId;
         $breakDownProblem->uuid          = HelperController::generateUuid();
         $breakDownProblem->creator()->associate($creator);  // Assign creator polymorphically
         $breakDownProblem->updater()->associate($creator);  // Associate the updater
         $breakDownProblem->save(); // Save the technician to the database
        // Update the technician's status
        Technician::where('id', $technicianId)->update(['status' => 'Not Available']);
         // Return a success response
        return response()->json(['success' => true, 'message' => 'BreakDown Problem  created successfully.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BreakdownService $breakdownService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BreakdownService $breakdownService)
    {
        //
    }
    public function serviceProcessing($uuid)
    {
      $breakdownService = BreakdownService::where('uuid', $uuid)->firstOrFail();
      // Determine the authenticated user (either from 'admin' or 'user' guard)
      if (Auth::guard('admin')->check()) {
          $currentUser = Auth::guard('admin')->user();
          $creatorType = Admin::class;
          // Check if the admin is a super admin
          if ($currentUser->role === 'superadmin') {
              // Super admins can edit any breakdownService
              return response()->json([
                  'success' => true,
                  'breakdownService' => $breakdownService
              ], Response::HTTP_OK);
          }
      } elseif (Auth::guard('user')->check()) {
          $currentUser = Auth::guard('user')->user();
          $creatorType = User::class;
      } else {
          return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
      }
      // Check if the breakdownService belongs to the current user or admin
      if ($breakdownService->creator_type !== $creatorType || $breakdownService->creator_id !== $currentUser->id) {
          return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this breakdownService.'], 403);
      }
      // Return the breakdownService data if authorized
      return response()->json([
          'success'          => true,
          'breakdownService' => $breakdownService
      ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, BreakdownService $breakdownService)
    // {
    //     //
    // }
    
    public function serviceProcessingUpdate(Request $request, $uuid)
    {
      dd($request->all());
        $breakdownService = BreakdownService::where('uuid', $uuid)->firstOrFail();
        // Validate only the breakdown_service_status field
        $validatedData = $request->validate([
            'breakdown_service_status'          => 'required|in:Done,Cancel',
            'breakdown_problem_note_id'         => 'nullable', // Validate machine_id if provided
            'breakdown_problem_note'            => 'nullable',
            'breakdown_technician_problem_note' => 'nullable',
            'parts_id'                          => 'nullable',
            'parts_quantity'                    => 'nullable',   
        ]);
        

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a superadmin
            if ($currentUser->role !== 'superadmin') {
                if ($breakdownService->creator_type !== $creatorType || $breakdownService->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this breakdownService.'], 403);
                }
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            if ($breakdownService->creator_type !== $creatorType || $breakdownService->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this breakdownService.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if (!empty($validatedData['parts_id']) && !empty($validatedData['parts_quantity'])) {
            $part = Parse::find($validatedData['parts_id']); // Assuming `Part` is the model for the parts table
    
            if ($part->quantity < $validatedData['parts_quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient quantity in inventory for the selected part.',
                ], 422);
            }
    
            // Deduct the quantity
            $part->quantity -= $validatedData['parts_quantity'];
            $part->save();
        }
        // Update only the breakdown_service_status
        $breakdownService->breakdown_problem_note_id                = $validatedData['breakdown_problem_note_id'] ? $validatedData['breakdown_problem_note_id'] : 0;
        $breakdownService->breakdown_service_status                 = $validatedData['breakdown_service_status'];
        $breakdownService->breakdown_problem_note                   = $validatedData['breakdown_problem_note'];
        $breakdownService->breakdown_technician_problem_note        = $validatedData['breakdown_technician_problem_note'];
        $breakdownService->parts_id                                 = $validatedData['parts_id'] ? $validatedData['parts_id'] : 0;
        $breakdownService->parts_quantity                           = $validatedData['parts_quantity'] ? $validatedData['parts_quantity'] : 0;
        $breakdownService->breakdown_service_technician_status      = $validatedData['breakdown_service_status'] == "Done" ? "Success" : "Failed";
        $breakdownService->technician_service_end_time              = now();
        $breakdownService->updater()->associate($currentUser); // Associate the updater
        $breakdownService->save();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Breakdown Service updated successfully.',
            'breakdownService' => $breakdownService,
        ], 200);
    }
    public function update(Request $request, $uuid)
    {
      // dd($request->all());
        $breakdownService = BreakdownService::where('uuid', $uuid)->firstOrFail();
        // Validate only the breakdown_service_status field
        $validatedData = $request->validate([
            'breakdown_service_status' => 'required|in:Processing',
            'machine_id'               => 'nullable|exists:mechine_assings,id', // Validate machine_id if provided
        ]);

        // Check if the machine_id exists in the database (if provided)
        if (!empty($validatedData['machine_id']) && $validatedData['machine_id'] != $breakdownService->machine_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Machine ID. Please provide a valid machine.',
            ], 422);
        }

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a superadmin
            if ($currentUser->role !== 'superadmin') {
                if ($breakdownService->creator_type !== $creatorType || $breakdownService->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this breakdownService.'], 403);
                }
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            if ($breakdownService->creator_type !== $creatorType || $breakdownService->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this breakdownService.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Update only the breakdown_service_status
        $breakdownService->breakdown_service_status      = $validatedData['breakdown_service_status'];
        $breakdownService->breakdown_service_technician_status      = 'Service Running';
        $breakdownService->technician_service_start_time = now();
        $breakdownService->updater()->associate($currentUser); // Associate the updater
        $breakdownService->save();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Breakdown Service updated successfully.',
            'breakdownService' => $breakdownService,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BreakdownService $breakdownService)
    {
        //
    }

     /**
     * Acknowledge the Breakdown Service.
     */
    public function acknowledge(Request $request)
    {
        // Update the breakdown service's technician status
        $updated = BreakdownService::where('uuid', $request->uuid)
            ->update(['breakdown_service_technician_status' => 'Coming',
                    'technician_acknowledge_start_time' => now()]);
        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Breakdown Technician status updated successfully!',
            ], 200);
        }
        // If no rows were updated, return an error response
        return response()->json([
            'success' => false,
            'message' => 'Failed to update breakdown service status.',
        ], 400);
    }
}
