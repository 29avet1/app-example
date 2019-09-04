<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookLogRequest extends FormRequest
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
            'page'       => 'required|numeric',
            'limit'      => 'nullable|numeric',
            'type'       => "nullable|string|in:{$webhookTypes}",
            'start_date' => 'nullable|numeric',
            'end_date'   => 'nullable|numeric',
        ];
    }
}
