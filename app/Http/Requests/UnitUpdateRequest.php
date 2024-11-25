<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UnitUpdateRequest extends FormRequest
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
    public function rules(): array
    {
        $unitUuid = $this->route('uuid'); // Fetch the UUID from the route
        $unit = \App\Models\Unit::where('uuid', $unitUuid)->firstOrFail(); // Retrieve the current record by UUID
    
        return [
            'floor_id' => 'required|exists:floors,id', // Ensure floor_id exists in the floors table
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'name')
                    ->ignore($unit->id) // Ignore the current unit's ID during the uniqueness check
                    ->where(function ($query) use ($unit) {
                        $floorId = $this->input('floor_id');
                        // Apply the uniqueness rule only if the `floor_id` matches
                        return $query->where('floor_id', $floorId ?? $unit->floor_id);
                    }),
            ],
            'description' => 'nullable|string',
            'status' => 'nullable|in:Active,Inactive',
            'meta_data' => 'nullable',
            'creator_id' => 'nullable',
            'creator_type' => 'nullable',
            'updater_id' => 'nullable',
            'updater_type' => 'nullable',
        ];
    }
    
}