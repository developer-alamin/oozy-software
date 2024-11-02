<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use App\Models\Floor;
use App\Models\Line;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class FactoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $factory = $request->all();

        return response()->json([
            'success' => true,
            'message' => 'factory updated successfully.',
            'factory' => $factory
        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Factory $factory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Factory $factory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factory $factory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factory $factory)
    {
        //
    }

    public function getCompanys(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for users by name with a limit
        $users = User::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the users as JSON
        return response()->json($users);
    }
    public function getFloors(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for floors by name with a limit
        $floors = Floor::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the floors as JSON
        return response()->json($floors);
    }
    public function getUnits(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for units by name with a limit
        $units  = Unit::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the units as JSON
        return response()->json($units);
    }
    public function getLines(Request $request){

        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for lines by name with a limit
        $lines  = Line::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the lines as JSON
        return response()->json($lines);
    }
}