<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Http\Requests\ParseStoreRequest;
use App\Http\Requests\ParseUpdateRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Parse;
use App\Models\ParseUnit;
use App\Models\ProductModel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Admin;
use App\Models\ParseStockIn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ParseController extends Controller
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
                $parseQuery = Parse::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $parseQuery = Parse::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $parseQuery = Parse::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $parseQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $parseQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $parse = $parseQuery->with('creator:id,name','user:id,name','factory:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $parse->items(), // Current page items
            'total' => $parse->total(), // Total number of records
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
    // public function store(ParseStoreRequest $request)
    // {
    //     // Check which authentication guard is in use and set the creator
    //     if (Auth::guard('admin')->check()) {
    //         $creator = Auth::guard('admin')->user();
    //     } elseif (Auth::guard('user')->check()) {
    //         $creator = Auth::guard('user')->user();
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    //     }

    //     // Validate the incoming request data
    //     $validatedData =$request->validated();
    //     // Process dates to handle timezone issues and format them properly
    //     if (isset($validatedData['purchase_date'])) {
    //         $validatedData['purchase_date'] = Carbon::parse(
    //             preg_replace('/\s*\(.*\)$/', '', $validatedData['purchase_date'])
    //         )->format('Y-m-d');
    //     }

    //     // Create the new parse instance with validated data
    //     $parse       = new Parse($validatedData);
    //     // Associate the creator and updater polymorphically
    //     $parse->uuid = HelperController::generateUuid();
    //     $parse->creator()->associate($creator);
    //     $parse->updater()->associate($creator);
    //     // Save the parse record
    //     $parse->save();

    //     // Save data to the Stock table
    //     $stockIn = new ParseStockIn([
    //         'parse_id'          => $parse->id,
    //         'quantity'          => $parse->quantity,
    //         'type'              => "parse",
    //     ]);

    //     $stockIn->creator()->associate($creator);
    //     $stockIn->updater()->associate($creator);
    //     $stockIn->save();

    //     // Return a success response
    //     return response()->json([
    //         'success'        => true,
    //         'message'        => 'parse created successfully.',
    //         'mechine_assing' => $parse
    //     ], 200);
    // }

    public function store(ParseStoreRequest $request)
    {
        // Determine the creator based on the authentication guard
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Validate the incoming request data
        $validatedData = $request->validated();
        // Process dates for timezone and format issues
        if (isset($validatedData['purchase_date'])) {
            $validatedData['purchase_date'] = Carbon::parse(
                preg_replace('/\s*\(.*\)$/', '', $validatedData['purchase_date'])
            )->format('Y-m-d');
        }
        // Wrap the creation in a transaction to ensure atomicity
        DB::beginTransaction();
        try {
            // Create the new parse instance with validated data
            $parse = new Parse($validatedData);
            $parse->uuid = HelperController::generateUuid();
            $parse->creator()->associate($creator);
            $parse->updater()->associate($creator);
            $parse->save();
            // Save data to the Stock table
            $stockIn = new ParseStockIn([
                'parse_id'    => $parse->id,
                'quantity_in' => $parse->quantity,
                'type'        => "Parse",
            ]);

            $stockIn->creator()->associate($creator);
            $stockIn->updater()->associate($creator);
            $stockIn->save();

            DB::commit();
            // Return success response
            return response()->json([
                'success'        => true,
                'message'        => 'Parse created successfully.',
                'mechine_assing' => $parse
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create parse.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Parse $parse)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        $parse = Parse::where('uuid', $uuid)->firstOrFail();
         if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any parse
                return response()->json([
                    'success' => true,
                    'parse'    => $parse
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the parse belongs to the current user or admin
        if ($parse->creator_type !== $creatorType || $parse->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this parse.'], 403);
        }
        // Return the parse data if authorized
        return response()->json([
            'success' => true,
            'parse'   => $parse
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(ParseUpdateRequest $request,$uuid)
    // {
    //     // Retrieve the parse model by uuid
    //     $parse = Parse::where('uuid', $uuid)->firstOrFail();
    //      // Validate the incoming request data
    //     $validatedData =$request->validated();

    //      // Determine the authenticated user (either from 'admin' or 'user' guard)
    //     if (Auth::guard('admin')->check()) {
    //         $currentUser = Auth::guard('admin')->user();
    //         $creatorType = Admin::class;

    //         // Check if the admin is a superadmin
    //         if ($currentUser->role === 'superadmin') {
    //             // Superadmin can update without additional checks
    //         } else {
    //             // Regular admin authorization check
    //             if ($parse->creator_type !== $creatorType || $parse->creator_id !== $currentUser->id) {
    //                 return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this parse.'], 403);
    //             }
    //         }

    //     } elseif (Auth::guard('user')->check()) {
    //         $currentUser = Auth::guard('user')->user();
    //         $creatorType = User::class;
    //         // Regular user authorization check
    //         if ($parse->creator_type !== $creatorType || $parse->creator_id !== $currentUser->id) {
    //             return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this parse.'], 403);
    //         }
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    //     }
    //     // Update the parse's details
    //     $parse->fill($validatedData);
    //     $parse->updater()->associate($currentUser); // Associate the updater
    //     $parse->save();
    //     // Return a success response
    //     return response()->json(['success' => true, 'message' => 'parse updated successfully.', 'parse' => $parse], 200);
    // }


    public function update(ParseUpdateRequest $request, $uuid)
    {
        // Retrieve the parse model by uuid
        $parse = Parse::where('uuid', $uuid)->firstOrFail();
        // Validate the incoming request data
        $validatedData = $request->validated();

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a superadmin
            if ($currentUser->role !== 'superadmin') {
                // Regular admin authorization check
                if ($parse->creator_type !== $creatorType || $parse->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this parse.'], 403);
                }
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            if ($parse->creator_type !== $creatorType || $parse->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this parse.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Begin transaction to ensure both Parse and ParseStockIn updates are atomic
        DB::beginTransaction();
        try {
            // Update the parse's details
            $parse->fill($validatedData);
            $parse->updater()->associate($currentUser); // Associate the updater
            $parse->save();
            // Update associated stock if needed
            $stockIn = ParseStockIn::where('parse_id', $parse->id)->first();
            if ($stockIn) {
                $stockIn->quantity_in = $validatedData['quantity'] ?? $stockIn->quantity; // Update quantity if provided
                $stockIn->updater()->associate($currentUser); // Associate the updater
                $stockIn->save();
            }

            DB::commit();

            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Parse updated successfully.',
                'parse'   => $parse,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update parse.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parse $parse)
    {
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any brand without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($parse->creator_type !== $creatorType || $parse->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this brand.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($parse->creator_type !== $creatorType || $parse->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this brand.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $parse->delete();
            return response()->json([
                'success' => true,
                'message' => 'parse deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting parse: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function trashedParseCount()
    {
         // Get the count of soft-deleted brands
        $trashedCount = Parse::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }

    public function trashed(Request $request)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all soft-deleted technicians
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed technicians without additional checks
                $mechinsQuery = Parse::onlyTrashed();
            } else {
                // Regular admin authorization check
                $mechinsQuery = Parse::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            $mechinsQuery = Parse::onlyTrashed()
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

    public function restore($id)
    {

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $restored = Parse::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = Parse::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = Parse::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if ($restored) {
            return response()->json(['message' => 'parse restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'parse not found or is not trashed'], Response::HTTP_NOT_FOUND);

        // Attempt to restore the brand using the static method on the model
        $parseRestored = Parse::onlyTrashed()->find($id);

        if ($parseRestored) {
            $parseRestored->restore();
            return response()->json(['message' => 'parse restored successfully'], 200);
        }

        return response()->json(['message' => 'parse not found or is not trashed'], 404);
    }

    public function forceDelete($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {

            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $parse = Parse::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $parse = Parse::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

        } elseif (Auth::guard('user')->check()) {
                $currentUser = Auth::guard('user')->user();
                $creatorType = User::class;

                // Regular user authorization check
                $parse = Parse::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            } else {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            try {
                ParseStockIn::where('parse_id', $parse->id)->delete();
                // Delete the supplier
                $parse->forceDelete();
                return response()->json([
                    'success' => true,
                    'message' => 'parse permanently deleted successfully.'
                ], Response::HTTP_OK);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error deleting Brand: ' . $e->getMessage()
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response()->json(['message' => 'parse permanently deleted']);
    }

    public function getCategory(Request $request){
        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for category by name with a limit
        $category  = Category::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the category as JSON
        return response()->json($category);
    }
    public function getSuppliers(Request $request){
        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for suppliers by name with a limit
        $suppliers  = ProductModel::where('name', 'like', '%' . $search . '%')
                     ->where('type','Parse')
                     ->limit($limit)
                     ->get();
        // Return the suppliers as JSON
        return response()->json($suppliers);
    }

    public function getModels(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for models by name with a limit
        $models  = ProductModel::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->where('type','Parse')
                     ->get();
        // Return the models as JSON
        return response()->json($models);
    }
    public function getBrands(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for brands by name with a limit
        $brands  = Brand::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->where('type','Parse')
                     ->get();
        // Return the brands as JSON
        return response()->json($brands);
    }
    public function getParseUnit(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for parseUnit by name with a limit
        $parseUnit  = ParseUnit::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the parseUnit as JSON
        return response()->json($parseUnit);
    }

}