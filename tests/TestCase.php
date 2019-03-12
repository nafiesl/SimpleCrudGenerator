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

    public function setUp(): void
    {
        parent::setUp();
        $this->model_name = 'MemberType';

        $this->full_model_name = 'App\\'.$this->model_name;
        $this->plural_model_name = str_plural($this->model_name);
        $this->table_name = snake_case($this->plural_model_name);
        $this->lang_name = snake_case($this->model_name);
        $this->collection_model_var_name = camel_case($this->plural_model_name);
        $this->single_model_var_name = camel_case($this->model_name);

        $this->withoutMockingConsoleOutput();
    }

    public function tearDown(): void
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
        $this->removeFileOrDir(resource_path('views/categories'));
        $this->removeFileOrDir(resource_path('views/'.$this->table_name));

        $defaultLayoutsFile = config('simple-crud.default_layout_view');

        $dataViewPathArray = explode('.', $defaultLayoutsFile);
        $fileName = array_pop($dataViewPathArray);
        $defaultLayoutPath = resource_path('views/'.implode('/', $dataViewPathArray));

        $this->removeFileOrDir($defaultLayoutPath);

        $locale = config('app.locale');
        $this->removeFileOrDir(resource_path("lang/{$locale}/app.php"));
        $this->removeFileOrDir(resource_path("lang/{$locale}/{$this->lang_name}.php"));
        $this->removeFileOrDir(resource_path("lang/{$locale}/category.php"));

        $this->removeFileOrDir(base_path('routes'));
        $this->removeFileOrDir(app_path('Policies'));
        $this->removeFileOrDir(app_path('Providers'));
        $this->removeFileOrDir(base_path('tests'));
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
