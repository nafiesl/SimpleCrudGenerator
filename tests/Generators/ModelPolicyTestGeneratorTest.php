<?php

namespace Tests\Generators;

use Tests\TestCase;

class ModelPolicyTestGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_model_policy_test_content()
    {
        $userModel = config('auth.providers.users.model');

        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $modelPolicyPath = base_path("tests/Unit/Policies/{$this->model_name}PolicyTest.php");
        $this->assertFileExists($modelPolicyPath);

        $modelPolicyContent = "<?php

namespace Tests\Unit\Policies;

use {$this->full_model_name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BrowserKitTest as TestCase;

class {$this->model_name}PolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \$this->assertTrue(\$user->can('create', new {$this->model_name}));
    }

    /** @test */
    public function user_can_view_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        \$this->assertTrue(\$user->can('view', \${$this->single_model_var_name}));
    }

    /** @test */
    public function user_can_update_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        \$this->assertTrue(\$user->can('update', \${$this->single_model_var_name}));
    }

    /** @test */
    public function user_can_delete_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        \$this->assertTrue(\$user->can('delete', \${$this->single_model_var_name}));
    }
}
";
        $this->assertEquals($modelPolicyContent, file_get_contents($modelPolicyPath));
    }

    /** @test */
    public function it_creates_correct_model_policy_test_class_with_base_test_class_based_on_config_file()
    {
        config(['simple-crud.base_test_path' => 'tests/MyTestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\MyTestCase']);

        $userModel = config('auth.providers.users.model');

        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $modelPolicyPath = base_path("tests/Unit/Policies/{$this->model_name}PolicyTest.php");
        $this->assertFileExists($modelPolicyPath);

        $modelPolicyContent = "<?php

namespace Tests\Unit\Policies;

use {$this->full_model_name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\MyTestCase as TestCase;

class {$this->model_name}PolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \$this->assertTrue(\$user->can('create', new {$this->model_name}));
    }

    /** @test */
    public function user_can_view_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        \$this->assertTrue(\$user->can('view', \${$this->single_model_var_name}));
    }

    /** @test */
    public function user_can_update_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        \$this->assertTrue(\$user->can('update', \${$this->single_model_var_name}));
    }

    /** @test */
    public function user_can_delete_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        \$this->assertTrue(\$user->can('delete', \${$this->single_model_var_name}));
    }
}
";
        $this->assertEquals($modelPolicyContent, file_get_contents($modelPolicyPath));
    }

    /** @test */
    public function same_base_test_case_class_name_dont_use_alias()
    {
        config(['simple-crud.base_test_path' => 'tests/TestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\TestCase']);

        $userModel = config('auth.providers.users.model');

        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $modelPolicyPath = base_path("tests/Unit/Policies/{$this->model_name}PolicyTest.php");
        $this->assertFileExists($modelPolicyPath);

        $modelPolicyContent = "<?php

namespace Tests\Unit\Policies;

use {$this->full_model_name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {$this->model_name}PolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_create_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \$this->assertTrue(\$user->can('create', new {$this->model_name}));
    }

    /** @test */
    public function user_can_view_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        \$this->assertTrue(\$user->can('view', \${$this->single_model_var_name}));
    }

    /** @test */
    public function user_can_update_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        \$this->assertTrue(\$user->can('update', \${$this->single_model_var_name}));
    }

    /** @test */
    public function user_can_delete_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        \$this->assertTrue(\$user->can('delete', \${$this->single_model_var_name}));
    }
}
";
        $this->assertEquals($modelPolicyContent, file_get_contents($modelPolicyPath));
    }
}
