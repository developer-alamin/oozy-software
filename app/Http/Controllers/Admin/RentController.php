<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rent;
use App\Models\Admin;
use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Auth;

class RentController extends Controller
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
                $RentsQuery = Rent::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $RentsQuery = Rent::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $RentsQuery = Rent::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $RentsQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $RentsQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $Rents = $RentsQuery->with('creator:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $Rents->items(), // Current page items
            'total' => $Rents->total(), // Total number of records
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

       // return Auth::guard('admin')->user();


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

    
        $name = $request->name;
        $nameResize = str_replace(" ","", $name);
        $http = "http://" . $_SERVER['HTTP_HOST'] . "/";

        if ($request->file("imageFile")) {
            $img = $request->file("imageFile");
            $imgPathName = $img->getClientOriginalName();
            $ExplodeImg = explode(".", $imgPathName);
            $endImg = end($ExplodeImg);
            $RandomPath = $nameResize.'Img'. rand(5,150) . "." . $endImg;
            $uploadImg = $http . "Rents/" . $RandomPath;
           $img->move(public_path("Rents/"), $RandomPath);
        }

         $validatedData = $request->validate(Rent::validationRules());
    

        $rent = new Rent();
        $rent->name = $name;
        $rent->email = $request->email;
        $rent->phone = $request->phone;
        $rent->photo = $uploadImg;
        $rent->address = $request->address;
        $rent->description = $request->description;

        $rent->creator()->associate($creator);  // Assign creator polymorphically
        $rent->updater()->associate($creator);  // Associate the updater
        $rent->save(); // Save the technician to the database
         // Return a success response
        return response()->json(['success' => true, 'message' => 'Brand created successfully.'], 201);

        return response()->json(['success' => true,'message' => 'Rent created successfully.'], 200);
       
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
    public function edit(Rent $rent)
    {

         if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any brand
                return response()->json([
                    'success' => true,
                    'rent' => $rent
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the rent belongs to the current user or admin
        if ($rent->creator_type !== $creatorType || $rent->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this rent.'], 403);
        }
        // Return the rent data if authorized
        return response()->json([
            'success' => true,
            'rent'   => $rent
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Rent $rent)
    {

        // Validate the incoming request data
         $validatedData = $request->validate(Rent::validationRules());

         // Determine the authenticated user (either from 'admin' or 'user' guard)
         if (Auth::guard('admin')->check()) {
             $currentUser = Auth::guard('admin')->user();
             $creatorType = Admin::class;

             // Check if the admin is a superadmin
             if ($currentUser->role === 'superadmin') {
                 // Superadmin can update without additional checks
             } else {
                 // Regular admin authorization check
                 if ($rent->creator_type !== $creatorType || $rent->creator_id !== $currentUser->id) {
                     return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this rent.'], 403);
                 }
             }

         } elseif (Auth::guard('user')->check()) {
             $currentUser = Auth::guard('user')->user();
             $creatorType = User::class;

             // Regular user authorization check
             if ($rent->creator_type !== $creatorType || $rent->creator_id !== $currentUser->id) {
                 return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this rent.'], 403);
             }
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }

         $name = $request->name;
        $http = "http://" . $_SERVER['HTTP_HOST'] . "/";
        $nameResize = str_replace(" ","", $name);

        // Rent Image Updated
        if ($request->file("imageFile")) {
            $img = $request->file("imageFile");
            $imgPathName = $img->getClientOriginalName();
            $ExplodeImg = explode(".", $imgPathName);
            $endImg = end($ExplodeImg);
            $RandomPath = $nameResize.'Img'. rand(5,150) . "." . $endImg;
            $uploadImg = $http . "Rents/" . $RandomPath;
            $img->move(public_path("Rents/"), $RandomPath);

            // old image delete system
             $oldImg = $request->oldImg;
             $explodeOldImg = explode("/", $oldImg);
             $endOldImg = end($explodeOldImg);
             $deletePublicPath = public_path("Rents/".$endOldImg);
             if(File::exists($deletePublicPath)){
                
                File::delete($deletePublicPath);
             }
        }else{
            $uploadImg = $request->oldImg;
        }
        // Update the Rent Date
        $rent->name = $name;
        $rent->email = $request->email;
        $rent->phone = $request->phone;
        $rent->photo = $uploadImg;
        $rent->address = $request->address;
        $rent->description = $request->description;
        $rent->updater()->associate($currentUser); // Associate the updater
        $rent->update();

        // // Return a success response
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Rent updated successfully.',
        // ]);

         // Return a success response
         return response()->json(['success' => true, 'message' => 'Rent updated successfully.', 'rent' => $rent], 200);
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rent $rent)
    {
        
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any brand without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($rent->creator_type !== $creatorType || $rent->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this rent.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($rent->creator_type !== $creatorType || $rent->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this rent.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

       

        try {
            // Delete the supplier
            $rent->delete();
            return response()->json([
                'success' => true,
                'message' => 'Rent deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Rent: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function rentstrashedcount()
    {
        // Get the count of soft-deleted rents
        $trashedCount = Rent::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }

    public function rentstrashed(Request $request)
    {

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all soft-deleted technicians
            if ($currentUser->role === 'superadmin') {
                // Fetch all trashed technicians without additional checks
                $rentsQuery = Rent::onlyTrashed();
            } else {
                // Regular admin authorization check
                $rentsQuery = Rent::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $rentsQuery = Rent::onlyTrashed()
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
            $rentsQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your brand fields
        }

        // Apply sorting
        $rentsQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $rents = $rentsQuery->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $rents->items(), // Current page items
            'total' => $rents->total(), // Total number of trashed records
        ]);
    }
    // Restore a soft-deleted rents
     public function rentsrestore($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $restored = Rent::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = Rent::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = Rent::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if ($restored) {
            return response()->json(['message' => 'Rent restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Rent not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }

    // Permanently delete a rent from trash
     public function rentsforcedelete($id)
     {

         // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
                
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $rent = Rent::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $rent = Rent::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

            } elseif (Auth::guard('user')->check()) {
                $currentUser = Auth::guard('user')->user();
                $creatorType = User::class;

                // Regular user authorization check
                $rent = Rent::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        try {
             // //rent image check and delete
            if ($rent->photo) {
                $img = $rent->photo;
                $explodeImg = explode("/", $img);
                $EndImg = end($explodeImg);
                $deletePath = public_path("Rents/" .$EndImg);
                if (File::exists($deletePath)) {
                    File::delete($deletePath);
                }  
            }
            // Delete the supplier
            $rent->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Rent permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Rent: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

     }
}
