<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\ProblemNote;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ProblemNoteController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc');
        $search       = $request->input('search', '');

        $problemNotesQuery = $this->getProblemNotesQuery($search, $sortBy, $sortOrder);

        // Paginate results
        $problemNotes = $problemNotesQuery->with(['creator', 'company'])->paginate($itemsPerPage);

        return response()->json([
            'items' => $problemNotes->items(),
            'total' => $problemNotes->total(),
        ]);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validatedData = $request->validate(ProblemNote::validationRules());

        $creator = $this->getAuthenticatedUser();
        if (!$creator) {
            return $this->errorResponse('Unauthorized');
        }

        $problemNote = new ProblemNote($validatedData);
        $problemNote->uuid = HelperController::generateUuid();
        $problemNote->company_id = $validatedData['company_id'];
        $problemNote->creator()->associate($creator);
        $problemNote->updater()->associate($creator);
        $problemNote->save();

        return response()->json([
            'success' => true,
            'message' => 'ProblemNote created successfully.',
            'data' => $problemNote
        ], 201);
    }

    // Edit the specified resource.
    public function edit($uuid)
    {
        $problemNote = ProblemNote::where('uuid', $uuid)->firstOrFail();
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($problemNote, $creatorType)) {
            return response()->json([
                'success' => true,
                'item' => $problemNote
            ], Response::HTTP_OK);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // Update the specified resource in storage.
    public function update(Request $request,$uuid)
    {
    
        $ProblemNote = ProblemNote::where('uuid', $uuid)->firstOrFail();
         // Validate the incoming request data
         $validatedData = $request->validate(ProblemNote::validationRules());

         // Determine the authenticated user (either from 'admin' or 'user' guard)
         if (Auth::guard('admin')->check()) {
             $currentUser = Auth::guard('admin')->user();
             $creatorType = Admin::class;

             // Check if the admin is a superadmin
             if ($currentUser->role === 'superadmin') {
                 // Superadmin can update without additional checks
             } else {
                 // Regular admin authorization check
                 if ($ProblemNote->creator_type !== $creatorType || $ProblemNote->creator_id !== $currentUser->id) {
                     return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this brand.'], 403);
                 }
             }

         } elseif (Auth::guard('user')->check()) {
             $currentUser = Auth::guard('user')->user();
             $creatorType = User::class;

             // Regular user authorization check
             if ($ProblemNote->creator_type !== $creatorType || $ProblemNote->creator_id !== $currentUser->id) {
                 return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this brand.'], 403);
             }
         } else {
             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
         }


         // Update the brand's details
         $ProblemNote->fill($validatedData);
         //return response()->json($validatedData,200);    

         $ProblemNote->company_id = $validatedData['company_id'];
         $ProblemNote->updater()->associate($currentUser); // Associate the updater
         $ProblemNote->save();

         // Return a success response
         return response()->json(['success' => true, 'message' => 'Problem Note updated successfully.'], 200);
    }
    

    // Destroy the specified resource from storage.
    public function destroy($uuid)
    {
        $problemNote = ProblemNote::where('uuid', $uuid)->firstOrFail();
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($problemNote, $creatorType)) {
            try {
                $problemNote->delete();
                return response()->json(['success' => true, 'message' => 'ProblemNote deleted successfully.'], Response::HTTP_OK);
            } catch (\Exception $e) {
                return $this->errorResponse('Error deleting ProblemNote: ' . $e->getMessage());
            }
        }

        return $this->errorResponse('Forbidden: You are not authorized to delete this ProblemNote.');
    }

    public function trashed_count()
    {
        $trashedCount = ProblemNote::onlyTrashed()->count();
        return response()->json(['trashedCount' => $trashedCount], Response::HTTP_OK);
    }

    public function trashed(Request $request)
    {
        // Get the ProblemNotes query with filters and only trashed notes
        $problemNotesQuery = $this->getProblemNotesQuery(
            $request->input('search', ''),
            $request->input('sortBy', 'created_at'),
            $request->input('sortOrder', 'desc'),
            true // Only trashed items
        );

        // Paginate results
        $problemNotes = $problemNotesQuery->with(['creator', 'company'])->paginate($request->input('itemsPerPage', 5));

        return response()->json([
            'items' => $problemNotes->items(),
            'total' => $problemNotes->total(),
        ]);
    }

    // Force Delete a ProblemNote.
    public function forceDelete($uuid)
    {
        $problemNote = $this->getProblemNote($uuid);
        if ($problemNote) {
            try {
                $problemNote->forceDelete();
                return response()->json([
                    'success' => true,
                    'message' => 'ProblemNote permanently deleted successfully.'
                ], Response::HTTP_OK);
            } catch (\Exception $e) {
                return $this->errorResponse('Error deleting ProblemNote: ' . $e->getMessage());
            }
        }

        return $this->errorResponse('ProblemNote not found or unauthorized');
    }

    // Restore a soft-deleted ProblemNote.
    public function restore($uuid)
    {
        $problemNote = $this->getProblemNote($uuid);
        if ($problemNote) {
            $problemNote->restore();
            return response()->json(['message' => 'ProblemNote restored successfully'], Response::HTTP_OK);
        }

        return $this->errorResponse('ProblemNote not found or unauthorized');
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
    private function checkAuthorization($problemNote, $creatorType)
    {
        $currentUser = $this->getAuthenticatedUser();
        return $problemNote->creator_type === $creatorType && $problemNote->creator_id === $currentUser->id;
    }

    // Helper method to get ProblemNotes query with filters.
    private function getProblemNotesQuery($search, $sortBy, $sortOrder, $trashed = false)
    {
        $query = $trashed ? ProblemNote::onlyTrashed() : ProblemNote::query();
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

    // Helper method to get the ProblemNote by UUID.
    private function getProblemNote($uuid)
    {
        $query = ProblemNote::onlyTrashed()->where('uuid', $uuid);
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

