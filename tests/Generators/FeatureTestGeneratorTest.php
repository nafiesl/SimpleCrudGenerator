<?php

namespace Tests\Generators;

use Tests\TestCase;

class FeatureTestGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_feature_test_class_content()
    {
        $this->artisan('make:crud', ['name' => 'Item', '--no-interaction' => true]);

        $this->assertFileExists(base_path('tests/Feature/ManageItemsTest.php'));
        $modelClassContent = "<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManageItemsTest extends TestCase
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
        $this->assertEquals($modelClassContent, file_get_contents(base_path('tests/Feature/ManageItemsTest.php')));
        exec('rm '.app_path('Item.php'));
        exec('rm -r '.app_path('Http'));
        exec('rm '.database_path('migrations/*'));
        exec('rm -r '.resource_path('views/items'));
        exec('rm -r '.base_path('tests/Feature'));
        exec('rm -r '.base_path('tests/Unit'));
    }
}
