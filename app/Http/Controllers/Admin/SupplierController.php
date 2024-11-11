<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\HelperController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
  

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
                $suppliersQuery = Supplier::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $suppliersQuery = Supplier::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $suppliersQuery = Supplier::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $suppliersQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $suppliersQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $suppliers = $suppliersQuery->with('creator:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'suppliers' => $suppliers->items(), // Current page items
            'total' => $suppliers->total(), // Total number of records
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
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:suppliers',
            'phone'          => 'required|string',
            'contact_person' => 'nullable|string|max:255',
            'address'        => 'nullable|string',
            'description'    => 'nullable|string',
            'imageFile'      => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


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

        $name       = $request->name;
        $nameResize = str_replace(" ","", $name);
        $http       = "http://" . $_SERVER['HTTP_HOST'] . "/";

        if ($request->file("imageFile")) {
            $img         = $request->file("imageFile");
            $imgPathName = $img->getClientOriginalName();
            $ExplodeImg  = explode(".", $imgPathName);
            $endImg      = end($ExplodeImg);
            $RandomPath  = $nameResize.'Img'. rand(5,150) . "." . $endImg;
            $uploadImg   = $http . "Supplier/" . $RandomPath;
           $img->move(public_path("Supplier/"), $RandomPath);
        }else{
            $uploadImg = null;
        }

        $supplier                 = new Supplier();
        $supplier->uuid           = HelperController::generateUuid();
        $supplier->name           = $name;
        $supplier->email          = $request->email;
        $supplier->phone          = $request->contact_person;
        $supplier->contact_person = $request->phone;
        $supplier->address        = $request->address;
        $supplier->description    = $request->description;
        $supplier->photo          = $uploadImg;

        $supplier->creator()->associate($creator);  // Assign creator polymorphically
        $supplier->updater()->associate($creator);  // Associate the updater
        $supplier->save();
        return response()->json(['success' => true, 'supplier' => $supplier], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return response()->json([
            'supplier' => $supplier
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($uuid)
    {
        
        $supplier = Supplier::where('uuid', $uuid)->firstOrFail();
       
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any brand
                return response()->json([
                    'success' => true,
                    'brand' => $supplier
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the brand belongs to the current user or admin
        if ($supplier->creator_type !== $creatorType || $supplier->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this brand.'], 403);
        }
        dd($supplier);
        // Return the brand data if authorized
        return response()->json([
            'success'    => true,
            'supplier'   => $supplier
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$uuid)
    {
        $supplier = Supplier::where('uuid', $uuid)->firstOrFail();
         //Validate the incoming data
        $validatedData = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone'          => 'required|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'address'        => 'nullable|string',
            'description'    => 'nullable|string',
            'imageFile'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional validation for photo
        ]);

         // Determine the authenticated user (either from 'admin' or 'user' guard)
         if (Auth::guard('admin')->check()) {
             $currentUser = Auth::guard('admin')->user();
             $creatorType = Admin::class;

             // Check if the admin is a superadmin
             if ($currentUser->role === 'superadmin') {
                 // Superadmin can update without additional checks
             } else {
                 // Regular admin authorization check
                 if ($supplier->creator_type !== $creatorType || $supplier->creator_id !== $currentUser->id) {
                     return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this supplier.'], 403);
                 }
             }

         } elseif (Auth::guard('user')->check()) {
             $currentUser = Auth::guard('user')->user();
             $creatorType = User::class;

             // Regular user authorization check
             if ($supplier->creator_type !== $creatorType || $supplier->creator_id !== $currentUser->id) {
                 return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this supplier.'], 403);
             }
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }

        $name       = $request->name;
        $http       = "http://" . $_SERVER['HTTP_HOST'] . "/";
        $nameResize = str_replace(" ","", $name);

        // Rent Image Updated
        if ($request->file("imageFile")) {
            $img         = $request->file("imageFile");
            $imgPathName = $img->getClientOriginalName();
            $ExplodeImg  = explode(".", $imgPathName);
            $endImg      = end($ExplodeImg);
            $RandomPath  = $nameResize.'Img'. rand(5,150) . "." . $endImg;
            $uploadImg   = $http . "Supplier/" . $RandomPath;
            $img->move(public_path("Supplier/"), $RandomPath);

            // old image delete system
             $oldImg           = $request->oldImg;
             $explodeOldImg    = explode("/", $oldImg);
             $endOldImg        = end($explodeOldImg);
             $deletePublicPath = public_path("Supplier/".$endOldImg);
             if(File::exists($deletePublicPath)){
                
                File::delete($deletePublicPath);
             }
        }else{
            $uploadImg = $request->oldImg;
        }
        // Update the Rent Date
        $supplier->name           = $name;
        $supplier->email          = $request->email;
        $supplier->phone          = $request->phone;
        $supplier->contact_person = $request->contact_person;
        $supplier->address        = $request->address;
        $supplier->description    = $request->description;
        $supplier->photo          = $uploadImg;
        $supplier->updater()->associate($currentUser); // Associate the updater
        $supplier->update();


         // Return a success response
        return response()->json(['success' => true, 'message' => 'Supplier updated successfully.', 'supplier' => $supplier], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
         if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any brand without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($supplier->creator_type !== $creatorType || $supplier->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this supplier.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($supplier->creator_type !== $creatorType || $supplier->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this supplier.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        
        try {
            // Delete the supplier from the database
            $supplier->delete();
            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully.'
            ]);
        } catch (\Exception $e) {
            // Handle any errors that may occur
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the supplier.',
                'error' => $e->getMessage(), // Optional: log the actual error message
            ], 500);
        }
    }

    public function suppliertrashedcount()
    {
        // Get the count of soft-deleted rents
        $trashedCount = Supplier::onlyTrashed()->count();
        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }

    public function suppliertrashed(Request $request)
    {

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all soft-deleted technicians
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed technicians without additional checks
                $suppliersQuery = Supplier::onlyTrashed();
            } else {
                // Regular admin authorization check
                $suppliersQuery = Supplier::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $suppliersQuery = Supplier::onlyTrashed()
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
            $suppliersQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your brand fields
        }
        // Apply sorting
        $suppliersQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $suppliers = $suppliersQuery->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $suppliers->items(), // Current page items
            'total' => $suppliers->total(), // Total number of trashed records
        ]);
    }
    // Restore a soft-deleted rents
     public function supplierrestore($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $restored = Supplier::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = Supplier::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = Supplier::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if ($restored) {
            return response()->json(['message' => 'Supplier restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Supplier not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }
    // Permanently delete a rent from trash
     public function supplierforcedelete($id)
     {

         // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
                
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $supplier = Supplier::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $supplier = Supplier::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

            } elseif (Auth::guard('user')->check()) {
                $currentUser = Auth::guard('user')->user();
                $creatorType = User::class;

                // Regular user authorization check
                $supplier = Supplier::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
             // //rent image check and delete
            if ($supplier->photo) {
                $img        = $supplier->photo;
                $explodeImg = explode("/", $img);
                $EndImg     = end($explodeImg);
                $deletePath = public_path("Rents/" .$EndImg);
                
                if (File::exists($deletePath)) {
                    File::delete($deletePath);
                }  
            }
            // Delete the supplier
            $supplier->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Supplier permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Supplier: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

     }

}
