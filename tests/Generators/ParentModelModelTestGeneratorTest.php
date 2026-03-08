<?php

namespace Tests\Generators;

use Tests\TestCase;

class ParentModelModelTestGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_unit_test_class_content()
    {
        config(['auth.providers.users.model' => 'App\Models\User']);
        $this->artisan('make:model', ['name' => 'Models/'.$this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $uniTestPath = base_path("tests/Unit/Models/{$this->model_name}Test.php");
        $this->assertFileExists($uniTestPath);
        $modelClassContent = "<?php

namespace Tests\Unit\Models;

use App\Models\User;
use {$this->full_model_name};
use {$this->full_parent_model_name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BrowserKitTest as TestCase;

class {$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_{$this->lang_name}_has_belongs_to_creator_relation()
    {
        \${$this->single_model_var_name} = {$this->model_name}::factory()->make();

        \$this->assertInstanceOf(User::class, \${$this->single_model_var_name}->creator);
        \$this->assertEquals(\${$this->single_model_var_name}->creator_id, \${$this->single_model_var_name}->creator->id);
    }

    /** @test */
    public function a_{$this->lang_name}_has_belongs_to_{$this->parent_lang_name}_relation()
    {
        \${$this->single_model_var_name} = {$this->model_name}::factory()->make();

        \$this->assertInstanceOf({$this->parent_model_name}::class, \${$this->single_model_var_name}->{$this->single_parent_model_var_name});
        \$this->assertEquals(\${$this->single_model_var_name}->{$this->parent_lang_name}_id, \${$this->single_model_var_name}->{$this->single_parent_model_var_name}->id);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($uniTestPath));
    }

    /** @test */
    public function it_creates_correct_unit_test_class_with_base_test_class_based_on_config_file()
    {
        config(['auth.providers.users.model' => 'App\Models\User']);
        config(['simple-crud.base_test_path' => 'tests/MyTestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\MyTestCase']);

        $this->artisan('make:model', ['name' => 'Models/'.$this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $uniTestPath = base_path("tests/Unit/Models/{$this->model_name}Test.php");
        $this->assertFileExists($uniTestPath);
        $modelClassContent = "<?php

namespace Tests\Unit\Models;

use App\Models\User;
use {$this->full_model_name};
use {$this->full_parent_model_name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\MyTestCase as TestCase;

class {$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_{$this->lang_name}_has_belongs_to_creator_relation()
    {
        \${$this->single_model_var_name} = {$this->model_name}::factory()->make();

        \$this->assertInstanceOf(User::class, \${$this->single_model_var_name}->creator);
        \$this->assertEquals(\${$this->single_model_var_name}->creator_id, \${$this->single_model_var_name}->creator->id);
    }

    /** @test */
    public function a_{$this->lang_name}_has_belongs_to_{$this->parent_lang_name}_relation()
    {
        \${$this->single_model_var_name} = {$this->model_name}::factory()->make();

        \$this->assertInstanceOf({$this->parent_model_name}::class, \${$this->single_model_var_name}->{$this->single_parent_model_var_name});
        \$this->assertEquals(\${$this->single_model_var_name}->{$this->parent_lang_name}_id, \${$this->single_model_var_name}->{$this->single_parent_model_var_name}->id);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($uniTestPath));
    }

    /** @test */
    public function same_base_test_case_class_title_dont_use_alias()
    {
        config(['auth.providers.users.model' => 'App\Models\User']);
        config(['simple-crud.base_test_path' => 'tests/TestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\TestCase']);

        $this->artisan('make:model', ['name' => 'Models/'.$this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $uniTestPath = base_path("tests/Unit/Models/{$this->model_name}Test.php");
        $this->assertFileExists($uniTestPath);
        $modelClassContent = "<?php

namespace Tests\Unit\Models;

use App\Models\User;
use {$this->full_model_name};
use {$this->full_parent_model_name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_{$this->lang_name}_has_belongs_to_creator_relation()
    {
        \${$this->single_model_var_name} = {$this->model_name}::factory()->make();

        \$this->assertInstanceOf(User::class, \${$this->single_model_var_name}->creator);
        \$this->assertEquals(\${$this->single_model_var_name}->creator_id, \${$this->single_model_var_name}->creator->id);
    }

    /** @test */
    public function a_{$this->lang_name}_has_belongs_to_{$this->parent_lang_name}_relation()
    {
        \${$this->single_model_var_name} = {$this->model_name}::factory()->make();

        \$this->assertInstanceOf({$this->parent_model_name}::class, \${$this->single_model_var_name}->{$this->single_parent_model_var_name});
        \$this->assertEquals(\${$this->single_model_var_name}->{$this->parent_lang_name}_id, \${$this->single_model_var_name}->{$this->single_parent_model_var_name}->id);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($uniTestPath));
    }
}
