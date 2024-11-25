<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FactoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $factoryId = $this->route('uuid');

        return [
            'company_id'   => 'required',
            'name'         => [
                'required',
                'string',
                'max:255',
                Rule::unique('factories', 'name')->where(function ($query) {
                    $companyId = request('company_id') ?? Auth::id();
                    return $query->where('company_id', $companyId);
                })->ignore($factoryId, 'uuid'),
            ],
            'email'            => 'nullable|email',
            'phone'            => 'nullable|string|max:15',
            'location'         => 'nullable|string',
            'factory_code'     => 'nullable|string|max:50',
            'factory_owner'    => 'nullable|string',
            'factory_size'     => 'nullable|string',
            'factory_capacity' => 'nullable|string',
            'note'             => 'nullable|string',
            'status'           => 'required|in:Active,Inactive',
        ];
    }
}