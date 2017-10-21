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
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\BrowserKitTest as TestCase;

class {$this->model_name}Test extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_create_{$this->lang_name}()
    {
        \$user = \$this->loginAsUser();
        \$this->assertTrue(\$user->can('create', new {$this->model_name}));
    }

    /** @test */
    public function user_can_view_{$this->lang_name}()
    {
        \$user = \$this->loginAsUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => '{$this->model_name} 1 name']);
        \$this->assertTrue(\$user->can('view', \${$this->single_model_var_name}));
    }

    /** @test */
    public function user_can_update_{$this->lang_name}()
    {
        \$user = \$this->loginAsUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => '{$this->model_name} 1 name']);
        \$this->assertTrue(\$user->can('update', \${$this->single_model_var_name}));
    }

    /** @test */
    public function user_can_delete_{$this->lang_name}()
    {
        \$user = \$this->loginAsUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => '{$this->model_name} 1 name']);
        \$this->assertTrue(\$user->can('delete', \${$this->single_model_var_name}));
    }
}
";
        $this->assertEquals($modelPolicyContent, file_get_contents($modelPolicyPath));
    }
}
