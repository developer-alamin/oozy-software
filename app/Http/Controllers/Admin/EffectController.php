<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HelperController;
use App\Models\Effect; // Changed model to Effect
use App\Models\Admin;
use App\Models\Cause;
use App\Models\User;
use Illuminate\Http\Response;

class EffectController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        $page         = $request->input('page', 1);
        $itemsPerPage = $request->input('itemsPerPage', 5);
        $sortBy       = $request->input('sortBy', 'created_at');
        $sortOrder    = $request->input('sortOrder', 'desc');
        $search       = $request->input('search', '');

        $effectsQuery = $this->getEffectsQuery($search, $sortBy, $sortOrder); // Modified

        // Paginate results
        $effects = $effectsQuery->with(['creator:id,name', 'cuase.problemNote.company:id,name'])->paginate($itemsPerPage);

        return response()->json([
            'items' => $effects->items(), // Modified
            'total' => $effects->total(),
        ]);
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validatedData = $request->validate(Effect::validationRules()); // Modified


        $creator = $this->getAuthenticatedUser();
        if (!$creator) {
            return $this->errorResponse('Unauthorized');
        }
        $cause = Cause::findOrFail($validatedData["cause_id"]);



        $effect = new Effect($validatedData); // Modified
        $effect->uuid = HelperController::generateUuid();
        $effect->cause_id = $validatedData['cause_id'];
        $effect->company_id = $cause->company_id;
        $effect->creator()->associate($creator);
        $effect->updater()->associate($creator);

        //return response()->json($effect,200);

        $effect->save();

        return response()->json([
            'success' => true,
            'message' => 'Effect created successfully.', // Modified
            'data' => $effect // Modified
        ], 201);
    }

    // Edit the specified resource.
    public function edit($uuid)
    {
        $effect = Effect::where('uuid', $uuid)->firstOrFail(); // Modified
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($effect, $creatorType)) { // Modified
            return response()->json([
                'success' => true,
                'item' => $effect // Modified
            ], Response::HTTP_OK);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // Update the specified resource in storage.
    public function update(Request $request, $uuid)
    {
        $effect = Effect::where('uuid', $uuid)->firstOrFail(); // Modified
        
        // Validate the incoming request data
        $validatedData = $request->validate(Effect::validationRules()); // Modified

        // Determine the authenticated user (either from 'admin' or 'user' guard)
        if (Auth::guard('admin')->check()) {
            $currentUser = Auth::guard('admin')->user();
            $creatorType = Admin::class;

            // Check if the admin is a superadmin
            if ($currentUser->role === 'superadmin') {
                // Superadmin can update without additional checks
            } else {
                // Regular admin authorization check
                if ($effect->creator_type !== $creatorType || $effect->creator_id !== $currentUser->id) {
                    return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this effect.'], 403);
                }
            }

        } elseif (Auth::guard('user')->check()) {
            $currentUser = Auth::guard('user')->user();
            $creatorType = User::class;

            // Regular user authorization check
            if ($effect->creator_type !== $creatorType || $effect->creator_id !== $currentUser->id) {
                return response()->json(['success' => false, 'message' => 'Forbidden: You are not authorized to update this effect.'], 403);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $cause = Cause::findOrFail($validatedData["cause_id"]);

        // Update the effect's details
        $effect->fill($validatedData); // Modified
        $effect->cause_id = $validatedData['cause_id'];
        $effect->company_id = $cause->company_id;
        $effect->updater()->associate($currentUser); // Associate the updater
        $effect->save();

        // Return a success response
        return response()->json(['success' => true, 'message' => 'Effect updated successfully.'], 200);
    }

    // Destroy the specified resource from storage.
    public function destroy($uuid)
    {
        $effect = Effect::where('uuid', $uuid)->firstOrFail(); // Modified
        $creatorType = $this->getCreatorType();

        if ($this->checkAuthorization($effect, $creatorType)) { // Modified
            try {
                $effect->delete();
                return response()->json(['success' => true, 'message' => 'Effect deleted successfully.'], Response::HTTP_OK);
            } catch (\Exception $e) {
                return $this->errorResponse('Error deleting Effect: ' . $e->getMessage()); // Modified
            }
        }

        return $this->errorResponse('Forbidden: You are not authorized to delete this Effect.'); // Modified
    }

    public function trashed_count()
    {
        $trashedCount = Effect::onlyTrashed()->count(); // Modified
        return response()->json(['trashedCount' => $trashedCount], Response::HTTP_OK);
    }

    public function trashed(Request $request)
    {
        // Get the Effects query with filters and only trashed items
        $effectsQuery = $this->getEffectsQuery(
            $request->input('search', ''),
            $request->input('sortBy', 'created_at'),
            $request->input('sortOrder', 'desc'),
            true // Only trashed items
        );

        // Paginate results
        $effects = $effectsQuery->with(['creator', 'company'])->paginate($request->input('itemsPerPage', 5));

        return response()->json([
            'items' => $effects->items(), // Modified
            'total' => $effects->total(),
        ]);
    }

    // Force Delete an Effect.
    public function forceDelete($uuid)
    {
        $effect = $this->getEffect($uuid); // Modified
        if ($effect) {
            try {
                $effect->forceDelete();
                return response()->json([
                    'success' => true,
                    'message' => 'Effect permanently deleted successfully.' // Modified
                ], Response::HTTP_OK);
            } catch (\Exception $e) {
                return $this->errorResponse('Error deleting Effect: ' . $e->getMessage()); // Modified
            }
        }

        return $this->errorResponse('Effect not found or unauthorized');
    }

    // Restore a soft-deleted Effect.
    public function restore($uuid)
    {
        $effect = $this->getEffect($uuid); // Modified
        if ($effect) {
            $effect->restore();
            return response()->json(['message' => 'Effect restored successfully'], Response::HTTP_OK);
        }

        return $this->errorResponse('Effect not found or unauthorized');
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
    private function getEffectsQuery($search, $sortBy, $sortOrder, $trashed = false)
    {
        $query = $trashed ? Effect::onlyTrashed() : Effect::query(); // Modified
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

    // Helper method to get the Effect by UUID.
    private function getEffect($uuid)
    {
        $query = Effect::onlyTrashed()->where('uuid', $uuid); // Modified
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
