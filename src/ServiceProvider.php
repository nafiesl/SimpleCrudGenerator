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
                CrudSimpleMake::class,
                CrudApiMake::class,
            ]);
        }

        $this->mergeConfigFrom(__DIR__.'/config.php', 'simple-crud');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('simple-crud.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/stubs' => base_path('stubs/simple-crud'),
        ], 'stubs');
    }
}
