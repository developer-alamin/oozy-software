<?php

namespace App\Http\Controllers;

use App\Models\MachineTag;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class MachineTagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
              $tagsQuery = MachineTag::query(); // No filters applied
          } else {
              // If not superadmin, filter by creator type and id
              $tagsQuery = MachineTag::where('creator_type', $creatorType)
                  ->where('creator_id', $currentUser->id);
          }
      } elseif (Auth::guard('user')->check()) {
          $currentUser = Auth::guard('user')->user();
          $creatorType = User::class;
          // For regular users, filter by creator type and id
          $tagsQuery = MachineTag::where('creator_type', $creatorType)
              ->where('creator_id', $currentUser->id);
      } else {
          return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
      }
      // Apply search if the search term is not empty
      if (!empty($search)) {
          $tagsQuery->where('name', 'LIKE', '%' . $search . '%');
      }
      // Apply sorting
      $tagsQuery->orderBy($sortBy, $sortOrder);
      // Paginate results
      $tags = $tagsQuery->with('creator:id,name')->paginate($itemsPerPage);
      // Return the response as JSON
      return response()->json([
          'items' => $tags->items(), // Current page items
          'total' => $tags->total(), // Total number of records
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
      $validatedData = $request->validate(MachineTag::validationRules());
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
       $tag       = new MachineTag($validatedData);
       $tag->uuid = HelperController::generateUuid();
       $tag->creator()->associate($creator);  // Assign creator polymorphically
       $tag->updater()->associate($creator);  // Associate the updater
       $tag->save(); // Save the technician to the database
       // Return a success response
       return response()->json(['success' => true, 'message' => 'Machine Tag created successfully.'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MachineTag $machineTag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MachineTag $machineTag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MachineTag $machineTag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MachineTag $machineTag)
    {
        //
    }
}