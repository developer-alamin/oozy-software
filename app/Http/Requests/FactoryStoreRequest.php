<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FactoryStoreRequest extends FormRequest
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
            'company_id'   => 'required', // Expecting an array for JSON storage
            'name'         => 'required|string|max:255',
            'email'        => 'nullable|email',
            'phone'        => 'nullable|string|max:15',
            'location'     => 'nullable|string',
            'factory_code' => 'required|string|max:50',
            'status'       => 'required|in:Active,Inactive',
            'floor_ids'    => 'array',
            'unit_ids'     => 'array',
            'line_ids'     => 'array',
        ];
    }
}
