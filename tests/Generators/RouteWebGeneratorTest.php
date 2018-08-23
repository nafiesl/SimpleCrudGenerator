<?php

namespace Tests\Generators;

use Tests\TestCase;

class RouteWebGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_web_route_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $routeWebPath = base_path('routes/web.php');
        $this->assertFileExists($routeWebPath);
        $routeWebFileContent = "<?php

/*
 * {$this->plural_model_name} Routes
 */
Route::resource('{$this->table_name}', '{$this->model_name}Controller');
";
        $this->assertEquals($routeWebFileContent, file_get_contents($routeWebPath));
    }

    /** @test */
    public function it_creates_correct_web_route_content_with_parent_command_option()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent' => 'Projects', '--no-interaction' => true]);

        $routeWebPath = base_path('routes/web.php');
        $this->assertFileExists($routeWebPath);
        $routeWebFileContent = "<?php

/*
 * {$this->plural_model_name} Routes
 */
Route::resource('{$this->table_name}', 'Projects\\{$this->model_name}Controller');
";
        $this->assertEquals($routeWebFileContent, file_get_contents($routeWebPath));
    }
}
