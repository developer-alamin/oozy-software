<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rent;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class RentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
         // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by name
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is ascending
        $search       = $request->input('search', ''); // Search term, default is empty
    
        // Query brands with pagination, sorting, and search
        $RentsQuery = Rent::query();
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $RentsQuery->where('name', 'LIKE', '%' . $search . '%');
        }
    
        // Apply sorting
        $RentsQuery->orderBy($sortBy, $sortOrder);
    
        // Paginate results
        $Rents = $RentsQuery->paginate($itemsPerPage);
    
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
        
        Rent::create([
            'name' => $name,
            'email' => $request->email,
            'phone' => $request->phone,
            'photo' => $uploadImg,
            'address' => $request->address,
            'description' => $request->description,
        ]);

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
         return response()->json([
            'success' => true,
            'rent' => $rent
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Rent $rent)
    {
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
        $rent->update();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Rent updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rent $rent)
    {
        
       

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
        // Get parameters from the request
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at'); // Default sort by name
        $sortOrder    = $request->input('sortOrder', 'desc'); // Default order is descending
        $search       = $request->input('search', ''); // Search term, default is empty

        // Query only soft deleted brands with pagination, sorting, and search
        $rentsQuery = Rent::onlyTrashed(); // Fetch only soft-deleted records

        // Apply search if the search term is not empty
        if (!empty($search)) {
            $rentsQuery->where('name', 'LIKE', '%' . $search . '%');
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
        // Attempt to restore the rents using the static method on the model
        $restored = Rent::restoreGroup($id);

        if ($restored) {
            return response()->json(['message' => 'Rent restored successfully'], 200);
        }

        return response()->json(['message' => 'Rent not found or is not trashed'], 404);
    }

    // Permanently delete a rent from trash
     public function rentsforcedelete($id)
     {
         $rent = Rent::onlyTrashed()->findOrFail($id);

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
        $rent->forceDelete(); // Permanent delete
 
         return response()->json(['message' => 'Rent permanently deleted']);
     }
}
