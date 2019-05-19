<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFamilyInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'guardian_first_name' => 'max:100',
            'guardian_last_name' => 'max:100',
            'tariff_id' => 'exists:tariffs,id',
            'remarks' => '',
            'contact' => '',
            'social_contact' => '',
            'needs_invoice' => '',
        ];
    }
}
