<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Requests\MachineAssignStoreRequest;
use App\Http\Requests\MechineTransferStore;
use App\Models\Admin;
use App\Models\MechineAssing;
use App\Models\Brand;
use App\Models\Factory;
use App\Models\Line;
use App\Models\Supplier;
use App\Models\GeneralSetting;
use App\Models\MachineStatus;
use App\Models\MechineStock;
use App\Models\MechineType;
use App\Models\ProductModel;
use App\Models\Rent;
use Carbon\Carbon;
use Illuminate\Http\Response;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MechineAssingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $page         = $request->input('page', 1);
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
                $machineAssignQuery = MechineAssing::query(); // No filters applied

            } else {
                // If not superadmin, filter by creator type and id
                $machineAssignQuery = MechineAssing::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $machineAssignQuery = MechineAssing::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $machineAssignQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $machineAssignQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $machineAssign = $machineAssignQuery->where('status', '!=', 'History')
                          ->with([
                            'creator',                          // Creator of the machine assignment
                            'factory.company',
                            //'factory.user:id,name',                           // Direct factory relationship
                            // 'factory.floors:id,name,factory_id',        // Floors within the factory
                            // 'factory.floors.units:id,name,floor_id',    // Units within the floors
                            // 'factory.floors.units.lines:id,name,unit_id', // Lines within the units
                            'machineStatus',                   // Machine status
                            'line.unit.floor.factory',
                            'machineStatus',                   // Machine status
                            'productModel',                    // Product model
                            'mechineType'                      // Machine type
                        ])
                        ->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $machineAssign->items(), // Current page items
            'total' => $machineAssign->total(), // Total number of records
        ]);
    }



      /**
     * Display a listing of the resource.
     */
    public function mechineTransferList(Request $request)
    {
        $page         = $request->input('page', 1);
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
                $mechineAssingQuery = MechineAssing::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $mechineAssingQuery = MechineAssing::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $mechineAssingQuery = MechineAssing::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $mechineAssingQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $mechineAssingQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $mechineAssing = $mechineAssingQuery->where('mechine_status','Transferred')->with('creator:id,name','user:id,name','factory:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $mechineAssing->items(), // Current page items
            'total' => $mechineAssing->total(), // Total number of records
        ]);
    }

      /**
     * Display a listing of the resource.
     */
    public function machineHistoryList(Request $request)
    {
        $page         = $request->input('page', 1);
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
                $machineAssignQuery = MechineAssing::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $machineAssignQuery = MechineAssing::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $machineAssignQuery = MechineAssing::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $machineAssignQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $machineAssignQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $machineAssign = $machineAssignQuery->where('status','History')->with([
          'creator:id,name',                          // Creator of the machine assignment
          'factory:id,name,company_id',
          'factory.user:id,name',                           // Direct factory relationship
          'machineStatus:id,name',                   // Machine status
          'line.unit.floor.factory.user:id,name',
          'machineStatus:id,name',                   // Machine status
          'productModel:id,name',                    // Product model
          'mechineType:id,name'                      // Machine type
        ])->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $machineAssign->items(), // Current page items
            'total' => $machineAssign->total(), // Total number of records
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

    public function store(MachineAssignStoreRequest $request)
    {


        // dd($request->all());
        // Check which authentication guard is in use and set the creator
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Validate the incoming request data
        $validatedData = $request->validated();

        // Process dates to handle timezone issues and format them properly
        if (!empty($request->purchase_date) && $request->purchase_date !== 'null') {
            // Remove extra characters like "(timezone)" if any and parse the date
            $validatedData['purchase_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $request->purchase_date)
            )->format('Y-m-d');
        } else {
            $validatedData['purchase_date'] = null; // Set to null if no valid date is provided
        }

        if (!empty($request->rent_date) && $request->rent_date !== 'null') {
            // Remove extra characters like "(timezone)" if any and parse the date
            $validatedData['rent_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $request->rent_date)
            )->format('Y-m-d');
        } else {
            $validatedData['rent_date'] = null; // Set to null if no valid date is provided
        }

        if (!empty($request->commission_date) && $request->commission_date !== 'null') {
            // Remove extra characters like "(timezone)" if any and parse the date
            $validatedData['commission_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $request->commission_date)
            )->format('Y-m-d');
        } else {
            $validatedData['commission_date'] = null; // Set to null if no valid date is provided
        }

        if (!empty($request->warranty_period) && $request->warranty_period !== 'null') {
            // Remove extra characters like "(timezone)" if any and parse the date
            $validatedData['warranty_period'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $request->warranty_period)
            )->format('Y-m-d');
        } else {
            $validatedData['warranty_period'] = null; // Set to null if no valid date is provided
        }

       $companyId = Factory::find($request->factory_id)?->company_id ?? 0;
       $show_basic_details = $request->show_basic_details == "true" ? true : false;
       $show_specifications = $request->show_specifications == "true" ? true : false;
        
       
       
       // Create the new MachineAssing instance with validated data
        $mechineAssing                      = new MechineAssing($validatedData);
        $mechineAssing->source_id           = $validatedData['source_id'];
        $mechineAssing->company_id          = $companyId;
        $mechineAssing->line_id             = $validatedData['line_id'];
        $mechineAssing->show_basic_details  = $show_basic_details;
        $mechineAssing->show_specifications = $show_specifications;
        // Associate the creator and updater polymorphically
        $mechineAssing->uuid              = HelperController::generateUuid();
        $mechineAssing->status            = "Assign";
        // Similarly handle supplier_id
        $mechineAssing->supplier_id       = $validatedData['supplier_id'];
        $mechineAssing->creator()->associate($creator);
        $mechineAssing->updater()->associate($creator);

        // Generate and save QR code
        // $qrCodeData = $mechineAssing->mechine_code;
        // $qrCodeImage = QrCode::format('png')->size(200)->generate($qrCodeData);
        // $qrCodePath = 'qrcodes/' . uniqid() . '_qrcode.png';
        // Storage::disk('public')->put($qrCodePath, $qrCodeImage);
        // $mechineAssing->qr_code_path = $qrCodePath;

        // Save the MechineAssing record
        $mechineAssing->save();

        // Save data to the Stock table
        // $stock = new MechineStock([
        //     'mechine_assing_id' => $mechineAssing->id,
        //     'quantity'          => 1,
        //     'type'              => "mechine",
        //     'status'            => 'in_stock',  // Adjust status as needed
        // ]);

        // $stock->creator()->associate($creator);
        // $stock->updater()->associate($creator);
        // $stock->save();

        // Return a success response
        return response()->json([
            'success'        => true,
            'message'        => 'Machine Assing created successfully.',
            'mechine_assing' => $mechineAssing
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,$uuid)
    {
        $machineAssign = MechineAssing::with([
          'creator:id,name',                          // Creator of the machine assignment
          'factory:id,name,company_id',
          'factory.user:id,name',                           // Direct factory relationship
          'machineStatus:id,name',                   // Machine status
          'line.unit.floor.factory.user:id,name',
          'machineStatus:id,name',
          'brand:id,name',                 // Machine status
          'productModel:id,name',                    // Product model
          'mechineType:id,name',
          'source:id,name',
          'supplier:id,name',                      // Machine type
        ])->where('uuid', $uuid)->firstOrFail();

        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
        $sortOrder    = $request->input('sortOrder', 'desc');    // Default sort order is descending
        $search       = $request->input('search', '');           // Search term, default is empty

      // If superadmin, retrieve all technicians
      // $machineAssignQuery = MechineAssing::query(); // No filters applied


      //   // Apply search if the search term is not empty
      //   if (!empty($search)) {
      //       $machineAssignQuery->where('name', 'LIKE', '%' . $search . '%');
      //   }
      //   // Apply sorting
      //   $machineAssignQuery->orderBy($sortBy, $sortOrder);
      //   // Paginate results
      //   $history = $machineAssignQuery->where('status','History')
      //   ->where('machine_id',$machineAssign->machine_id)
      //   ->with([
      //     'creator:id,name',                          // Creator of the machine assignment
      //     'factory:id,name,company_id',
      //     'factory.user:id,name',                           // Direct factory relationship
      //     'machineStatus:id,name',                   // Machine status
      //     'line.unit.floor.factory.user:id,name',
      //     'machineStatus:id,name',                   // Machine status
      //     'productModel:id,name',                    // Product model
      //     'mechineType:id,name'                      // Machine type
      //   ])->paginate($itemsPerPage);

        $history = MechineAssing::where('status', 'History')->where('machine_id',$machineAssign->machine_id)->with([
              'creator:id,name',                          // Creator of the machine assignment
              'factory:id,name,company_id',
              'factory.user:id,name',                           // Direct factory relationship
              'machineStatus:id,name',                   // Machine status
              'line.unit.floor.factory.user:id,name',
              'machineStatus:id,name',                   // Machine status
              'productModel:id,name',                    // Product model
              'mechineType:id,name'                      // Machine type
            ])->get();

        if (Auth::guard('admin')->check()) {
          $currentUser = Auth::guard('admin')->user();
          $creatorType = Admin::class;
          // Check if the admin is a super admin
          if ($currentUser->role === 'superadmin') {
              // Super admins can edit any mechineAssing
              return response()->json([
                  'success'          => true,
                  'machineAssign'    => $machineAssign,
                  'items' =>  $history,
              ], Response::HTTP_OK);
          }
      } elseif (Auth::guard('user')->check()) {
          $currentUser = Auth::guard('user')->user();
          $creatorType = User::class;
      } else {
          return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
      }
      // Check if the machineAssign belongs to the current user or admin
      if ($machineAssign->creator_type !== $creatorType || $machineAssign->creator_id !== $currentUser->id) {
          return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this machineAssign.'], 403);
      }

      //  // Return the machineAssign data if authorized
      //  return response()->json([
      //   'success'       => true,
      //   'machineAssign' => $machineAssign,
      //   // 'items'         => $machineAssignHistory, // Current page items
      //   // 'total'         => $machineAssignHistory->total(),
      // ], Response::HTTP_OK);

      // $page         = $request->input('page', 1);
      //   $itemsPerPage = $request->input('itemsPerPage', 5);
      //   $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by created_at
      //   $sortOrder    = $request->input('sortOrder', 'desc');    // Default sort order is descending
      //   $search       = $request->input('search', '');           // Search term, default is empty
      //   // Determine the authenticated user (either from 'admin' or 'user' guard)
      //   if (Auth::guard('admin')->check()) {
      //       $currentUser = Auth::guard('admin')->user();
      //       $creatorType = Admin::class;
      //       // Check if the admin is a super admin
      //       if ($currentUser->role === 'superadmin') {
      //           // If superadmin, retrieve all technicians
      //           $machineAssignQuery = MechineAssing::query(); // No filters applied
      //       } else {
      //           // If not superadmin, filter by creator type and id
      //           $machineAssignQuery = MechineAssing::where('creator_type', $creatorType)
      //               ->where('creator_id', $currentUser->id);
      //       }
      //   } elseif (Auth::guard('user')->check()) {
      //       $currentUser = Auth::guard('user')->user();
      //       $creatorType = User::class;
      //       // For regular users, filter by creator type and id
      //       $machineAssignQuery = MechineAssing::where('creator_type', $creatorType)
      //           ->where('creator_id', $currentUser->id);
      //   } else {
      //       return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
      //   }
      //   // Apply search if the search term is not empty
      //   if (!empty($search)) {
      //       $machineAssignQuery->where('name', 'LIKE', '%' . $search . '%');
      //   }
      //   // Apply sorting
      //   $machineAssignQuery->orderBy($sortBy, $sortOrder);
      //   // Paginate results
      //   $history = $machineAssignQuery->where('status','History')->with([
      //     'creator:id,name',                          // Creator of the machine assignment
      //     'factory:id,name,company_id',
      //     'factory.user:id,name',                           // Direct factory relationship
      //     'machineStatus:id,name',                   // Machine status
      //     'line.unit.floor.factory.user:id,name',
      //     'machineStatus:id,name',                   // Machine status
      //     'productModel:id,name',                    // Product model
      //     'mechineType:id,name'                      // Machine type
      //   ])->paginate($itemsPerPage);
      //   // Return the response as JSON
      //   return response()->json([
      //       'success'       => true,
      //       'machineAssign' => $machineAssign,
      //       'items'         => $history->isEmpty() ? [] : $history->items(), // Current page items
      //       'total' => $history->total(), // Total number of records
      //   ]);
      // $history = MechineAssing::where('machine_id', $machineAssign->id)
      //   ->paginate(10); // Adjust pagination limit as needed

    // return response()->json([
    //     'success' => true,
    //     'machineAssign' => $machineAssign,
    //     'items' => $history->items(), // Current page items
    //     'total' => $history->total(), // Total number of records
    // ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $mechineAssing = MechineAssing::where('uuid', $uuid)->firstOrFail();

        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
        
            // Superadmin can access any MechineAssing
            if ($currentUser->role === 'superadmin') {
                return response()->json([
                    'success' => true,
                    'mechineAssing' => $mechineAssing
                ], Response::HTTP_OK);
            }
        
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        
        } else {
            // Unauthorized access
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        // Check if the MechineAssing belongs to the current user or admin
        if (
            $mechineAssing->creator_type !== $creatorType ||
            $mechineAssing->creator_id !== $currentUser->id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden: You are not authorized to edit this MechineAssing.'
            ], Response::HTTP_FORBIDDEN);
        }
        
        // Return the MechineAssing data if authorized
        return response()->json([
            'success' => true,
            'mechineAssing' => $mechineAssing
        ], Response::HTTP_OK);
    }
    public function mechineTransfer(Request $request, $uuid)
    {
        $mechineTransfer = MechineAssing::where('uuid', $uuid)->firstOrFail();

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any mechineTransfer
                return response()->json([
                    'success' => true,
                    'mechineTransfer' => $mechineTransfer
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if the mechineTransfer belongs to the current user or admin
        if ($mechineTransfer->creator_type !== $creatorType || $mechineTransfer->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this mechineTransfer.'], 403);
        }

        // Validate the location and status settings
        if ($mechineTransfer->location_status == 'Sewing Line' && !$mechineTransfer->line_id) {
            return response()->json(['success' => false, 'message' => 'Line must be selected for Sewing Line location.'], 400);
        }

        // Optionally, you can validate machine status if needed
        if (!$mechineTransfer->machine_status_id) {
            return response()->json(['success' => false, 'message' => 'Machine status is required.'], 400);
        }

        // Transfer machine logic (save updated details)
        // $mechineTransfer->update([
        //     'location_status' => $request->machine['location_status'],
        //     'machine_status_id' => $request->machine['machine_status_id'],
        //     'line_id' => $request->machine['line_id'],
        // ]);

        return response()->json([
            'success' => true,
            'mechineTransfer' => $mechineTransfer
        ], Response::HTTP_OK);


    }

    public function mechineTransferUpdate(MachineAssignStoreRequest $request,$uuid){
        // dd($request->all());
        $machine = MechineAssing::where('uuid', $uuid)->firstOrFail();
        $machineCode = $machine->machine_code;
        $machineCodeData = MechineAssing::where('machine_code', $machine->machine_code)->first();
        $machineId = $machineCodeData->id;
        // dd($machineId);
        // Check which authentication guard is in use and set the creator
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // validation
        $validatedData = $request->validated();
        if (!empty($request->purchase_date) && $request->purchase_date !== 'null') {
            // Remove extra characters like "(timezone)" if any and parse the date
            $validatedData['purchase_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $request->purchase_date)
            )->format('Y-m-d');
        } else {
            $validatedData['purchase_date'] = null; // Set to null if no valid date is provided
        }

        if (!empty($request->rent_date) && $request->rent_date !== 'null') {
            // Remove extra characters like "(timezone)" if any and parse the date
            $validatedData['rent_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $request->rent_date)
            )->format('Y-m-d');
        } else {
            $validatedData['rent_date'] = null; // Set to null if no valid date is provided
        }

        if (!empty($request->commission_date) && $request->commission_date !== 'null') {
          // Remove extra characters like "(timezone)" if any and parse the date
          $validatedData['commission_date'] = Carbon::parse(
              preg_replace('/\s*\(.*\)$/', '', $request->commission_date)
          )->format('Y-m-d');
        } else {
          $validatedData['commission_date'] = null; // Set to null if no valid date is provided
        }

        if (!empty($request->warranty_period) && $request->warranty_period !== 'null') {
            // Remove extra characters like "(timezone)" if any and parse the date
            $validatedData['warranty_period'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $request->warranty_period)
            )->format('Y-m-d');
        } else {
            $validatedData['warranty_period'] = null; // Set to null if no valid date is provided
        }

         // Update the original machine status to "history"


        $mechineTransfer                      = new MechineAssing( $validatedData);
        $mechineTransfer->machine_id          = $machineId;
        $mechineTransfer->uuid                = HelperController::generateUuid();
        $mechineTransfer->source_id           = ($request->source_id && $request->source_id !== 'null') ? $request->source_id : 0;
        $mechineTransfer->line_id             = ($request->line_id && $request->line_id !== 'null') ? $request->line_id : 0;
        $mechineTransfer->show_basic_details  = $request->show_basic_details == "true" ? true : false;
        $mechineTransfer->show_specifications = $request->show_specifications == "true" ? true : false;

        $mechineTransfer->status              = "Transferred";

        $mechineTransfer->creator()->associate($creator);
        $mechineTransfer->updater()->associate($creator);
        $mechineTransfer->save();
        // $mechineTransfer->uuid                      = HelperController::generateUuid();
        // $mechineTransfer->factory_id                = $request->factory_id;
        // $mechineTransfer->brand_id                  = $request->brand_id;
        // $mechineTransfer->model_id                  = $request->model_id;
        // $mechineTransfer->mechine_type_id           = $request->mechine_type_id;
        // $mechineTransfer->mechine_source_id         = $request->mechine_source_id;
        // $mechineTransfer->supplier_id               = ($request->supplier_id && $request->supplier_id !== 'null') ? $request->supplier_id : 0;
        // $mechineTransfer->rent_id                   = ($request->rent_id && $request->rent_id !== 'null') ? $request->rent_id : 0;
        // $mechineTransfer->rent_date                 = $request->rent_date;
        // $mechineTransfer->name                      = $request->name;
        // $mechineTransfer->mechine_code              = $request->mechine_code;
        // $mechineTransfer->preventive_service_days   = $request->preventive_service_days;
        // $mechineTransfer->purchace_price            = $request->purchace_price;
        // $mechineTransfer->purchase_date             = $request->purchase_date;
        // $mechineTransfer->status                    = $request->status;
        // $mechineTransfer->note                      = $request->note;
        // $mechineTransfer->mechine_transfer_id       = $mechine->id;


        $machine->update(['status' => 'History']);

        return response()->json([
            'success' => true,
            'message' => 'Mechine Transfer  successfully.',
        ], 200);


    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $mechineAssing = MechineAssing::where("uuid", $uuid)->first();

        if (!$mechineAssing) {
            return response()->json(['success' => false, 'message' => 'Mechine Assing not found.'], 404);
        }
        
        // Determine the updater based on the authentication guard
        if (Auth::guard('admin')->check()) {
            $updater = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $updater = Auth::guard('user')->user();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        // Validate the incoming request data
        $validatedData = $request->validate([
            'factory_id'              => 'required|exists:factories,id',
            'brand_id'                => 'required|exists:brands,id',
            'product_model_id'        => 'required|exists:product_models,id',
            'machine_type_id'         => 'required|exists:product_models,id',
            'source_id'               => 'nullable|exists:sources,id',
            'supplier_id'             => 'nullable|exists:suppliers,id',
            'name'                    => 'required|string|max:255',
            'machine_code'            => 'required|string|max:255',
            'partial_maintenance_day' => 'nullable',
            'purchace_price'          => 'nullable|numeric',
            'purchase_date'           => 'nullable|date',
            'rent_date'               => 'nullable|date',
            'rent_note'               => 'nullable|string',
            'mechine_status'          => 'nullable|string',
        ]);
        
        // Process dates to handle timezone issues and format them properly
        if (!empty($request->purchase_date) && $request->purchase_date !== 'null') {
            $validatedData['purchase_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $request->purchase_date)
            )->format('Y-m-d');
        } else {
            $validatedData['purchase_date'] = null;
        }
        
        if (!empty($request->rent_date) && $request->rent_date !== 'null') {
            $validatedData['rent_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $request->rent_date)
            )->format('Y-m-d');
        } else {
            $validatedData['rent_date'] = null;
        }
        $companyId = Factory::find($request->factory_id)?->company_id ?? 0;

        // Update the MechineAssing instance with validated data
        $mechineAssing->fill($validatedData);
        $mechineAssing->supplier_id = $validatedData['supplier_id']; 
        $mechineAssing->source_id           = $validatedData['source_id'];
        $mechineAssing->company_id          = $companyId;
        // Associate the updater polymorphically
        $mechineAssing->updater()->associate($updater);
        
        // Save the updated MechineAssing record and return the response
        if ($mechineAssing->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Mechine Assing updated successfully.',
                'mechine_assing' => $mechineAssing,
            ], 200);
        }
        
        // Return an error response if save fails
        return response()->json(['success' => false, 'message' => 'Failed to update Mechine Assing.'], 500);
        
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($uuid)
    {
       // Fetch the machine assignment record
       $machineAssign = MechineAssing::where('uuid', $uuid)->first();
        //return response()->json($machineAssign,200);
       // Check if the machine assignment exists
       if (!$machineAssign) {
           return response()->json([
               'success' => false,
               'message' => 'Machine assignment not found.'
           ], 404);
       }

       // Authorization check for admin
       if (Auth::guard('admin')->check()) {
           $currentUser = Auth::guard('admin')->user();

           // Superadmin can delete any machine assignment without additional checks
           if ($currentUser->role !== 'superadmin') {
               if ($machineAssign->creator_type !== Admin::class || $machineAssign->creator_id !== $currentUser->id) {
                   return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this machine assignment.'], 403);
               }
           }
       }
       // Authorization check for user
       elseif (Auth::guard('user')->check()) {
           $currentUser = Auth::guard('user')->user();

           if ($machineAssign->creator_type !== User::class || $machineAssign->creator_id !== $currentUser->id) {
               return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this machine assignment.'], 403);
           }
       } else {
           return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
       }

       // Delete related movements and the machine assignment
       //$machineAssign->movements()->delete();
       $machineAssign->delete();

       return response()->json([
           'success' => true,
           'message' => 'Machine assignment deleted successfully.'
       ], Response::HTTP_OK);
    }
    
    public function mechineTrashedCount()
    {
         // Get the count of soft-deleted brands
        $trashedCount = MechineAssing::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }

    public function mechineAssingTrashed(Request $request)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all soft-deleted technicians
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed technicians without additional checks
                $mechinsQuery = MechineAssing::onlyTrashed();
            } else {
                // Regular admin authorization check
                $mechinsQuery = MechineAssing::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            $mechinsQuery = MechineAssing::onlyTrashed()
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
        $search       = $request->input('search', ''); // Search term, default is empty
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $mechinsQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your brand fields
        }
        // Apply sorting
        $mechinsQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $mechins = $mechinsQuery->where('status','Assign')
        ->with([
            'creator',                          // Creator of the machine assignment
            'factory.company',
            'machineStatus',                   // Machine status
            'line.unit.floor.factory',
            'machineStatus',                   // Machine status
            'productModel',                    // Product model
            'mechineType'                      // Machine type
        ])
        ->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $mechins->items(), // Current page items
            'total' => $mechins->total(), // Total number of trashed records
        ]);
    }

    public function mechineAssingRestore($uuid)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        $query = MechineAssing::onlyTrashed()->where('uuid', $uuid);
    
        // Check for authenticated admin
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
    
            // Superadmin: Restore any trashed machine
            if ($currentUser->role === 'superadmin') {
                $restored = $query->restore();
            } else {
                // Regular admin: Restore based on creator ID and type
                $restored = $query->where('creator_id', $currentUser->id)
                                  ->where('creator_type', $creatorType)
                                  ->restore();
            }
    
        // Check for authenticated user
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
    
            // Regular user: Restore based on creator ID and type
            $restored = $query->where('creator_id', $currentUser->id)
                              ->where('creator_type', $creatorType)
                              ->restore();
            
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    
        // Check if any records were restored
        return $restored > 0 
            ? response()->json(['message' => 'Mechine assing restored successfully'], Response::HTTP_OK)
            : response()->json(['message' => 'Mechine assing not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }
    
    

    public function mechineAssingforceDelete($uuid)
    {
        // Query the trashed MechineAssing once
        $mechineAssingQuery = MechineAssing::onlyTrashed()->where('uuid', $uuid);
    
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
    
            // Superadmin check: Allow access to all trashed machine assignments
            if ($currentUser->role === 'superadmin') {
                $mechineAssing = $mechineAssingQuery->firstOrFail();
            } else {
                // Regular admin authorization check
                $mechineAssing = $mechineAssingQuery
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->firstOrFail();
            }
    
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
    
            // Regular user authorization check
            $mechineAssing = $mechineAssingQuery
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->firstOrFail();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
    
        try {
            // Force delete the machine assignment
            $mechineAssing->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Machine assignment permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting machine assignment: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    public function getFactories(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $factories = Factory::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the factories as JSON
        return response()->json($factories);
    }

    public function generateMachineCode()
    {
        // Fetch the prefix from the settings table
        $prefix = GeneralSetting::where('key', 'machine_code_prefix')->value('value') ?? 'OZ-'; // Default to 'OZ-' if not found

        // Get the last machine assigned with the highest code
        $lastMachine = MechineAssing::orderBy('machine_code', 'desc')->first();

        // Determine the next numeric part of the machine code
        if ($lastMachine) {
            // Remove the prefix and convert the numeric part to an integer
            $lastCode = intval(str_replace($prefix, '', $lastMachine->machine_code));
            $nextCode = $lastCode + 1;
        } else {
            // If no machine code exists, start from 1
            $nextCode = 1;
        }

        // Format the new machine code with the prefix and six-digit padding
        $machineCode = $prefix . str_pad($nextCode, 8, '0', STR_PAD_LEFT);

        // Return the generated code as JSON
        return response()->json(['machine_code' => $machineCode]);
    }
    public function getTypes(Request $request){

       // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');  // Default to empty string if no search term
        $limit  = $request->query('limit', 5);    // Default limit is 5, can be changed to 10 if needed

        // Query to search for types by name with a limit
        $types = MechineType::where('name', 'like', '%' . $search . '%') // Filter by search term
                    ->limit($limit)   // Apply limit
                    ->get();          // Get results

        // Return the types as JSON
        return response()->json($types,Response::HTTP_OK);
    }
    public function getSources(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for source by name with a limit
        $source  = Source::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the source as JSON
        return response()->json($source);
    }
    public function getSuppliers(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for suppliers by name with a limit
        $suppliers  = ProductModel::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the suppliers as JSON
        return response()->json($suppliers);
    }
    public function getRents(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for rents by name with a limit
        $rents  = Rent::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the rents as JSON
        return response()->json($rents);
    }
    public function machinegetStatus(Request $request){
        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for factories by name with a limit
        $status = MachineStatus::where('name', 'like', '%' . $search . '%')
                    ->limit($limit)
                    ->get();
        // Return the factories as JSON
        return response()->json($status);
    }
}