<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateUserRequest
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @package App\Http\Requests
 */
class UserUpdateRequest extends FormRequest
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
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'first_name' => 'First name',
            'last_name'  => 'Last name',
        ];
    }
}
