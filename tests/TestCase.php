<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $modelName;
    protected $pluralModelName;
    protected $tableName;
    protected $singleModelName;

    public function setUp()
    {
        parent::setUp();
        $this->modelName = 'Category';

        $this->pluralModelName = str_plural($this->modelName);
        $this->tableName = strtolower($this->pluralModelName);
        $this->singleModelName = strtolower($this->modelName);
    }

    public function tearDown()
    {
        exec('rm '.app_path($this->modelName.'.php'));
        exec('rm -r '.app_path('Http'));
        exec('rm '.database_path('migrations/*'));
        exec('rm -r '.resource_path('views/'.$this->tableName));
        exec('rm -r '.base_path('routes'));
        exec('rm '.base_path('tests/BrowserKitTest.php'));
        exec('rm -r '.base_path('tests/Feature'));
        exec('rm -r '.base_path('tests/Unit'));

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return ['Luthfi\CrudGenerator\ServiceProvider'];
    }
}
