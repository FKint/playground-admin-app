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
            'guardian_first_name' => 'required|max:100',
            'guardian_last_name' => 'required|max:100',
            'tariff_id' => 'exists:tariffs,id',
            'remarks' => '',
            'contact' => '',
            'social_contact' => '',
            'needs_invoice' => 'required|in:0,1',
            'email' => 'max:100',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'needs_invoice' => 'payment method',
        ];
    }
}
