<?php

namespace Tests;

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
        $this->assertFileExists(base_path("tests/Feature/Manage{$this->pluralModelName}Test.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$this->modelName}Test.php"));
    }
}
