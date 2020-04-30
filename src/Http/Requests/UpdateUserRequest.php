<?php

namespace Dzangolab\Auth\Http\Requests;

class UpdateUserRequest extends ApiRequest
{
    public function rules()
    {
        return config()->has('dzangolabAuth.validation.update_user.rules')
            ? config('dzangolabAuth.validation.update_user.rules')
            : [];
    }
}
