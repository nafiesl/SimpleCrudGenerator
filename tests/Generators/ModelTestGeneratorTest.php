<?php

namespace Tests\Generators;

use Tests\TestCase;

class ModelTestGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_unit_test_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $uniTestPath = base_path("tests/Unit/Models/{$this->model_name}Test.php");
        $this->assertFileExists($uniTestPath);
        $modelClassContent = "<?php

namespace Tests\Unit\Models;

use App\User;
use {$this->full_model_name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BrowserKitTest as TestCase;

class {$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_{$this->lang_name}_has_name_link_attribute()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$title = __('app.show_detail_title', [
            'name' => \${$this->single_model_var_name}->name, 'type' => __('{$this->lang_name}.{$this->lang_name}'),
        ]);
        \$link = '<a href=\"'.route('{$this->table_name}.show', \${$this->single_model_var_name}).'\"';
        \$link .= ' title=\"'.\$title.'\">';
        \$link .= \${$this->single_model_var_name}->name;
        \$link .= '</a>';

        \$this->assertEquals(\$link, \${$this->single_model_var_name}->name_link);
    }

    /** @test */
    public function a_{$this->lang_name}_has_belongs_to_creator_relation()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->make();

        \$this->assertInstanceOf(User::class, \${$this->single_model_var_name}->creator);
        \$this->assertEquals(\${$this->single_model_var_name}->creator_id, \${$this->single_model_var_name}->creator->id);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($uniTestPath));
    }

    /** @test */
    public function it_creates_correct_unit_test_class_with_base_test_class_based_on_config_file()
    {
        config(['simple-crud.base_test_path' => 'tests/MyTestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\MyTestCase']);

        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $uniTestPath = base_path("tests/Unit/Models/{$this->model_name}Test.php");
        $this->assertFileExists($uniTestPath);
        $modelClassContent = "<?php

namespace Tests\Unit\Models;

use App\User;
use {$this->full_model_name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\MyTestCase as TestCase;

class {$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_{$this->lang_name}_has_name_link_attribute()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$title = __('app.show_detail_title', [
            'name' => \${$this->single_model_var_name}->name, 'type' => __('{$this->lang_name}.{$this->lang_name}'),
        ]);
        \$link = '<a href=\"'.route('{$this->table_name}.show', \${$this->single_model_var_name}).'\"';
        \$link .= ' title=\"'.\$title.'\">';
        \$link .= \${$this->single_model_var_name}->name;
        \$link .= '</a>';

        \$this->assertEquals(\$link, \${$this->single_model_var_name}->name_link);
    }

    /** @test */
    public function a_{$this->lang_name}_has_belongs_to_creator_relation()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->make();

        \$this->assertInstanceOf(User::class, \${$this->single_model_var_name}->creator);
        \$this->assertEquals(\${$this->single_model_var_name}->creator_id, \${$this->single_model_var_name}->creator->id);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($uniTestPath));
    }

    /** @test */
    public function same_base_test_case_class_name_dont_use_alias()
    {
        config(['simple-crud.base_test_path' => 'tests/TestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\TestCase']);

        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $uniTestPath = base_path("tests/Unit/Models/{$this->model_name}Test.php");
        $this->assertFileExists($uniTestPath);
        $modelClassContent = "<?php

namespace Tests\Unit\Models;

use App\User;
use {$this->full_model_name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_{$this->lang_name}_has_name_link_attribute()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$title = __('app.show_detail_title', [
            'name' => \${$this->single_model_var_name}->name, 'type' => __('{$this->lang_name}.{$this->lang_name}'),
        ]);
        \$link = '<a href=\"'.route('{$this->table_name}.show', \${$this->single_model_var_name}).'\"';
        \$link .= ' title=\"'.\$title.'\">';
        \$link .= \${$this->single_model_var_name}->name;
        \$link .= '</a>';

        \$this->assertEquals(\$link, \${$this->single_model_var_name}->name_link);
    }

    /** @test */
    public function a_{$this->lang_name}_has_belongs_to_creator_relation()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->make();

        \$this->assertInstanceOf(User::class, \${$this->single_model_var_name}->creator);
        \$this->assertEquals(\${$this->single_model_var_name}->creator_id, \${$this->single_model_var_name}->creator->id);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($uniTestPath));
    }
}
