<?php

namespace Tests\Generators;

use Tests\TestCase;

class ModelTestGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_unit_test_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $this->assertFileExists(base_path("tests/Unit/Models/{$this->modelName}Test.php"));
        $modelClassContent = "<?php

namespace Tests\Unit\Models;

use App\\{$this->modelName};
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class {$this->modelName}Test extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_name_attribute()
    {
        \${$this->singleModelName} = factory({$this->modelName}::class)->create(['name' => '{$this->modelName} 1 name']);
        \$this->assertEquals('{$this->modelName} 1 name', \${$this->singleModelName}->name);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents(base_path("tests/Unit/Models/{$this->modelName}Test.php")));
    }
}
