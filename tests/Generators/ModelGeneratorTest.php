<?php

namespace Tests\Generators;

use Tests\TestCase;

class ModelGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_model_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $modelPath = app_path($this->modelName.'.php');
        $this->assertFileExists($modelPath);
        $modelClassContent = "<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
}
";
        $this->assertEquals($modelClassContent, file_get_contents($modelPath));
    }
}
