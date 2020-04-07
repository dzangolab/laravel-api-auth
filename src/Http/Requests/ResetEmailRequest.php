<?php

namespace Dzangolab\Auth\Http\Requests;

class ResetEmailRequest extends ApiRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|max:255',
        ];
    }
}
