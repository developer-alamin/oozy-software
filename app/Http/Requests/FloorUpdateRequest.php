<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Floor;

class FloorUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        // $floorUuid = $this->route('uuid') ?? $this->route()->parameter('uuid') ?? request()->route('uuid');
        // return response()->json($floorUuid,200);
        // // Ensure the Floor exists
        // $floor = Floor::where('uuid', $floorUuid)->first();

        return [
            'factory_id' => 'required|exists:factories,id', // Ensure factory_id exists in the factories table
            'name' => 'required|string|max:255', // Remove the unique constraint
            'description' => 'nullable|string',
            'status' => 'nullable|in:Active,Inactive', // Ensure the status is within allowed values
        ];

    }
    
}
