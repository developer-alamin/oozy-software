<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MachineAssignStoreRequest extends FormRequest
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
            'name'                        => 'required|string|max:255',
            'factory_id'                  => 'required|integer',
            'brand_id'                    => 'required|integer',
            'model_id'                    => 'required|integer',
            'machine_type_id'             => 'required|integer',
            'machine_source_id'           => 'nullable',
            'supplier_id'                 => 'nullable',
            'rent_date'                   => 'nullable',
            'rent_name'                   => 'nullable|string|max:255',
            'rent_note'                   => 'nullable|string',
            'rent_amount_type'            => 'nullable|string',
            'machine_code'                => 'required|string|max:255',
            'partial_maintenance_day'     => 'nullable',
            'full_maintenance_day'        => 'nullable',
            'purchase_price'              => 'nullable',
            'purchase_date'               => 'nullable',
            'status'                      => 'nullable',  // Example: assumes "status" has specific values
            'note'                        => 'nullable|string',
            'machine_status_id'           => 'required',
            'qr_code_path'                => 'nullable',
            'serial_number'               => 'nullable',
            'commission_date'             => 'nullable',
            'warranty_period'             => 'nullable',
            'ownership'                   => 'nullable',
            'power_requirements'          => 'nullable',
            'capacity'                    => 'nullable',
            'dimensions'                  => 'nullable',
            'machine_weight'              => 'nullable',
            'material_compatibility'      => 'nullable',
            'maximum_speed'               => 'nullable',
            'optimum_speed'               => 'nullable',
            'operating_temperature_range' => 'nullable',
            'location_status'             => 'nullable',
            'tag'                         => 'nullable',
            'line_id'                     => 'nullable',
            'show_basic_details'          => 'nullable',
            'show_specifications'         => 'nullable',
            'machine_id'                  => 'nullable',
            'uuid'                        => 'nullable'

        ];
    }
}