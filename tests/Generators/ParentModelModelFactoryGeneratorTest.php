<?php

namespace Tests\Generators;

use Tests\TestCase;

class ParentModelModelFactoryGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_model_factory_content()
    {
        config(['auth.providers.users.model' => 'App\Models\User']);
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $modelFactoryPath = database_path('factories/'.$this->model_name.'Factory.php');
        $this->assertFileExists($modelFactoryPath);
        $modelFactoryContent = "<?php

namespace Database\Factories;

use App\Models\User;
use {$this->full_model_name};
use {$this->full_parent_model_name};
use Illuminate\Database\Eloquent\Factories\Factory;

class {$this->model_name}Factory extends Factory
{
    protected \$model = {$this->model_name}::class;

    public function definition()
    {
        return [
            'title' => \$this->faker->word,
            'description' => \$this->faker->sentence,
            '{$this->parent_lang_name}_id' => function () {
                return {$this->parent_model_name}::factory()->create()->id;
            },
            'creator_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
";
        $this->assertEquals($modelFactoryContent, file_get_contents($modelFactoryPath));
    }

    /** @test */
    public function it_creates_correct_model_factory_content_with_namespaced_model()
    {
        $inputName = 'Entities/References/Category';
        $modelName = 'Category';
        $fullModelName = 'App\Entities\References\Category';
        $pluralModelName = 'Categories';
        $tableName = 'categories';
        $langName = 'category';
        $modelPath = 'Entities/References';
        $factoryNamespace = 'Entities\References';

        config(['auth.providers.users.model' => 'App\Entities\Users\User']);
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud-api', ['name' => $inputName, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $modelFactoryPath = database_path('factories/'.$inputName.'Factory.php');
        $this->assertFileExists($modelFactoryPath);
        $modelFactoryContent = "<?php

namespace Database\Factories\\{$factoryNamespace};

use App\Entities\Users\User;
use {$fullModelName};
use {$this->full_parent_model_name};
use Illuminate\Database\Eloquent\Factories\Factory;

class {$modelName}Factory extends Factory
{
    protected \$model = {$modelName}::class;

    public function definition()
    {
        return [
            'title' => \$this->faker->word,
            'description' => \$this->faker->sentence,
            '{$this->parent_lang_name}_id' => function () {
                return {$this->parent_model_name}::factory()->create()->id;
            },
            'creator_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
";
        $this->assertEquals($modelFactoryContent, file_get_contents($modelFactoryPath));
    }

    /** @test */
    public function it_creates_model_factory_file_content_from_published_stub()
    {
        app('files')->makeDirectory(base_path('stubs/simple-crud/database/factories'), 0777, true, true);
        app('files')->copy(
            __DIR__.'/../stubs/database/factories/model-factory.stub',
            base_path('stubs/simple-crud/database/factories/model-factory.stub')
        );
        config(['auth.providers.users.model' => 'App\Models\User']);
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $modelFactoryPath = database_path('factories/'.$this->model_name.'Factory.php');
        $this->assertFileExists($modelFactoryPath);
        $modelFactoryContent = "<?php

namespace Database\Factories;

use App\Models\User;
use {$this->full_model_name};
use {$this->full_parent_model_name};
use Illuminate\Database\Eloquent\Factories\Factory;

class {$this->model_name}Factory extends Factory
{
    protected \$model = {$this->model_name}::class;

    public function definition()
    {
        return [
            'title' => \$this->faker->word,
            'description' => \$this->faker->sentence,
            '{$this->parent_lang_name}_id' => function () {
                return {$this->parent_model_name}::factory()->create()->id;
            },
            'creator_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
";
        $this->assertEquals($modelFactoryContent, file_get_contents($modelFactoryPath));
        $this->removeFileOrDir(base_path('stubs'));
    }

    /** @test */
    public function it_doesnt_override_the_existing_model_factory_content()
    {
        $this->artisan('make:factory', ['name' => $this->model_name.'Factory', '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $modelFactoryPath = database_path('factories/'.$this->model_name.'Factory.php');
        $this->assertFileExists($modelFactoryPath);
        $modelFactoryContent = "<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Model>
 */
class {$this->model_name}Factory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }
}
";
        $this->assertEquals($modelFactoryContent, file_get_contents($modelFactoryPath));
    }
}
