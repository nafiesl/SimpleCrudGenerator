<?php

namespace Tests\Generators;

use Tests\TestCase;

class ModelFactoryGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_model_factory_content()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $modelFactoryPath = database_path('factories/'.$this->modelName.'Factory.php');
        $this->assertFileExists($modelFactoryPath);
        $modelFactoryContent = "<?php

use App\Item;
use Faker\Generator as Faker;

\$factory->define(Item::class, function (Faker \$faker) {

    return [
        'name' => \$faker->word,
        'description' => \$faker->sentence,
    ];
});
";
        $this->assertEquals($modelFactoryContent, file_get_contents($modelFactoryPath));
    }
}
