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
        return [
            'floor_id' => 'required|exists:floors,id', // Ensure floor_id exists in the floors table
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('units', 'name')
                    ->ignore($unitId) // Ignore the current unit's ID during uniqueness check
                    ->where(function ($query) {
                        $floorId = request('floor_id') ?? Auth::id(); // Use floor_id from request or fallback
                        return $query->where('floor_id', $floorId);
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
