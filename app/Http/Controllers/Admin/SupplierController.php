<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $suppliers = Supplier::all()->map(function ($supplier) {
    //         $supplier->photo = $supplier->photo ? asset('storage/' . $supplier->photo) : null;
    //         return $supplier;
    //     });
    //     return response()->json(['suppliers' => $suppliers, 'total' => $suppliers->count()]);
    // }


    public function index(Request $request)
    {
        // Validate incoming request parameters

        $request->validate([
            'search'       => 'nullable|string|max:255',
            'itemsPerPage' => 'nullable|integer|min:1|max:100',
        ]);

        $query = Supplier::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Sorting
        if ($request->filled('sortBy')) {
            $query->orderBy($request->sortBy, $request->sortDesc ? 'desc' : 'asc');
        }

        // Pagination
        $itemsPerPage = $request->input('itemsPerPage', 15); // Default items per page
        $suppliers    = $query->paginate($itemsPerPage);

        return response()->json([
            'suppliers' => $suppliers->items(),
            'total'     => $suppliers->total(), // Total count for pagination
        ]);
    }


    // public function index(Request $request)
    // {
    //     $query = Supplier::query();
    
    //     // Apply search if provided
    //     if ($request->has('search') && $request->search !== '') {
    //         $search = $request->search;
    //         $query->where(function ($q) use ($search) {
    //             $q->where('name', 'LIKE', "%$search%")
    //               ->orWhere('email', 'LIKE', "%$search%")
    //               ->orWhere('phone', 'LIKE', "%$search%")
    //               ->orWhere('contact_person', 'LIKE', "%$search%");
    //         });
    //     }
    
    //     // Apply sorting
    //     if ($request->has('sortBy')) {
    //         $sortBy = $request->sortBy;
    //         $sortDesc = $request->sortDesc ? 'desc' : 'asc';
    //         $query->orderBy($sortBy, $sortDesc);
    //     }
    
    //     // Pagination
    //     $suppliers = $query->paginate($request->get('itemsPerPage', 10)); // Default 10 items per page
    
    //     return response()->json([
    //         'suppliers' => $suppliers->items(),
    //         'total' => $suppliers->total(),
    //     ]);
    // }
    

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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:suppliers',
            'phone' => 'required|string|max:15',
            'contact_person' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $supplier = new Supplier($request->all());

        if ($request->hasFile('photo')) {
            $supplier->photo = $request->file('photo')->store('photos', 'public');
        }
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
    public function edit(Supplier $supplier)
    {
        return response()->json([
            'supplier' => $supplier
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional validation for photo
        ]);

        // Handle the photo upload if provided
        if ($request->hasFile('photo')) {
            // Delete the old photo if it exists
            if ($supplier->photo && Storage::exists('public/' . $supplier->photo)) {
                Storage::delete('public/' . $supplier->photo);
            }

            // Store the new photo
            $photoPath = $request->file('photo')->store('suppliers', 'public');
            $validatedData['photo'] = $photoPath;
        }

        // Update the supplier's information
        $supplier->update($validatedData);

        // Return a response (either JSON or redirect)
        return response()->json(['message' => 'Supplier updated successfully', 'supplier' => $supplier]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
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
}
