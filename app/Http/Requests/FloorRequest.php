<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FloorRequest extends FormRequest
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
            'factory_id'   => 'required|integer',
            'name'         => [
                'required',
                'string',
                'max:255',
                Rule::unique('floors', 'name')->where(function ($query) {
                    $factoryId = request('factory_id') ?? Auth::id(); // Use factory_id from request, fallback to Auth::id
                    return $query->where('factory_id', $factoryId);
                }),
            ],
            'description'  => 'nullable',
            'status'       => 'nullable',
        ];
    }
}
