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

Route::resource('{$this->table_name}', '{$this->plural_model_name}Controller');
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

Route::resource('{$this->table_name}', 'Projects\\{$this->plural_model_name}Controller');
";
        $this->assertEquals($routeApiFileContent, file_get_contents($routeApiPath));
    }
}
