<?php

namespace Dzangolab\Auth\Http\Requests;

class CreateUserRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => 'required',
        ];
    }
}
