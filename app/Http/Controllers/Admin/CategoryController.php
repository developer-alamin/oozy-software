<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     // Validate incoming request parameters

    //     $request->validate([
    //         'search'       => 'nullable|string|max:255',
    //         'itemsPerPage' => 'nullable|integer|min:1|max:100',
    //         'page'         => 'nullable|integer|min:1',
    //     ]);

    //     $query = Category::query();

    //     // Search functionality
    //     if ($request->filled('search')) {
    //         $query->where('name', 'like', '%' . $request->search . '%')
    //             ->orWhere('description', 'like', '%' . $request->search . '%');
    //     }

    //     // Sorting
    //     if ($request->filled('sortBy')) {
    //         $query->orderBy($request->sortBy, $request->sortDesc ? 'desc' : 'asc');
    //     }

    //     // Pagination
    //     $itemsPerPage = $request->input('itemsPerPage', 5); // Default items per page
    //     $categories   = $query->paginate($itemsPerPage);

    //     return response()->json([
    //         'categories' => $categories->items(),
    //         'total'      => $categories->total(), // Total count for pagination
    //     ]);
    // }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $validatedData = $request->validate(Category::validationRules());

    //     Category::create($validatedData);
    //     return response()->json(['success' => true,'message' => 'Category created successfully.'], 201);
    // }


    /**
     * Display the specified resource.
     */
    // public function show(Category $category)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Category $category)
    // {
    //     return response()->json([
    //         'success' => true,
    //         'category' => $category
    //     ], Response::HTTP_OK);
    // }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, Category $category)
    // {
    //     $validatedData = $request->validate(Category::validationRules());

    //     // Update the product model with the validated data
    //     $category->update($validatedData);

    //     // Return a success response
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Category updated successfully.',
    //         // 'category' => $category
    //     ]);
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Category $category)
    // {
    //     try {
    //         // Delete the supplier
    //         $category->delete();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Category deleted successfully.'
    //         ], Response::HTTP_OK);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error deleting Category: ' . $e->getMessage()
    //         ], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    // }











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
                $categorysQuery = Category::query(); // No filters applied
            } else {
                // If not superadmin, filter by creator type and id
                $categorysQuery = Category::where('creator_type', $creatorType)
                    ->where('creator_id', $currentUser->id);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // For regular users, filter by creator type and id
            $categorysQuery = Category::where('creator_type', $creatorType)
                ->where('creator_id', $currentUser->id);
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Apply search if the search term is not empty
        if (!empty($search)) {
            $categorysQuery->where('name', 'LIKE', '%' . $search . '%');
        }
        // Apply sorting
        $categorysQuery->orderBy($sortBy, $sortOrder);
        // Paginate results
        $categorys = $categorysQuery->with('creator:id,name')->paginate($itemsPerPage);
        // Return the response as JSON
        return response()->json([
            'items' => $categorys->items(), // Current page items
            'total' => $categorys->total(), // Total number of records
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
        $validatedData = $request->validate(Category::validationRules());
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
         // Create the technician and associate it with the creator
         $category = new Category($validatedData);
         $category->creator()->associate($creator);  // Assign creator polymorphically
         $category->updater()->associate($creator);  // Associate the updater
         $category->save(); // Save the technician to the database
         // Return a success response
         return response()->json(['success' => true, 'message' => 'Category created successfully.'], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;
            // Check if the admin is a super admin
            if ($currentUser->role === 'superadmin') {
                // Super admins can edit any category
                return response()->json([
                    'success'  => true,
                    'category' => $category
                ], Response::HTTP_OK);
            }
        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        // Check if the category belongs to the current user or admin
        if ($category->creator_type !== $creatorType || $category->creator_id !== $currentUser->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to edit this category.'], 403);
        }
        // Return the category data if authorized
        return response()->json([
            'success' => true,
            'category'=> $category
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {

         // Validate the incoming request data
         $validatedData = $request->validate(Category::validationRules());

         // Determine the authenticated user (either from 'admin' or 'user' guard)
         if (Auth::guard('admin')->check()) {
             $currentUser = Auth::guard('admin')->user();
             $creatorType = Admin::class;

             // Check if the admin is a superadmin
             if ($currentUser->role === 'superadmin') {
                 // Superadmin can update without additional checks
             } else {
                 // Regular admin authorization check
                 if ($category->creator_type !== $creatorType || $category->creator_id !== $currentUser->id) {
                     return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this category.'], 403);
                 }
             }

         } elseif (Auth::guard('user')->check()) {
             $currentUser = Auth::guard('user')->user();
             $creatorType = User::class;

             // Regular user authorization check
             if ($category->creator_type !== $creatorType || $category->creator_id !== $currentUser->id) {
                 return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this category.'], 403);
             }
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }
         // Update the category's details
         $category->fill($validatedData);
         $category->updater()->associate($currentUser); // Associate the updater
         $category->save();

         // Return a success response
         return response()->json(['success' => true, 'message' => 'Category updated successfully.', 'category' => $category], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {

        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can delete any category without additional checks
            } else {
                $creatorType = Admin::class;
                // Regular admin authorization check
                if ($category->creator_type !== $creatorType || $category->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this category.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;
            // Regular user authorization check
            if ($category->creator_type !== $creatorType || $category->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to delete this category.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'category deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
                $categorysQuery = Category::onlyTrashed();
            } else {
                // Regular admin authorization check
                $categorysQuery = Category::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType); // Only fetch soft-deleted records created by this admin
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $categorysQuery = Category::onlyTrashed()
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
            $categorysQuery->where('name', 'LIKE', '%' . $search . '%'); // Adjust as per your category fields
        }

        // Apply sorting
        $categorysQuery->orderBy($sortBy, $sortOrder);

        // Paginate results
        $categorys = $categorysQuery->paginate($itemsPerPage);

        // Return the response as JSON
        return response()->json([
            'items' => $categorys->items(), // Current page items
            'total' => $categorys->total(), // Total number of trashed records
        ]);


    }
    public function trashedcategorysCount()
    {
        // Get the count of soft-deleted categorys
        $trashedCount = Category::onlyTrashed()->count();

        return response()->json([
            'trashedCount' => $trashedCount
        ], Response::HTTP_OK);
    }
     // Permanently delete a category from trash
    public function forceDelete($id)
    {
        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {

            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $category = Category::onlyTrashed()->findOrFail($id);
            } else {
                // Regular admin authorization check
                $category = Category::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            }

            } elseif (Auth::guard('user')->check()) {
                $currentUser = Auth::guard('user')->user();
                $creatorType = User::class;

                // Regular user authorization check
                $category = Category::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id);
            } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Delete the supplier
            $category->forceDelete();
            return response()->json([
                'success' => true,
                'message' => 'Category permanently deleted successfully.'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting Category: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

     // Restore a soft-deleted category
    public function restore($id)
    {

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Superadmin check: Allow access to all trashed technicians
            if ($currentUser->role === 'superadmin') {
                $restored = Category::onlyTrashed()->findOrFail($id)->restore();
            } else {
                // Regular admin authorization check
                $restored = Category::onlyTrashed()
                    ->where('creator_id', $currentUser->id)
                    ->where('creator_type', $creatorType)
                    ->findOrFail($id)
                    ->restore();
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            $restored = Category::onlyTrashed()
                ->where('creator_id', $currentUser->id)
                ->where('creator_type', $creatorType)
                ->findOrFail($id)
                ->restore();
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        if ($restored) {
            return response()->json(['message' => 'Category restored successfully'], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Category not found or is not trashed'], Response::HTTP_NOT_FOUND);
    }

}
