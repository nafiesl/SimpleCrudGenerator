<?php

namespace Tests\Generators;

use Tests\TestCase;

class ModelTestGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_unit_test_class_content()
    {
        $this->artisan('make:crud', ['name' => 'Item', '--no-interaction' => true]);

        $this->assertFileExists(base_path('tests/Unit/Models/ItemTest.php'));
        $modelClassContent = "<?php

namespace DummyNamespace;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ItemTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        \$this->assertTrue(true);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents(base_path('tests/Unit/Models/ItemTest.php')));
        exec('rm '.app_path('Item.php'));
        exec('rm -r '.app_path('Http'));
        exec('rm '.database_path('migrations/*'));
        exec('rm -r '.resource_path('views/items'));
        exec('rm -r '.base_path('tests/Feature'));
        exec('rm -r '.base_path('tests/Unit'));
    }
}
