<?php

namespace Tests\Generators;

use Tests\TestCase;

class ModelFactoryGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_model_factory_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $modelFactoryPath = database_path('factories/'.$this->model_name.'Factory.php');
        $this->assertFileExists($modelFactoryPath);
        $modelFactoryContent = "<?php

use App\User;
use {$this->full_model_name};
use Faker\Generator as Faker;

\$factory->define({$this->model_name}::class, function (Faker \$faker) {

    return [
        'name' => \$faker->word,
        'description' => \$faker->sentence,
        'creator_id' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
";
        $this->assertEquals($modelFactoryContent, file_get_contents($modelFactoryPath));
    }
}
