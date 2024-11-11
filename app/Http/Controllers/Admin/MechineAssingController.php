<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Requests\MechineTransferStore;
use App\Models\Admin;
use App\Models\MechineAssing;
use App\Models\Brand;
use App\Models\Factory;
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
        $mechineAssing = $mechineAssingQuery->where('mechine_status','Assing')->with('creator:id,name','user:id,name','factory:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $mechineAssing->items(), // Current page items
            'total' => $mechineAssing->total(), // Total number of records
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
    public function mechineHistoryList(Request $request)
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
        $mechineAssing = $mechineAssingQuery->where('mechine_status','History')->with('creator:id,name','user:id,name','factory:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $mechineAssing->items(), // Current page items
            'total' => $mechineAssing->total(), // Total number of records
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
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'company_id'              => 'required|integer',
            'factory_id'              => 'required|integer',
            'brand_id'                => 'required|integer',
            'model_id'                => 'required|integer',
            'mechine_type_id'         => 'required|integer',
            'mechine_source_id'       => 'required|integer',
            'supplier_id'             => 'nullable',
            'rent_id'                 => 'nullable|integer',
            'rent_date'               => 'nullable',
            'name'                    => 'required|string|max:255',
            'mechine_code'            => 'required|string|max:255',
            'serial_number'           => 'nullable|string|max:255',
            'preventive_service_days' => 'nullable',
            'purchace_price'          => 'nullable|numeric',
            'purchase_date'           => 'nullable',
            'status'                  => 'nullable',  // Example: assumes "status" has specific values
            'note'                    => 'nullable|string',
            'mechine_status'          => 'nullable',
        ]);

        // Process dates to handle timezone issues and format them properly
        if (isset($validatedData['purchase_date'])) {
            $validatedData['purchase_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $validatedData['purchase_date'])
            )->format('Y-m-d');
        }

        if (isset($validatedData['rent_date'])) {
            $validatedData['rent_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $validatedData['rent_date'])
            )->format('Y-m-d');
        }

        // Create the new MechineAssing instance with validated data
        $mechineAssing       = new MechineAssing($validatedData);
        // Associate the creator and updater polymorphically
        $mechineAssing->uuid = HelperController::generateUuid();
        $mechineAssing->mechine_status = "Assing";
        $mechineAssing->creator()->associate($creator);
        $mechineAssing->updater()->associate($creator);

        // Save the MechineAssing record
        $mechineAssing->save();

        // Save data to the Stock table
        $stock = new MechineStock([
            'mechine_assing_id' => $mechineAssing->id,
            'quantity'          => 1,
            'type'              => "mechine",
            'status'            => 'in_stock',  // Adjust status as needed
        ]);

        $stock->creator()->associate($creator);
        $stock->updater()->associate($creator);
        $stock->save();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'MechineAssing created successfully.',
            'mechine_assing' => $mechineAssing
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(MechineAssing $mechineAssing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MechineAssing $mechineAssing)
    {
        //
    }
    public function mechineTransfer($uuid){

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
        // Return the mechineTransfer data if authorized
        return response()->json([
            'success' => true,
            'mechineTransfer' => $mechineTransfer
        ], Response::HTTP_OK);
    }

    public function mechineTransferStore(MechineTransferStore $request,$uuid){
        // dd($request->all());
        $mechine = MechineAssing::where('uuid', $uuid)->firstOrFail();
        // dd($mechine);
        // Check which authentication guard is in use and set the creator
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // validataion
        $validatedData = $request->validated();
        if (isset($validatedData['purchase_date'])) {
            $validatedData['purchase_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $validatedData['purchase_date'])
            )->format('Y-m-d');
        }

        if (isset($validatedData['rent_date'])) {
            $validatedData['rent_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $validatedData['rent_date'])
            )->format('Y-m-d');
        }
         // Update the original machine status to "history"


        $mechineTransfer                            = new MechineAssing();
        $mechineTransfer->uuid                      = HelperController::generateUuid();
        $mechineTransfer->company_id                = $request->company_id;
        $mechineTransfer->factory_id                = $request->factory_id;
        $mechineTransfer->brand_id                  = $request->brand_id;
        $mechineTransfer->model_id                  = $request->model_id;
        $mechineTransfer->mechine_type_id           = $request->mechine_type_id;
        $mechineTransfer->mechine_source_id         = $request->mechine_source_id;
        $mechineTransfer->supplier_id               = $request->supplier_id;
        $mechineTransfer->rent_id                   = $request->rent_id;
        $mechineTransfer->rent_date                 = $request->rent_date;
        $mechineTransfer->name                      = $request->name;
        $mechineTransfer->mechine_code              = $request->mechine_code;
        $mechineTransfer->preventive_service_days   = $request->preventive_service_days;
        $mechineTransfer->purchace_price            = $request->purchace_price;
        $mechineTransfer->purchase_date             = $request->purchase_date;
        $mechineTransfer->status                    = $request->status;
        $mechineTransfer->note                      = $request->note;
        $mechineTransfer->mechine_transfer_id       = $mechine->id;
        $mechineTransfer->mechine_status            = "Transferred";

        $mechineTransfer->creator()->associate($creator);
        $mechineTransfer->updater()->associate($creator);
        $mechineTransfer->save();

        $mechine->update(['mechine_status' => 'History']);

        return response()->json([
            'success' => true,
            'message' => 'Mechine Transfer  successfully.',
        ], 200);


    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MechineAssing $mechineAssing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MechineAssing $mechineAssing)
    {
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any brand without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($mechineAssing->creator_type !== $creatorType || $mechineAssing->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this brand.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($mechineAssing->creator_type !== $creatorType || $mechineAssing->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this brand.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $mechineAssing->delete();
            return response()->json([
                'success' => true,
                'message' => 'mechine assing deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting mechine assing: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
        $mechins = $mechinsQuery->with('creator:id,name','user:id,name','factory:id,name')->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $mechins->items(), // Current page items
            'total' => $mechins->total(), // Total number of trashed records
        ]);
    }

    public function mechineAssingRestore($id)
    {

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $restored = MechineAssing::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = MechineAssing::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = MechineAssing::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if ($restored) {
            return response()->json(['message' => 'mechine assing restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'mechine assing not found or is not trashed'], Response::HTTP_NOT_FOUND);

        // Attempt to restore the brand using the static method on the model
        $mechineAssingRestored = MechineAssing::onlyTrashed()->find($id);

        if ($mechineAssingRestored) {
            $mechineAssingRestored->restore();
            return response()->json(['message' => 'mechine assing restored successfully'], 200);
        }

        return response()->json(['message' => 'mechine assing not found or is not trashed'], 404);
    }

    public function mechineAssingforceDelete($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {

            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $mechineAssing = MechineAssing::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $mechineAssing = MechineAssing::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

            } elseif (Auth::guard('user')->check()) {
                $currentUser = Auth::guard('user')->user();
                $creatorType = User::class;

                // Regular user authorization check
                $mechineAssing = MechineAssing::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            } else {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    try {
        // Delete the supplier
        $mechineAssing->forceDelete();
        return response()->json([
            'success' => true,
            'message' => 'mechineAssing permanently deleted successfully.'
        ], Response::HTTP_OK);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error deleting Brand: ' . $e->getMessage()
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

        return response()->json(['message' => 'mechineAssing permanently deleted']);
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
    public function getBrands(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for brands by name with a limit
        $brands  = Brand::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the brands as JSON
        return response()->json($brands);
    }
    public function getModels(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for models by name with a limit
        $models  = ProductModel::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the models as JSON
        return response()->json($models);
    }
    public function getTypes(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for types by name with a limit
        $types  = MechineType::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the types as JSON
        return response()->json($types);
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
}