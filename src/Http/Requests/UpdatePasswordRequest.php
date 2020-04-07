<?php

namespace Dzangolab\Auth\Http\Requests;

class UpdatePasswordRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => 'array|required',
            'password.current_password' => 'required|string',
            'password.new_password' => 'required|string|min:6',
        ];
    }
}
