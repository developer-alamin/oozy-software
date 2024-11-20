<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


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
    public function rules(): array
    {
        $floorUuid = $this->route('uuid'); // Fetch the UUID from the route
        $floor = \App\Models\Floor::where('uuid', $floorUuid)->firstOrFail(); // Retrieve the current record by UUID
    
        return [
            'factory_id' => 'required|exists:factories,id', // Ensure factory_id exists in the factories table
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('floors', 'name')
                    ->ignore($floor->id) // Ignore the current record by its primary key
                    ->where(function ($query) use ($floor) {
                        $factoryId = $this->input('factory_id');
                        // Apply the uniqueness rule only if the `factory_id` matches
                        return $query->where('factory_id', $factoryId ?? $floor->factory_id);
                    }),
            ],
            'description' => 'nullable|string',
            'status' => 'nullable|in:Active,Inactive', // Ensure the status is within allowed values
        ];
    }
    
}
