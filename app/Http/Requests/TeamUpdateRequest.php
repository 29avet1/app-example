<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class EditTeamRequest
 * @property string  $name
 * @property string  $legal_team_name
 * @property string  $legal_entity_type
 * @property string  $website
 * @property string  $logo
 * @property string  $city
 * @property string  $line
 * @property string  $postal_code
 * @property string  $state
 * @property string  $country
 * @property string  $plan
 * @property string  $slug
 * @property integer $user_id
 * @property integer $team_id
 * @package App\Http\Requests
 */
class TeamUpdateRequest extends FormRequest
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
            'name'        => 'required|string|max:255',
            'website'     => 'nullable|string|max:255',
            'email'       => 'nullable|string|max:255',
            'address'     => 'nullable|string:255',
            'about'       => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ];
    }
}
