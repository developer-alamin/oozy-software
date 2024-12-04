<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParseUpdateRequest extends FormRequest
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
          'company_id'              => 'required|integer',
          'factory_id'              => 'required|integer',
          'category_id'             => 'required|integer',
          'supplier_name'           => 'required',
          'brand_name'              => 'required',
          'model_name'              => 'required',
          'parse_unit_id'           => 'required|integer',
          'name'                    => 'required|string|max:255',
          'quantity'                => 'nullable|numeric',
          'purchase_price'          => 'nullable|numeric',
          'purchase_date'           => 'nullable',
          'status'                  => 'nullable',  // Example: assumes "status" has specific values
          'note'                    => 'nullable|string',
        ];
    }
}
