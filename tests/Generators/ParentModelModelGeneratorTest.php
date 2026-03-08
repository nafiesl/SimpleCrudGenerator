<?php

namespace Tests\Generators;

use Tests\TestCase;

class ParentModelModelGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_model_class_content()
    {
        config(['auth.providers.users.model' => 'App\Models\User']);
        $this->artisan('make:model', ['name' => 'Models/'.$this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $modelPath = app_path('Models/'.$this->model_name.'.php');
        $this->assertFileExists($modelPath);
        $modelClassContent = "<?php

namespace App\Models;

use App\Models\User;
use {$this->full_parent_model_name};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$this->model_name} extends Model
{
    use HasFactory;

    protected \$fillable = ['title', 'description', 'creator_id'];

    public function creator()
    {
        return \$this->belongsTo(User::class);
    }

    public function {$this->single_parent_model_var_name}()
    {
        return \$this->belongsTo({$this->parent_model_name}::class);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($modelPath));
    }

    /** @test */
    public function it_creates_correct_namespaced_model_class_content()
    {
        config(['auth.providers.users.model' => 'App\Models\User']);
        $this->artisan('make:model', ['name' => 'Models/'.$this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => 'Entities/References/Category', '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $modelPath = app_path('Entities/References/Category.php');
        $this->assertFileExists($modelPath);
        $modelClassContent = "<?php

namespace App\Entities\References;

use App\Models\User;
use {$this->full_parent_model_name};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected \$fillable = ['title', 'description', 'creator_id'];

    public function creator()
    {
        return \$this->belongsTo(User::class);
    }

    public function {$this->single_parent_model_var_name}()
    {
        return \$this->belongsTo({$this->parent_model_name}::class);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($modelPath));

        // tearDown
        $this->removeFileOrDir(resource_path('views/categories'));
        $this->removeFileOrDir(resource_path("lang/en/category.php"));
    }

    /** @test */
    public function it_adds_parent_model_has_many_relations_to_the_new_model()
    {
        // dump($this->parent_model_name);
        $this->artisan('make:model', ['name' => 'Models/'.$this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);
        // dd('hits');
        $parentModelPath = app_path('Models/'.$this->parent_model_name.'.php');
        $relationToNewModel = "
    public function {$this->collection_model_var_name}()
    {
        return \$this->hasMany($this->model_name::class);
    }
";
        // dd('hit');
        $this->assertStringContainsString($relationToNewModel, file_get_contents($parentModelPath));
    }

    /** @test */
    public function it_doesnt_override_the_existing_model()
    {
        $this->mockConsoleOutput = true;
        config(['auth.providers.users.model' => 'App\Models\User']);
        $this->artisan('make:model', ['name' => 'Models/'.$this->model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true])
            ->expectsQuestion('Model file exists, are you sure to generate CRUD files?', true);

        $modelPath = app_path('Models/'.$this->model_name.'.php');
        $this->assertFileExists($modelPath);
        $modelClassContent = "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$this->model_name} extends Model
{
    use HasFactory;
}
";
        $this->assertEquals($modelClassContent, file_get_contents($modelPath));
    }
}
