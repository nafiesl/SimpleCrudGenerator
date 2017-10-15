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

Route::apiResource('{$this->table_name}', '{$this->plural_model_name}Controller');
";
        $this->assertEquals($routeWebFileContent, file_get_contents($routeWebPath));
    }
}
