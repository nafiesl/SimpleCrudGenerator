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

use App\Item;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_name_attribute()
    {
        \$item = factory(Item::class)->create(['name' => 'Item 1 name']);
        \$this->assertEquals('Item 1 name', \$item->name);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents(base_path("tests/Unit/Models/{$this->modelName}Test.php")));
    }
}
