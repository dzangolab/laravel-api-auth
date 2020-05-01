<?php

namespace Dzangolab\Auth\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

abstract class ApiRequest extends FormRequest
{
    const CONFIG_VALIDATION_RULES = '';

    public function rules()
    {
        $config = static::CONFIG_VALIDATION_RULES;

        return config()->has($config)
            ? config($config)
            : [];
    }

    protected function failedAuthorization()
    {
        throw new HttpException(403);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new UnprocessableEntityHttpException($validator->errors()->toJson());
    }
}
