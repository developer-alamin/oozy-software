<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LineUpdateRequest extends FormRequest
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
        $lineUuid = $this->route('uuid'); // Fetch the UUID from the route
        $line = \App\Models\Line::where('uuid', $lineUuid)->firstOrFail(); // Retrieve the current record
       
        return [
            'unit_id' => 'required|exists:units,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('lines', 'name')
                    ->ignore($line->id) // Ignore the current record by its primary key
                    ->where(function ($query) use ($line) {
                        $unitId = $this->input('unit_id');
                        // Apply the uniqueness only if the `unit_id` matches
                        return $query->where('unit_id', $unitId ?? $line->unit_id);
                    }),
            ],
            'status' => 'nullable|string',
            'description' => 'nullable|string',
        ];
    }

}
