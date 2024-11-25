<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LineRequest extends FormRequest
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
            'unit_id'      => 'required',
            'name'         => [
                'required',
                'string',
                'max:255',
                Rule::unique('lines', 'name')->where(function ($query) {
                    $unitId = request('unit_id') ?? Auth::id(); // Use unit_id from request, fallback to Auth::id
                    return $query->where('unit_id', $unitId);
                }),
            ],
            'status'       => 'nullable|string',
            'description'  => 'nullable|string',
        ];
    }
}