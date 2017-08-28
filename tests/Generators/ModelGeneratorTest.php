<?php

namespace Tests\Generators;

use Tests\TestCase;

class ModelGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_model_class_content()
    {
        $this->artisan('make:crud', ['name' => 'Item', '--no-interaction' => true]);

        $this->assertFileExists(app_path('Item.php'));
        $modelClassContent = "<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
}
";
        $this->assertEquals($modelClassContent, file_get_contents(app_path('Item.php')));
        exec('rm '.app_path('Item.php'));
        exec('rm -r '.app_path('Http'));
        exec('rm '.database_path('migrations/*'));
        exec('rm -r '.resource_path('views/items'));
        exec('rm -r '.base_path('tests/Feature'));
        exec('rm -r '.base_path('tests/Unit'));
    }
}
