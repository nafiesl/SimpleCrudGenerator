<?php

namespace Tests\Generators;

use Tests\TestCase;

class ParentModelRouteWebGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_web_route_content()
    {
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $routeWebPath = base_path('routes/web.php');
        $this->assertFileExists($routeWebPath);
        $routeWebFileContent = "<?php

/*
 * {$this->plural_model_name} Routes
 */
Route::resource('{$this->parent_table_name}.{$this->table_name}', App\\Http\\Controllers\\{$this->plural_parent_model_name}\\{$this->model_name}Controller::class);
";
        $this->assertEquals($routeWebFileContent, file_get_contents($routeWebPath));
    }

    /** @test */
    public function it_creates_correct_web_route_content_with_parent_model_disobey_parent_command_option()
    {
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--parent' => 'Projects', '--no-interaction' => true]);

        $routeWebPath = base_path('routes/web.php');
        $this->assertFileExists($routeWebPath);
        $routeWebFileContent = "<?php

/*
 * {$this->plural_model_name} Routes
 */
Route::resource('{$this->parent_table_name}.{$this->table_name}', App\\Http\\Controllers\\{$this->plural_parent_model_name}\\{$this->model_name}Controller::class);
";
        $this->assertEquals($routeWebFileContent, file_get_contents($routeWebPath));
    }
}
