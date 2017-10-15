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
            'Masters', 'Master', 'masters', 'master', 'mstrCollections', 'singleMstr',
        ], $crudMaker->stubModelNames);
    }

    /** @test */
    public function it_has_model_names_property()
    {
        $crudMaker = app(CrudMake::class);

        $this->assertEquals([
            'plural_model_name' => 'Categories',
            'model_name' => 'Category',
            'table_name' => 'categories',
            'lang_name' => 'category',
            'collection_model_var_name' => 'categories',
            'single_model_var_name' => 'category',
        ], $crudMaker->getModelName('category'));
    }

    /** @test */
    public function it_can_generate_simple_crud_files()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

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
}
