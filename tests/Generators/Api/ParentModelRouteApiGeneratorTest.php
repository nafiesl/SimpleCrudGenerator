<?php

namespace Tests\Generators\Api;

use Tests\TestCase;

class ParentModelRouteApiGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_api_route_content()
    {
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud-api', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $routeApiPath = base_path('routes/api.php');
        $this->assertFileExists($routeApiPath);
        $routeApiFileContent = "<?php

/*
 * {$this->plural_model_name} Endpoints
 */
Route::middleware('auth:api')->resource('{$this->parent_table_name}.{$this->table_name}', App\\Http\\Controllers\\Api\\{$this->plural_parent_model_name}\\{$this->model_name}Controller::class)->names('api.{$this->parent_table_name}.{$this->table_name}');
";
        $this->assertEquals($routeApiFileContent, file_get_contents($routeApiPath));
    }

    /** @test */
    public function it_creates_correct_api_route_content_with_parent_model_will_disobey_parent_command_option()
    {
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud-api', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--parent' => 'Projects', '--no-interaction' => true]);

        $routeApiPath = base_path('routes/api.php');
        $this->assertFileExists($routeApiPath);
        $routeApiFileContent = "<?php

/*
 * {$this->plural_model_name} Endpoints
 */
Route::middleware('auth:api')->resource('{$this->parent_table_name}.{$this->table_name}', App\\Http\\Controllers\\Api\\{$this->plural_parent_model_name}\\{$this->model_name}Controller::class)->names('api.{$this->parent_table_name}.{$this->table_name}');
";
        $this->assertEquals($routeApiFileContent, file_get_contents($routeApiPath));
    }
}
