<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class NewUserRequest extends FormRequest
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
            'user_name' => "required|unique:users,user_name|min:4|max:20",
            'password' =>  "required"
        ];
    }
}
