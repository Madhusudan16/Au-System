<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:45',
            'user_name' => 'required|min:4|max:20|unique:users,user_name,'.auth()->user()->id.',id',
            'password' => "nullable|min:8",
            'avatar' => "required|image|dimensions:max_width=256,max_height=256"
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'avatar.dimensions' => 'Invalid image dimensions, Please select image  256x256.',
        ];
    }

}
