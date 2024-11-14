<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MechineAssing;
use App\Models\Operator;
use App\Models\Parse;
use App\Models\Service;
use App\Models\Technician;
use Illuminate\Http\Request;

class ServiceController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }

    public function getMechins(Request $request)
    {
        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for mechines by name with a limit
        $mechines = MechineAssing::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the mechine as JSON
        return response()->json($mechines);
    }
    public function getOperators(Request $request)
    {   
         // Get search term and limit from the request, with defaults
         $search = $request->query('search', '');
         $limit  = $request->query('limit', 5); // Default limit of 10
         // Query to search for operators by name with a limit
         $operators = Operator::where('name', 'like', '%' . $search . '%')
                      ->limit($limit)
                      ->get();
         // Return the operators as JSON
         return response()->json($operators);
    }
    public function getTechnicians(Request $request)
    {
         // Get search term and limit from the request, with defaults
         $search = $request->query('search', '');
         $limit  = $request->query('limit', 5); // Default limit of 10
         // Query to search for technicians by name with a limit
         $technicians = Technician::where('name', 'like', '%' . $search . '%')
                      ->limit($limit)
                      ->get();
         // Return the technicians as JSON
         return response()->json($technicians);
    }
    public function getParses(Request $request)
    {
        // Get search term and limit from the request, with defaults
        $search = $request->query('search', '');
        $limit  = $request->query('limit', 5); // Default limit of 10
        // Query to search for parses by name with a limit
        $parses = Parse::where('name', 'like', '%' . $search . '%')
                     ->limit($limit)
                     ->get();
        // Return the parses as JSON
        return response()->json($parses);
    }
}
