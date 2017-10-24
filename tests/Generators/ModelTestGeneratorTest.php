<?php

namespace Tests\Generators;

use Tests\TestCase;

class ModelTestGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_unit_test_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $uniTestPath = base_path("tests/Unit/Models/{$this->model_name}Test.php");
        $this->assertFileExists($uniTestPath);
        $modelClassContent = "<?php

namespace Tests\Unit\Models;

use {$this->full_model_name};
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\BrowserKitTest as TestCase;

class {$this->model_name}Test extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_name_attribute()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => '{$this->model_name} 1 name']);
        \$this->assertEquals('{$this->model_name} 1 name', \${$this->single_model_var_name}->name);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($uniTestPath));
    }

    /** @test */
    public function it_creates_correct_unit_test_class_with_base_test_class_based_on_config_file()
    {
        config(['simple-crud.base_test_path' => 'tests/TestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\TestCase']);

        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $uniTestPath = base_path("tests/Unit/Models/{$this->model_name}Test.php");
        $this->assertFileExists($uniTestPath);
        $modelClassContent = "<?php

namespace Tests\Unit\Models;

use {$this->full_model_name};
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase as TestCase;

class {$this->model_name}Test extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_name_attribute()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => '{$this->model_name} 1 name']);
        \$this->assertEquals('{$this->model_name} 1 name', \${$this->single_model_var_name}->name);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($uniTestPath));
    }
}
