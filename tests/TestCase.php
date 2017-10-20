<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $full_model_name;
    protected $model_name;
    protected $plural_model_name;
    protected $table_name;
    protected $lang_name;
    protected $collection_model_var_name;
    protected $single_model_var_name;

    public function setUp()
    {
        parent::setUp();
        $this->model_name = class_basename('References/Category');

        $this->full_model_name = 'App\\'.$this->model_name;
        $this->plural_model_name = str_plural($this->model_name);
        $this->table_name = snake_case($this->plural_model_name);
        $this->lang_name = snake_case($this->model_name);
        $this->collection_model_var_name = camel_case($this->plural_model_name);
        $this->single_model_var_name = camel_case($this->model_name);
    }

    public function tearDown()
    {
        $this->cleanUpGeneratedFiles();

        parent::tearDown();
    }

    protected function cleanUpGeneratedFiles()
    {
        $this->removeFileOrDir(app_path($this->model_name.'.php'));
        $this->removeFileOrDir(app_path('Entities'));
        $this->removeFileOrDir(app_path('Http'));
        $this->removeFileOrDir(database_path('migrations'));
        $this->removeFileOrDir(database_path('factories'));
        $this->removeFileOrDir(resource_path('views/'.$this->table_name));
        $this->removeFileOrDir(resource_path("lang/en/app.php"));
        $this->removeFileOrDir(resource_path("lang/en/{$this->lang_name}.php"));
        $this->removeFileOrDir(base_path('routes'));
        $this->removeFileOrDir(app_path('Policies'));
        $this->removeFileOrDir(app_path('Providers'));
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
