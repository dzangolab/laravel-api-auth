<?php

namespace Dzangolab\Auth\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateUserRequest extends ApiRequest
{
    public function attributes()
    {
        return [
            'user.email' => 'the user\'s email',
        ];
    }

    public function rules()
    {
        return [
            'profile' => 'array',
            'profile.gender' => [
                Rule::in([1, 2]),
            ],
            'profile.given_name' => 'string|min:2|max:255',
            'profile.surname' => 'string|min:2|max:255',
            'user' => 'array',
            'user.email' => 'email|max:255',
            'user.username' => 'string|min:3|max:255',
        ];
    }
}
