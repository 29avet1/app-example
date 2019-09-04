<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookUpdateRequest extends FormRequest
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
        $webhookTypes = implode(',', webhook_types());

        return [
            'active'   => 'required|boolean',
            'types'   => 'required|array',
            'types.*' => "required|string|in:{$webhookTypes}",
        ];
    }
}
