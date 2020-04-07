<?php

namespace Dzangolab\Auth\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use ReflectionClass;

class AuthRouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Dzangolab\Auth\Http\Controllers';

    public function map()
    {
        Route::middleware('api')
            ->namespace($this->namespace)
            ->group(dirname((new ReflectionClass($this))->getFileName()).'/../Http/Routes/auth.php');
    }
}
