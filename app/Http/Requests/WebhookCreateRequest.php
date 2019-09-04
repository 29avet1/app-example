<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookCreateRequest extends FormRequest
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
            'url'     => 'required|string|active_url|unique:webhook_endpoints,url',
            'active'  => 'required|boolean',
            'types'   => 'required|array',
            'types.*' => "required|string|in:{$webhookTypes}",
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'url'   => 'URL',
            'types' => 'Webhook Types',
        ];
    }

}
