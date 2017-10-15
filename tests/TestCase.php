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
        $this->cleanUpGeneratedFiles();

        parent::tearDown();
    }

    protected function cleanUpGeneratedFiles()
    {
        $this->removeFileOrDir(app_path($this->modelName.'.php'));
        $this->removeFileOrDir(app_path('Http'));
        $this->removeFileOrDir(database_path('migrations'));
        $this->removeFileOrDir(database_path('factories'));
        $this->removeFileOrDir(resource_path('views/'.$this->tableName));
        $this->removeFileOrDir(resource_path("lang/en/{$this->singleModelName}.php"));
        $this->removeFileOrDir(base_path('routes'));
        $this->removeFileOrDir(base_path('tests/BrowserKitTest.php'));
        $this->removeFileOrDir(base_path('tests/Feature'));
        $this->removeFileOrDir(base_path('tests/Unit'));
    }

    protected function removeFileOrDir($path)
    {
        if (file_exists($path) && is_file($path)) {
            exec('rm '.$path);
        }

        if (file_exists($path) && is_dir($path)) {
            exec('rm  -r '.$path);
        }
    }

    protected function getPackageProviders($app)
    {
        return ['Luthfi\CrudGenerator\ServiceProvider'];
    }
}
