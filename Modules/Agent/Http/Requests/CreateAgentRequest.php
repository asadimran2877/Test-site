<?php

namespace Modules\Agent\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAgentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        return [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|unique:agents,email',
            'password' => 'required|min:6',
            'confirm_password' => 'required|min:6|same:password',
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
            'first_name' => __('First Name'),
            'last_name' => __('Last Name'),
            'email' => __('Email'),
            'password' => __('Password'),
            'confirm_password' => __('Confirm Password'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
