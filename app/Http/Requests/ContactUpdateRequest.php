<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactUpdateRequest extends FormRequest
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
            'name'                 => 'nullable|string|max:255',
            'email'                => 'nullable|email|max:255',
            'facebook_link'        => 'nullable|string|max:255',
            'twitter_link'         => 'nullable|string|max:255',
            'address'              => 'nullable|array',
            'address.city'         => 'nullable|string|max:255',
            'address.line'         => 'nullable|string',
            'address.state'        => "nullable|string|exists_where:states,code,country_code,{$this->address['country_code']}",
            'address.postal_code'  => 'nullable|string|max:255',
            'address.country_code' => 'nullable|string|max:2|uppercase|exists:countries,code',
        ];
    }
}
