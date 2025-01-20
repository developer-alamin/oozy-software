<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelperController;
use App\Models\Effect; // Changed model to Effect
use App\Models\Admin;
use App\Models\Cause;
use App\Models\FishboneCategory;
use App\Models\User;
use Illuminate\Http\Response;

class CausesController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc');
        $search       = $request->input('search', '');

        $causeQuery = $this->getCausesQuery($search, $sortBy, $sortOrder); // Modified

        // Paginate results
        $causes = $causeQuery->with(['creator:id,name', 'fishbone_category.problemNote.company:id,name'])->paginate($itemsPerPage);

        return response()->json([
            'items' => $causes->items(), // Modified
            'total' => $causes->total(),
        ]);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validatedData = $request->validate(Cause::validationRules()); // Modified


        $creator = $this->getAuthenticatedUser();
        if (!$creator) {
            return $this->errorResponse('Unauthorized');
        }

        $category = FishboneCategory::findOrFail($validatedData["fishbone_category_id"]);
        

        $cause = new Cause($validatedData); 
        $cause->uuid = HelperController::generateUuid();
        $cause->fishbone_category_id = $validatedData['fishbone_category_id'];
        $cause->company_id = $category->company_id;
        $cause->creator()->associate($creator);
        $cause->updater()->associate($creator);

        $cause->save();

        return response()->json([
            'success' => true,
            'message' => 'Cause created successfully.', 
            'data' => $cause 
        ], 201);
    }

    // Edit the specified resource.
    public function edit($uuid)
    {
        $cause = Cause::where('uuid', $uuid)->firstOrFail(); // Modified
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($cause, $creatorType)) { // Modified
            return response()->json([
                'success' => true,
                'item' => $cause // Modified
            ], Response::HTTP_OK);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // Update the specified resource in storage.
    public function update(Request $request, $uuid)
{
    $creator = $this->getAuthenticatedUser();
    if (!$creator) {
        return $this->errorResponse('Unauthorized');
    }

    $cause = Cause::where('uuid', $uuid)->firstOrFail();

    $validatedData = $request->validate(Cause::validationRules());
    $creatorType = $this->getCreatorType();

    // Ensure the updater is set to the authenticated user
    $cause->fill($validatedData);
    $cause->updater()->associate($creator); // Associate the actual user (creator) as the updater
    $cause->save();

    return response()->json(['success' => true, 'message' => 'Cause updated successfully.'], 200);
}

    
    // Destroy the specified resource from storage.
    public function destroy($uuid)
    {
        $cause = Cause::where('uuid', $uuid)->firstOrFail(); // Modified
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($cause, $creatorType)) { // Modified
            try {
                $cause->delete(); // Delete the Cause
                return response()->json(['success' => true, 'message' => 'Cause deleted successfully.'], Response::HTTP_OK); // Modified message
            } catch (\Exception $e) {
                return $this->errorResponse('Error deleting Cause: ' . $e->getMessage()); // Corrected message
            }
        }
        return $this->errorResponse('Forbidden: You are not authorized to delete this Cause.'); // Corrected message
    }


    public function trashed_count()
    {
        $currentUser = $this->getAuthenticatedUser();
        if (!$currentUser) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $creatorType = $this->getCreatorType();
        
        $query = Cause::query();
        $count = $query->where("creator_id", $currentUser->id)
            ->where("creator_type",$creatorType)->onlyTrashed()->count();

        return response()->json(['count' => $count], Response::HTTP_OK);
    }

    public function trashed(Request $request)
    {
        // Get the Effects query with filters and only trashed items
        $causeQuery = $this->getCausesQuery(
            $request->input('search', ''),
            $request->input('sortBy', 'created_at'),
            $request->input('sortOrder', 'desc'),
            true // Only trashed items
        );

        // Paginate results
        $causes = $causeQuery->with(['creator:id,name', 'fishbone_category.problemNote.company:id,name'])->paginate($request->input('itemsPerPage', 5));

        return response()->json([
            'items' => $causes->items(), // Modified
            'total' => $causes->total(),
        ]);
    }

    // Force Delete an Effect.
    public function forceDelete($uuid)
    {
        try {
            // Fetch the soft-deleted Cause by UUID
            $cause = Cause::onlyTrashed()->where('uuid', $uuid)->first();

            if (!$cause) {
                return $this->errorResponse('Cause not found or already permanently deleted.');
            }

            // Check if the user is authorized to delete this Cause
            if (!$this->checkAuthorization($cause, $this->getCreatorType())) {
                return $this->errorResponse('You are not authorized to delete this Cause.');
            }

            // Permanently delete the Cause
            $cause->forceDelete();

            return response()->json([
                'success' => true,
                'message' => 'Cause permanently deleted successfully.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->errorResponse('Error permanently deleting Cause: ' . $e->getMessage());
        }
    }


    // Restore a soft-deleted Effect.
    public function restore($uuid)
    {
        try {
            // Fetch the soft-deleted Cause by UUID
            $cause = Cause::onlyTrashed()->where('uuid', $uuid)->first();

            if (!$cause) {
                return $this->errorResponse('Cause not found or already restored.');
            }

            // Check if the user is authorized to restore this Cause
            if (!$this->checkAuthorization($cause, $this->getCreatorType())) {
                return $this->errorResponse('You are not authorized to restore this Cause.');
            }

            // Restore the Cause
            $cause->restore();

            return response()->json([
                'success' => true,
                'message' => 'Cause restored successfully.',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->errorResponse('Error restoring Cause: ' . $e->getMessage());
        }
    }

    // Helper method to get authenticated user.
    private function getAuthenticatedUser()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        } elseif (Auth::guard('user')->check()) {
            return Auth::guard('user')->user();
        }
        return null;
    }

    // Helper method to get creator type.
    private function getCreatorType()
    {
        return Auth::guard('admin')->check() ? Admin::class : (Auth::guard('user')->check() ? User::class : null);
    }

    // Helper method to check authorization.
    private function checkAuthorization($effect, $creatorType)
    {
        $currentUser = $this->getAuthenticatedUser();
        return $effect->creator_type === $creatorType && $effect->creator_id === $currentUser->id;
    }

    // Helper method to get Effects query with filters.
    private function getCausesQuery($search, $sortBy, $sortOrder, $trashed = false)
    {
        $query = $trashed ? Cause::onlyTrashed() : Cause::query(); // Modified
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
    // Helper method for error response.
    private function errorResponse($message)
    {
        return response()->json(['success' => false, 'message' => $message], Response::HTTP_FORBIDDEN);
    }
}
