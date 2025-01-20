<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelperController;
use App\Models\FishboneCategory;
use App\Models\Admin;
use App\Models\ProblemNote;
use App\Models\User;
use Illuminate\Http\Response;

class FishboneCategoryController extends Controller
{
    public function index(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc');
        $search       = $request->input('search', '');

        $categoriesQuery = $this->getCategoriesQuery($search, $sortBy, $sortOrder);

        $categories = $categoriesQuery->with(['creator:id,name', 'company:id,name', 'problemNote:id,name'])->paginate($itemsPerPage);

        return response()->json([
            'items' => $categories->items(),
            'total' => $categories->total(),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate(FishboneCategory::validationRules());

        $creator = $this->getAuthenticatedUser();
        if (!$creator) {
            return $this->errorResponse('Unauthorized', 401);
        }

        $problemNote = ProblemNote::findOrFail($validatedData['problem_note_id']);

        $category = new FishboneCategory($validatedData);
        $category->uuid = HelperController::generateUuid();
        $category->problem_note_id = $validatedData['problem_note_id'];
        $category->company_id = $problemNote['company_id'];
        $category->creator()->associate($creator);
        $category->updater()->associate($creator);
        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $category,
        ], 201);
    }

    public function edit($uuid)
    {
        $category = FishboneCategory::where('uuid', $uuid)->firstOrFail();
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($category, $creatorType)) {
            return response()->json([
                'success' => true,
                'item' => $category,
            ], 200);
        }

        return $this->errorResponse('Unauthorized', 403);
    }

    public function update(Request $request, $uuid)
    {
        $category = FishboneCategory::where('uuid', $uuid)->firstOrFail();

        $validatedData = $request->validate(FishboneCategory::validationRules());

        $currentUser = $this->getAuthenticatedUser();
        $creatorType = $this->getCreatorType();

        if (!$this->checkAuthorization($category, $creatorType)) {
            return $this->errorResponse('Forbidden: You are not authorized to update this category.', 403);
        }

        $problemNote = ProblemNote::findOrFail($validatedData['problem_note_id']);

        $category->fill($validatedData);
        $category->problem_note_id = $validatedData['problem_note_id'];
        $category->company_id = $problemNote['company_id'];
        $category->updater()->associate($currentUser);
        $category->save();

        return response()->json(['success' => true, 'message' => 'Category updated successfully.'], 200);
    }

    public function destroy($uuid)
    {
        $category = FishboneCategory::where('uuid', $uuid)->firstOrFail();
        $creatorType = $this->getCreatorType();

        if (!$this->checkAuthorization($category, $creatorType)) {
            return $this->errorResponse('Forbidden: You are not authorized to delete this category.', 403);
        }

        try {
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully.'], 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Error deleting category: ' . $e->getMessage(), 500);
        }
    }

    public function trashed_count()
    {
        
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $creatorType = $this->getCreatorType();

        $trashedCount = FishboneCategory::where('creator_type', $creatorType)
            ->where('creator_id', $currentUser->id)
            ->onlyTrashed()->count(); // Modified
        
        return response()->json(['count' => $trashedCount], Response::HTTP_OK);
    }

    public function trashed(Request $request)
    {
         // Get the Effects query with filters and only trashed items
        $categoryQuery = $this->getCategoriesQuery(
            $request->input('search', ''),
            $request->input('sortBy', 'created_at'),
            $request->input('sortOrder', 'desc'),
            true // Only trashed items
        );

        // Paginate results for trashed categories
        $categories = $categoryQuery->with(['creator', 'company'])->paginate($request->input('itemsPerPage', 5));

        return response()->json([
            'items' => $categories->items(),
            'total' => $categories->total(),
        ]);
    }
    public function forceDelete($uuid)
    {
        // Find the FishboneCategory by UUID
        $category = FishboneCategory::onlyTrashed()->where('uuid', $uuid)->firstOrFail();
        
        // If category is found, proceed with the deletion
        if ($category) {
            // Check if the current user is authorized to delete the category
            $creatorType = $this->getCreatorType();

            if (!$this->checkAuthorization($category, $creatorType)) {
                return $this->errorResponse('Forbidden: You are not authorized to delete this category.', 403);
            }
    
            try {
                // Permanently delete the category
                $category->forceDelete();
    
                // Return success response
                return response()->json([
                    'success' => true,
                    'message' => 'Category permanently deleted successfully.'
                ], Response::HTTP_OK);
            } catch (\Exception $e) {
                // Handle any errors that occur during the deletion process
                return $this->errorResponse('Error deleting category: ' . $e->getMessage());
            }
        }
    
        // Return error response if the category is not found
        return $this->errorResponse('Category not found or unauthorized');
    }
    
    public function restore($uuid)
    {
        $category = $this->getFishboneCategory($uuid); // Modified method call
    
        if ($category) {
            $category->restore(); // Restores the category
            return response()->json(['message' => 'Category restored successfully'], Response::HTTP_OK);
        }
    
        return $this->errorResponse('Category not found or unauthorized'); // Adjusted message
    }
    
    private function getFishboneCategory($uuid)
    {
        $query = FishboneCategory::onlyTrashed()->where('uuid', $uuid); // Modified query for trashed categories
        $creatorType = $this->getCreatorType();
        $currentUser = $this->getAuthenticatedUser();
    
        if ($creatorType && $currentUser) {
            $query->where('creator_id', $currentUser->id)->where('creator_type', $creatorType); // Checks for creator authorization
        }
    
        return $query->first();
    }
    

    // Helper methods
    private function getCategoriesQuery($search, $sortBy, $sortOrder, $trashed = false)
    {
        $query = $trashed ? FishboneCategory::onlyTrashed() : FishboneCategory::query();
        $creatorType = $this->getCreatorType();
        $currentUser = $this->getAuthenticatedUser();

        if ($creatorType) {
            $query->where('creator_type', $creatorType)
                  ->where('creator_id', $currentUser->id);
        }

        if (!empty($search)) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        $query->orderBy($sortBy, $sortOrder);
        return $query;
    }

    private function getAuthenticatedUser()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            return Auth::guard('user')->user();
        }
        return null;
    }

    private function getCreatorType()
    {
        if (Auth::guard('admin')->check()) {
            return Admin::class;
        } elseif (Auth::guard('user')->check()) {
            return User::class;
        }
        return null;
    }

    private function errorResponse($message, $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    private function checkAuthorization($category, $creatorType)
    {
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return false;
        }

        return $category->creator_type === $creatorType && $category->creator_id === $currentUser->id;
    }
}