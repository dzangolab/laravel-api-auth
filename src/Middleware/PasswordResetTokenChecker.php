<?php

namespace Dzangolab\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PasswordResetTokenChecker
{
    public function handle(Request $request, Closure $next)
    {
        $validator = Validator::make(
            $request->route()->parameters(),
            [
                'token' => 'exists:password_resets,token',
            ]
        );

        if ($validator->fails()) {
            return redirect('/login')->withErrors($validator);
        }

        return $next($request);
    }
}
