<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

class CrudMakeCommandTest extends TestCase
{
    /** @test */
    public function it_can_generate_simple_crud_files()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $this->assertFileExists(app_path($this->modelName.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$this->pluralModelName}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->tableName.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$this->tableName}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->tableName}/forms.blade.php"));
        $this->assertFileExists(resource_path("lang/en/{$this->singleModelName}.php"));
        $this->assertFileExists(database_path("factories/{$this->modelName}Factory.php"));
        $this->assertFileExists(base_path("routes/web.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$this->pluralModelName}Test.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$this->modelName}Test.php"));
    }

    /** @test */
    public function it_cannot_generate_crud_files_if_model_exists()
    {
        $this->artisan('make:model', ['name' => $this->modelName, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $this->assertRegExp("/{$this->modelName} model already exists./", app(Kernel::class)->output());

        $this->assertFileExists(app_path($this->modelName.'.php'));
        $this->assertFileNotExists(app_path("Http/Controllers/{$this->pluralModelName}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->tableName.'_table.php');
        $this->assertFileNotExists($migrationFilePath);

        $this->assertFileNotExists(resource_path("views/{$this->tableName}/index.blade.php"));
        $this->assertFileNotExists(resource_path("views/{$this->tableName}/forms.blade.php"));
        $this->assertFileNotExists(resource_path("lang/en/{$this->singleModelName}.php"));
        $this->assertFileNotExists(database_path("factories/{$this->modelName}Factory.php"));
        $this->assertFileNotExists(base_path("routes/web.php"));
        $this->assertFileNotExists(base_path("tests/Feature/Manage{$this->pluralModelName}Test.php"));
        $this->assertFileNotExists(base_path("tests/Unit/Models/{$this->modelName}Test.php"));
    }
}
