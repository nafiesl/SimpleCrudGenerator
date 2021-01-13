<?php

namespace Tests\CommandOptions;

use Illuminate\Contracts\Console\Kernel;
use Tests\TestCase;

class TestsOnlyOptionsTest extends TestCase
{
    /** @test */
    public function it_can_generate_only_tests_files()
    {
        $this->artisan('make:crud', [
            'name'         => $this->model_name,
            '--tests-only' => true,
        ]);

        $output = app(Kernel::class)->output();

        $this->assertStringNotContainsString("{$this->model_name} model already exists.", $output);

        $this->assertFileDoesNotExist(app_path($this->model_name.'.php'));
        $this->assertFileDoesNotExist(app_path("Http/Controllers/{$this->model_name}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileDoesNotExist($migrationFilePath);

        $this->assertFileDoesNotExist(resource_path("views/{$this->table_name}/index.blade.php"));
        $this->assertFileDoesNotExist(resource_path("views/{$this->table_name}/forms.blade.php"));

        $localeConfig = config('app.locale');
        $this->assertFileDoesNotExist(resource_path("lang/{$localeConfig}/{$this->lang_name}.php"));

        $this->assertFileDoesNotExist(base_path("routes/web.php"));
        $this->assertFileDoesNotExist(app_path("Policies/{$this->model_name}Policy.php"));
        $this->assertFileDoesNotExist(database_path("factories/{$this->model_name}Factory.php"));

        $this->assertFileExists(base_path("tests/Unit/Models/{$this->model_name}Test.php"));
        $this->assertFileExists(base_path("tests/Unit/Policies/{$this->model_name}PolicyTest.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$this->model_name}Test.php"));

        $this->assertStringContainsString('Test files generated successfully!', $output);
    }
}
