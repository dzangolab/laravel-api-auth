<?php

namespace Dzangolab\Auth\Http\Requests;

class ResetPasswordRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'password' => 'required|string|min:6',
        ];
    }
}
