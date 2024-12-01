<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MechineTransferStore extends FormRequest
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
        //    'company_id'              => 'required|integer',
        //     'factory_id'              => 'required|integer',
        //     'brand_id'                => 'required|integer',
        //     'model_id'                => 'required|integer',
        //     'mechine_type_id'         => 'required|integer',
        //     'mechine_source_id'       => 'required|integer',
        //     'supplier_id'             => 'nullable',
        //     'rent_id'                 => 'nullable|integer',
        //     'rent_date'               => 'nullable',
        //     'name'                    => 'required|string|max:255',
        //     'mechine_code'            => 'required|string|max:255',
        //     'serial_number'           => 'nullable|string|max:255',
        //     'preventive_service_days' => 'nullable',
        //     'purchace_price'          => 'nullable|numeric',
        //     'purchase_date'           => 'nullable',
        //     'status'                  => 'nullable',  // Example: assumes "status" has specific values
        //     'note'                    => 'nullable|string',
        //     'mechine_transfer_id'     => 'nullable|integer',
        //     'mechine_status'          => 'nullable'
        ];
    }
}