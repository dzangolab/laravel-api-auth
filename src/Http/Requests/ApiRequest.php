<?php

namespace Dzangolab\Auth\Http\Requests;

use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidatesWhenResolvedTrait;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/** @deprecated directly use Illuminate Request instead */
abstract class ApiRequest extends Request implements ValidatesWhenResolved
{
    use ValidatesWhenResolvedTrait;

    protected $container;

    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
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
