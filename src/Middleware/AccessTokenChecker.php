<?php

namespace Dzangolab\Auth\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AccessTokenChecker
{
    protected $authenticateMiddleware;

    public function __construct(Authenticate $authenticateMiddleware) {
        $this->authenticateMiddleware = $authenticateMiddleware;
    }

    public function handle($request, Closure $next, $scopesString = null)
    {
        try {
            return $this->authenticateMiddleware->handle($request, $next, 'api');
        } catch (AuthenticationException $e) {
            throw new UnauthorizedHttpException('Bearer', $e->getMessage(), $e);
        }
    }
}
