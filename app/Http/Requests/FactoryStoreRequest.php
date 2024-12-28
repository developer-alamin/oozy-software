<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

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
            'company_id'       => 'required|exists:companies,id', // Ensure company_id is present
            'name'             => "required|string|max:255",
            'email'            => 'nullable|email',
            'phone'            => 'nullable|string|max:15',
            'location'         => 'nullable|string',
            'factory_code'     => 'nullable|string|max:50',
            'phone'            => 'nullable|string|max:25',
            'factory_owner'    => 'nullable|string',
            'factory_size'     => 'nullable|string',
            'factory_capacity' => 'nullable|string',
            'note'             => 'nullable|string',
            'status'           => 'required|in:Active,Inactive',
        ];
    }
}
