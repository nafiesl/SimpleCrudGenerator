<?php

namespace Luthfi\CrudGenerator;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Crud Generator Service Provider Class.
 */
class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CrudMake::class,
            ]);
        }
    }

    public function boot()
    {

    }
}
