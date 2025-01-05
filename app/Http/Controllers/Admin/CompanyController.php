<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use Illuminate\Validation\ValidationException;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CompanyController extends Controller
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
        $query = Company::query();
        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        $query->orderBy($sortBy, $sortOrder);
        $categorys = $query->with(['user'])->paginate($itemsPerPage);
        
        
        return response()->json([
            'items' => $categorys->items(),
            'total' => $categorys->total(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function trash(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc');
        $search       = $request->input('search', '');
        $query = Company::onlyTrashed();
        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }
        $query->orderBy($sortBy, $sortOrder);
        $categorys = $query->paginate($itemsPerPage);
        return response()->json([
            'items' => $categorys->items(),
            'total' => $categorys->total(),
        ]);
    }

    public function companyNext(Request $request){
       
    
        try {
            // Validate the request data against the validation rules defined in the Company model
            $validatedData = $request->validate(Company::validationRules());
            $validatedData["uuid"] = HelperController::generateUuid();
            // If validation passes, return a success response
            return response()->json([
                'success' => true,
                'item' => $validatedData,
                'message' => 'Next Step'
            ], Response::HTTP_OK);
    
        } catch (ValidationException  $e) {
            // If validation fails, return a validation error response
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } 

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
        $validatedData = $request->validate(User::validationRules());

        // Determine the authenticated user (from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $creator = Auth::guard('admin')->user();

            // Check if the admin is a superadmin
            if ($creator->role === 'superadmin') {
                // Superadmin-specific logic (if required)
            } else {
                // Regular admin authorization logic (if required)
            }
        } elseif (Auth::guard('user')->check()) {
            $creator = Auth::guard('user')->user();

            // Add user-specific restrictions if required
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $name       = $validatedData['name'];
        $nameResize = preg_replace('/\s+/', '', $name); // Remove spaces from name
        $http       = $request->getSchemeAndHttpHost() . "/";

        if ($request->file("imageFile")) {
            $img         = $request->file("imageFile");
            $imgPathName = $img->getClientOriginalName();
            $ExplodeImg  = explode(".", $imgPathName);
            $endImg      = end($ExplodeImg);
            $RandomPath  = $nameResize.'Img'. rand(5,150) . "." . $endImg;
            $uploadImg   = $http . "Users/" . $RandomPath;
            $img->move(public_path("Users/"), $RandomPath);
        }else{
            $uploadImg = null;
        }

        // Create a new company
        $company = Company::create([
            'name'   => $request->input('companyName'),
            'status' => $request->input('status'),
            'uuid'   => HelperController::generateUuid(),
        ]);

        // Create a new user and associate with the company
        $user = new User();
        $user->company_id  = $company->id;
        $user->name        = $validatedData['name'];
        $user->email       = $validatedData['email'];
        $user->password    = Hash::make($validatedData['password']);
        $user->phone       = $request->input('phone');
        $user->code        = $request->input('code');
        $user->photo       = $uploadImg;
        $user->address     = $request->input('address');
        $user->description = $request->input('description');
        $user->creator()->associate($creator); // Set creator relationship
        $user->updater()->associate($creator); // Set updater relationship
        $user->save();
        
        $company->user_id = $user->id;
        $company->save();

        return response()->json([
            'success' => true,
            'message' => 'Company and user created successfully.',
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $company = Company::where('id', $id)->firstOrFail();
        return response()->json([
            'success' => true,
            'item'=> $company
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $company = Company::where('id', $id)->firstOrFail();
        $validatedData = $request->validate(Company::validationRules());
        $company->fill($validatedData);
        $company->save();

         return response()->json(['success' => true, 'message' => 'Company updated successfully.', 'item' => $company], 200);
    }

    public function trashed_count(Request $request){
        // Get the count of soft-deleted brands
        $trashedCount = Company::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }


    // Restore a soft-deleted operator
    public function restore($id)
    {
        $restored = Company::onlyTrashed()
            ->findOrFail($id)
            ->restore();
        return response()->json(['message' => 'Company restored successfully'], Response::HTTP_OK);
    }

    public function forceDelete($id)
    {
        $operator = Company::onlyTrashed()
            ->findOrFail($id);
        $operator->forceDelete();
        return response()->json(['message' => 'Company permanently deleted'], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        try {
            $company->delete();
            return response()->json([
                'success' => true,
                'message' => 'Company deleted successfully.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting factory: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
