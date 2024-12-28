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
            'name' =>   "required|string|max:155",
            'status' => 'nullable|string',
            'description' => 'nullable|string',
        ];
    }

}
