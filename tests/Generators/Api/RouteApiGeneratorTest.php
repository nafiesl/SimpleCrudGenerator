<?php

namespace Tests\Generators\Api;

use Tests\TestCase;

class RouteApiGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_api_route_content()
    {
        $this->artisan('make:crud-api', ['name' => $this->model_name, '--no-interaction' => true]);

        $routeApiPath = base_path('routes/api.php');
        $this->assertFileExists($routeApiPath);
        $routeApiFileContent = "<?php

/*
 * {$this->plural_model_name} Endpoints
 */
Route::middleware('auth:api')->resource('{$this->table_name}', 'Api\\{$this->model_name}Controller')->names('api.{$this->table_name}');
";
        $this->assertEquals($routeApiFileContent, file_get_contents($routeApiPath));
    }

    /** @test */
    public function it_creates_correct_api_route_content_with_parent_command_option()
    {
        $this->artisan('make:crud-api', ['name' => $this->model_name, '--parent' => 'Projects', '--no-interaction' => true]);

        $routeApiPath = base_path('routes/api.php');
        $this->assertFileExists($routeApiPath);
        $routeApiFileContent = "<?php

/*
 * {$this->plural_model_name} Endpoints
 */
Route::middleware('auth:api')->resource('{$this->table_name}', 'Api\\Projects\\{$this->model_name}Controller')->names('api.{$this->table_name}');
";
        $this->assertEquals($routeApiFileContent, file_get_contents($routeApiPath));
    }
}
