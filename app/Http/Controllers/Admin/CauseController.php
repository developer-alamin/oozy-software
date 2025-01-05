<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelperController;
use App\Models\Cause;
use App\Models\Admin;
use App\Models\ProblemNote;
use App\Models\User;
use Illuminate\Http\Response;


class CauseController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc');
        $search       = $request->input('search', '');

        $causesQuery = $this->getCausesQuery($search, $sortBy, $sortOrder);

        // Paginate results
        $causes = $causesQuery->with(['creator:id,name', 'company:id,name',"problemNote:id,name"])->paginate($itemsPerPage);

        return response()->json([
            'items' => $causes->items(),
            'total' => $causes->total(),
        ]);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validatedData = $request->validate(Cause::validationRules());

        $creator = $this->getAuthenticatedUser();
        if (!$creator) {
            return $this->errorResponse('Unauthorized');
        }
        $problemNote = ProblemNote::findOrFail($validatedData['problem_note_id']);
     
        $cause = new Cause($validatedData);
        $cause->uuid = HelperController::generateUuid();
        $cause->problem_note_id	= $validatedData['problem_note_id'];
        $cause->company_id = $problemNote['company_id'];
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
        $cause = Cause::where('uuid', $uuid)->firstOrFail();
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($cause, $creatorType)) {
            return response()->json([
                'success' => true,
                'item' => $cause
            ], Response::HTTP_OK);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // Update the specified resource in storage.
    public function update(Request $request, $uuid)
    {
        $cause = Cause::where('uuid', $uuid)->firstOrFail();
        
        // Validate the incoming request data
        $validatedData = $request->validate(Cause::validationRules());

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can update without additional checks
            } else {
                // Regular admin authorization check
                if ($cause->creator_type !== $creatorType || $cause->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this cause.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            if ($cause->creator_type !== $creatorType || $cause->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this cause.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $problemNote = ProblemNote::findOrFail($validatedData['problem_note_id']);


        // Update the cause's details
        $cause->fill($validatedData);
        $cause->problem_note_id = $validatedData["problem_note_id"];
        $cause->company_id = $problemNote['company_id'];
        $cause->updater()->associate($currentUser); // Associate the updater
        $cause->save();

        // Return a success response
        return response()->json(['success' => true, 'message' => 'Cause updated successfully.'], 200);
    }

    // Destroy the specified resource from storage.
    public function destroy($uuid)
    {
        $cause = Cause::where('uuid', $uuid)->firstOrFail();
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($cause, $creatorType)) {
            try {
                $cause->delete();
                return response()->json(['success' => true, 'message' => 'Cause deleted successfully.'], Response::HTTP_OK);
            } catch (\Exception $e) {
                return $this->errorResponse('Error deleting Cause: ' . $e->getMessage());
            }
        }

        return $this->errorResponse('Forbidden: You are not authorized to delete this Cause.');
    }

    public function trashed_count()
    {
        $trashedCount = Cause::onlyTrashed()->count();
        return response()->json(['trashedCount' => $trashedCount], Response::HTTP_OK);
    }

    public function trashed(Request $request)
    {
        // Get the Causes query with filters and only trashed notes
        $causesQuery = $this->getCausesQuery(
            $request->input('search', ''),
            $request->input('sortBy', 'created_at'),
            $request->input('sortOrder', 'desc'),
            true // Only trashed items
        );

        // Paginate results
        $causes = $causesQuery->with(['creator', 'company'])->paginate($request->input('itemsPerPage', 5));

        return response()->json([
            'items' => $causes->items(),
            'total' => $causes->total(),
        ]);
    }

    // Force Delete a Cause.
    public function forceDelete($uuid)
    {
        $cause = $this->getCause($uuid);
        if ($cause) {
            try {
                $cause->forceDelete();
                return response()->json([
                    'success' => true,
                    'message' => 'Cause permanently deleted successfully.'
                ], Response::HTTP_OK);
            } catch (\Exception $e) {
                return $this->errorResponse('Error deleting Cause: ' . $e->getMessage());
            }
        }

        return $this->errorResponse('Cause not found or unauthorized');
    }

    // Restore a soft-deleted Cause.
    public function restore($uuid)
    {
        $cause = $this->getCause($uuid);
        if ($cause) {
            $cause->restore();
            return response()->json(['message' => 'Cause restored successfully'], Response::HTTP_OK);
        }

        return $this->errorResponse('Cause not found or unauthorized');
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
    private function checkAuthorization($cause, $creatorType)
    {
        $currentUser = $this->getAuthenticatedUser();
        return $cause->creator_type === $creatorType && $cause->creator_id === $currentUser->id;
    }

    // Helper method to get Causes query with filters.
    private function getCausesQuery($search, $sortBy, $sortOrder, $trashed = false)
    {
        $query = $trashed ? Cause::onlyTrashed() : Cause::query();
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

    // Helper method to get the Cause by UUID.
    private function getCause($uuid)
    {
        $query = Cause::onlyTrashed()->where('uuid', $uuid);
        $creatorType = $this->getCreatorType();
        $currentUser = $this->getAuthenticatedUser();

        if ($creatorType) {
            $query->where('creator_id', $currentUser->id)->where('creator_type', $creatorType);
        }

        return $query->first();
    }

    // Helper method for error response.
    private function errorResponse($message)
    {
        return response()->json(['success' => false, 'message' => $message], Response::HTTP_FORBIDDEN);
    }
}
