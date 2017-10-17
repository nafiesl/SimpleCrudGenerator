<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Luthfi\CrudGenerator\CrudMake;

class CrudMakeCommandTest extends TestCase
{
    /** @test */
    public function it_has_stub_model_names_property()
    {
        $crudMaker = app(CrudMake::class);

        $this->assertEquals([
            'model_namespace' => 'mstrNmspc',
            'full_model_name' => 'fullMstr',
            'plural_model_name' => 'Masters',
            'model_name' => 'Master',
            'table_name' => 'masters',
            'lang_name' => 'master',
            'collection_model_var_name' => 'mstrCollections',
            'single_model_var_name' => 'singleMstr',
        ], $crudMaker->stubModelNames);
    }

    /** @test */
    public function it_has_model_names_property()
    {
        $crudMaker = app(CrudMake::class);

        $this->assertEquals([
            'full_model_name' => 'App\Category',
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
            'model_path' => '',
            'model_namespace' => 'App',
        ], $crudMaker->getModelName('Category'));

        $this->assertEquals([
            'full_model_name' => 'App\Category',
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
            'model_path' => '',
            'model_namespace' => 'App',
        ], $crudMaker->getModelName('category'));
    }

    /** @test */
    public function it_set_proper_model_names_property_for_namespaced_model_name_entry()
    {
        $crudMaker = app(CrudMake::class);

        $this->assertEquals([
            'full_model_name' => 'App\Entities\References\Category',
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
            'model_path' => 'Entities/References',
            'model_namespace' => 'App\Entities\References',
        ], $crudMaker->getModelName('Entities/References/Category'));

        $this->assertEquals([
            'full_model_name' => 'App\Models\Category',
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
            'model_path' => 'Models',
            'model_namespace' => 'App\Models',
        ], $crudMaker->getModelName('Models/Category'));

        $this->assertEquals([
            'full_model_name' => 'App\Models\Category',
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
            'model_path' => 'Models',
            'model_namespace' => 'App\Models',
        ], $crudMaker->getModelName('models/category'));
    }

    /** @test */
    public function it_can_generate_simple_crud_files()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertNotRegExp("/{$this->model_name} model already exists./", app(Kernel::class)->output());

        $this->assertFileExists(app_path($this->model_name.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$this->plural_model_name}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$this->table_name}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/forms.blade.php"));
        $this->assertFileExists(resource_path("lang/en/{$this->lang_name}.php"));
        $this->assertFileExists(database_path("factories/{$this->model_name}Factory.php"));
        $this->assertFileExists(base_path("routes/web.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$this->plural_model_name}Test.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$this->model_name}Test.php"));
    }

    /** @test */
    public function it_cannot_generate_crud_files_if_model_exists()
    {
        $this->artisan('make:model', ['name' => $this->model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertRegExp("/{$this->model_name} model already exists./", app(Kernel::class)->output());

        $this->assertFileExists(app_path($this->model_name.'.php'));
        $this->assertFileNotExists(app_path("Http/Controllers/{$this->plural_model_name}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileNotExists($migrationFilePath);

        $this->assertFileNotExists(resource_path("views/{$this->table_name}/index.blade.php"));
        $this->assertFileNotExists(resource_path("views/{$this->table_name}/forms.blade.php"));
        $this->assertFileNotExists(resource_path("lang/en/{$this->lang_name}.php"));
        $this->assertFileNotExists(database_path("factories/{$this->model_name}Factory.php"));
        $this->assertFileNotExists(base_path("routes/web.php"));
        $this->assertFileNotExists(base_path("tests/Feature/Manage{$this->plural_model_name}Test.php"));
        $this->assertFileNotExists(base_path("tests/Unit/Models/{$this->model_name}Test.php"));
    }

    /** @test */
    public function it_can_generate_crud_files_for_namespaced_model()
    {
        $inputName = 'Entities/References/Category';
        $modelName = 'Category';
        $pluralModelName = 'Categories';
        $tableName = 'categories';
        $langName = 'category';
        $modelPath = 'Entities/References';

        $this->artisan('make:crud', ['name' => $inputName, '--no-interaction' => true]);

        $this->assertNotRegExp("/{$modelName} model already exists./", app(Kernel::class)->output());

        $this->assertFileExists(app_path($modelPath.'/'.$modelName.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$pluralModelName}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$tableName.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$tableName}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$tableName}/forms.blade.php"));
        $this->assertFileExists(resource_path("lang/en/{$langName}.php"));
        $this->assertFileExists(database_path("factories/{$modelName}Factory.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$pluralModelName}Test.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$modelName}Test.php"));
    }
}
