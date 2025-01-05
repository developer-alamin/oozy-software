<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HelperController;
use App\Models\Action;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ActionsController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc');
        $search       = $request->input('search', '');

        $actionsQuery = $this->getActionsQuery($search, $sortBy, $sortOrder);
        
        // Paginate results
        $actions = $actionsQuery->with(['creator', 'company'])->paginate($itemsPerPage);

        return response()->json([
            'items' => $actions->items(),
            'total' => $actions->total(),
        ]);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validatedData = $request->validate(Action::validationRules());

        $creator = $this->getAuthenticatedUser();
        if (!$creator) {
            return $this->errorResponse('Unauthorized');
        }

        $action = new Action($validatedData);
        $action->uuid = HelperController::generateUuid();
        $action->company_id = $validatedData['company_id'];
        $action->creator()->associate($creator);
        $action->updater()->associate($creator);
        $action->save();

        return response()->json([
            'success' => true,
            'message' => 'Action created successfully.',
            'data' => $action
        ], 201);
    }

    // Edit the specified resource.
    public function edit($uuid)
    {
        $action = Action::where('uuid', $uuid)->firstOrFail();
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($action, $creatorType)) {
            return response()->json([
                'success' => true,
                'item' => $action
            ], Response::HTTP_OK);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // Update the specified resource in storage.
    public function update(Request $request, $uuid)
    {
        $action = Action::where('uuid', $uuid)->firstOrFail();
        $validatedData = $request->validate(Action::validationRules());

        $creator = $this->getAuthenticatedUser();
        if (!$creator || !$this->checkAuthorization($action, $creator)) {
            return $this->errorResponse('Forbidden: You are not authorized to update this action.');
        }

        $action->fill($validatedData);
        $action->company_id = $validatedData['company_id'];
        $action->updater()->associate($creator);
        $action->save();

        return response()->json(['success' => true, 'message' => 'Action updated successfully.', 'action' => $action], 200);
    }

    // Destroy the specified resource from storage.
    public function destroy($uuid)
    {
        $action = Action::where('uuid', $uuid)->firstOrFail();
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($action, $creatorType)) {
            try {
                $action->delete();
                return response()->json(['success' => true, 'message' => 'Action deleted successfully.'], Response::HTTP_OK);
            } catch (\Exception $e) {
                return $this->errorResponse('Error deleting action: ' . $e->getMessage());
            }
        }

        return $this->errorResponse('Forbidden: You are not authorized to delete this action.');
    }

    // Get the count of soft-deleted actions.
    public function trashedActionsCount()
    {
        $trashedCount = Action::onlyTrashed()->count();
        return response()->json(['trashedCount' => $trashedCount], Response::HTTP_OK);
    }

    // Display trashed actions.
    public function trashed(Request $request)
    {
        $actionsQuery = $this->getActionsQuery($request->input('search', ''), $request->input('sortBy', 'created_at'), $request->input('sortOrder', 'desc'), true);
        
        // Paginate results
        $actions = $actionsQuery->with(['creator', 'company'])->paginate($request->input('itemsPerPage', 5));

        return response()->json([
            'items' => $actions->items(),
            'total' => $actions->total(),
        ]);
    }

    // Force Delete an Action.
    public function forceDelete($uuid)
    {
        $action = $this->getAction($uuid);
        if ($action) {
            try {
                $action->forceDelete();
                return response()->json([
                    'success' => true,
                    'message' => 'Action permanently deleted successfully.'
                ], Response::HTTP_OK);
            } catch (\Exception $e) {
                return $this->errorResponse('Error deleting action: ' . $e->getMessage());
            }
        }

        return $this->errorResponse('Action not found or unauthorized');
    }

    // Restore a soft-deleted Action.
    public function restore($uuid)
    {
        $action = $this->getAction($uuid);
        if ($action) {
            $action->restore();
            return response()->json(['message' => 'Action restored successfully'], Response::HTTP_OK);
        }

        return $this->errorResponse('Action not found or unauthorized');
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
    private function checkAuthorization($action, $creatorType)
    {
        $currentUser = $this->getAuthenticatedUser();
        return $action->creator_type === $creatorType && $action->creator_id === $currentUser->id;
    }

    // Helper method to get actions query with filters.
    private function getActionsQuery($search, $sortBy, $sortOrder, $trashed = false)
    {
        $query = $trashed ? Action::onlyTrashed() : Action::query();
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

    // Helper method to get the action by UUID.
    private function getAction($uuid)
    {
        $query = Action::onlyTrashed()->where('uuid', $uuid);
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
