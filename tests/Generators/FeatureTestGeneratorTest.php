<?php

namespace Tests\Generators;

use Tests\TestCase;

class FeatureTestGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_browser_kit_base_test_class()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists(base_path("tests/BrowserKitTest.php"));
        $browserKitTestClassContent = "<?php

namespace Tests;

use App\User;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class BrowserKitTest extends BaseTestCase
{
    use CreatesApplication;

    protected \$baseUrl = 'http://localhost';

    protected function loginAsUser()
    {
        \$user = \$this->createUser();
        \$this->actingAs(\$user);

        return \$user;
    }

    protected function createUser()
    {
        return factory(User::class)->create();
    }
}
";
        $this->assertEquals($browserKitTestClassContent, file_get_contents(base_path("tests/BrowserKitTest.php")));
    }

    /** @test */
    public function it_creates_correct_feature_test_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists(base_path("tests/Feature/Manage{$this->model_name}Test.php"));
        $modelClassContent = "<?php

namespace Tests\Feature;

use {$this->full_model_name};
use Tests\BrowserKitTest as TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Manage{$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_{$this->lang_name}_list_in_{$this->lang_name}_index_page()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$this->loginAsUser();
        \$this->visitRoute('{$this->table_name}.index');
        \$this->see(\${$this->single_model_var_name}->name);
    }

    private function getCreateFields(array \$overrides = [])
    {
        return array_merge([
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], \$overrides);
    }

    /** @test */
    public function user_can_create_a_{$this->lang_name}()
    {
        \$this->loginAsUser();
        \$this->visitRoute('{$this->table_name}.index');

        \$this->click(__('{$this->lang_name}.create'));
        \$this->seeRouteIs('{$this->table_name}.create');

        \$this->submitForm(__('{$this->lang_name}.create'), \$this->getCreateFields());

        \$this->seeRouteIs('{$this->table_name}.show', {$this->model_name}::first());

        \$this->seeInDatabase('{$this->table_name}', \$this->getCreateFields());
    }

    /** @test */
    public function validate_{$this->lang_name}_name_is_required()
    {
        \$this->loginAsUser();

        // name empty
        \$this->post(route('{$this->table_name}.store'), \$this->getCreateFields(['name' => '']));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_name_is_not_more_than_60_characters()
    {
        \$this->loginAsUser();

        // name 70 characters
        \$this->post(route('{$this->table_name}.store'), \$this->getCreateFields([
            'name' => str_repeat('Test Title', 7),
        ]));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_description_is_not_more_than_255_characters()
    {
        \$this->loginAsUser();

        // description 256 characters
        \$this->post(route('{$this->table_name}.store'), \$this->getCreateFields([
            'description' => str_repeat('Long description', 16),
        ]));
        \$this->assertSessionHasErrors('description');
    }

    private function getEditFields(array \$overrides = [])
    {
        return array_merge([
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], \$overrides);
    }

    /** @test */
    public function user_can_edit_a_{$this->lang_name}()
    {
        \$this->loginAsUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        \$this->visitRoute('{$this->table_name}.show', \${$this->single_model_var_name});
        \$this->click('edit-{$this->lang_name}-'.\${$this->single_model_var_name}->id);
        \$this->seeRouteIs('{$this->table_name}.edit', \${$this->single_model_var_name});

        \$this->submitForm(__('{$this->lang_name}.update'), \$this->getEditFields());

        \$this->seeRouteIs('{$this->table_name}.show', \${$this->single_model_var_name});

        \$this->seeInDatabase('{$this->table_name}', \$this->getEditFields([
            'id' => \${$this->single_model_var_name}->id,
        ]));
    }

    /** @test */
    public function validate_{$this->lang_name}_name_update_is_required()
    {
        \$this->loginAsUser();
        \${$this->lang_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // name empty
        \$this->patch(route('{$this->table_name}.update', \${$this->lang_name}), \$this->getEditFields(['name' => '']));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_name_update_is_not_more_than_60_characters()
    {
        \$this->loginAsUser();
        \${$this->lang_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // name 70 characters
        \$this->patch(route('{$this->table_name}.update', \${$this->lang_name}), \$this->getEditFields([
            'name' => str_repeat('Test Title', 7),
        ]));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_description_update_is_not_more_than_255_characters()
    {
        \$this->loginAsUser();
        \${$this->lang_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // description 256 characters
        \$this->patch(route('{$this->table_name}.update', \${$this->lang_name}), \$this->getEditFields([
            'description' => str_repeat('Long description', 16),
        ]));
        \$this->assertSessionHasErrors('description');
    }

    /** @test */
    public function user_can_delete_a_{$this->lang_name}()
    {
        \$this->loginAsUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        factory({$this->model_name}::class)->create();

        \$this->visitRoute('{$this->table_name}.edit', \${$this->single_model_var_name});
        \$this->click('del-{$this->lang_name}-'.\${$this->single_model_var_name}->id);
        \$this->seeRouteIs('{$this->table_name}.edit', [\${$this->single_model_var_name}, 'action' => 'delete']);

        \$this->press(__('app.delete_confirm_button'));

        \$this->dontSeeInDatabase('{$this->table_name}', [
            'id' => \${$this->single_model_var_name}->id,
        ]);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents(base_path("tests/Feature/Manage{$this->model_name}Test.php")));
    }

    /** @test */
    public function it_generates_base_test_class_based_on_config_file()
    {
        config(['simple-crud.base_test_path' => 'tests/TestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\TestCase']);

        $baseTestPath = base_path('tests/TestCase.php');
        $baseTestClass = 'TestCase';

        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists($baseTestPath);
        $browserKitTestClassContent = "<?php

namespace Tests;

use App\User;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class {$baseTestClass} extends BaseTestCase
{
    use CreatesApplication;

    protected \$baseUrl = 'http://localhost';

    protected function loginAsUser()
    {
        \$user = \$this->createUser();
        \$this->actingAs(\$user);

        return \$user;
    }

    protected function createUser()
    {
        return factory(User::class)->create();
    }
}
";
        $this->assertEquals($browserKitTestClassContent, file_get_contents($baseTestPath));
    }

    /** @test */
    public function it_creates_correct_feature_test_class_with_base_test_class_based_on_config_file()
    {
        config(['simple-crud.base_test_path' => 'tests/MyTestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\MyTestCase']);

        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists(base_path("tests/Feature/Manage{$this->model_name}Test.php"));
        $modelClassContent = "<?php

namespace Tests\Feature;

use {$this->full_model_name};
use Tests\MyTestCase as TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Manage{$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_{$this->lang_name}_list_in_{$this->lang_name}_index_page()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$this->loginAsUser();
        \$this->visitRoute('{$this->table_name}.index');
        \$this->see(\${$this->single_model_var_name}->name);
    }

    private function getCreateFields(array \$overrides = [])
    {
        return array_merge([
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], \$overrides);
    }

    /** @test */
    public function user_can_create_a_{$this->lang_name}()
    {
        \$this->loginAsUser();
        \$this->visitRoute('{$this->table_name}.index');

        \$this->click(__('{$this->lang_name}.create'));
        \$this->seeRouteIs('{$this->table_name}.create');

        \$this->submitForm(__('{$this->lang_name}.create'), \$this->getCreateFields());

        \$this->seeRouteIs('{$this->table_name}.show', {$this->model_name}::first());

        \$this->seeInDatabase('{$this->table_name}', \$this->getCreateFields());
    }

    /** @test */
    public function validate_{$this->lang_name}_name_is_required()
    {
        \$this->loginAsUser();

        // name empty
        \$this->post(route('{$this->table_name}.store'), \$this->getCreateFields(['name' => '']));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_name_is_not_more_than_60_characters()
    {
        \$this->loginAsUser();

        // name 70 characters
        \$this->post(route('{$this->table_name}.store'), \$this->getCreateFields([
            'name' => str_repeat('Test Title', 7),
        ]));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_description_is_not_more_than_255_characters()
    {
        \$this->loginAsUser();

        // description 256 characters
        \$this->post(route('{$this->table_name}.store'), \$this->getCreateFields([
            'description' => str_repeat('Long description', 16),
        ]));
        \$this->assertSessionHasErrors('description');
    }

    private function getEditFields(array \$overrides = [])
    {
        return array_merge([
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], \$overrides);
    }

    /** @test */
    public function user_can_edit_a_{$this->lang_name}()
    {
        \$this->loginAsUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        \$this->visitRoute('{$this->table_name}.show', \${$this->single_model_var_name});
        \$this->click('edit-{$this->lang_name}-'.\${$this->single_model_var_name}->id);
        \$this->seeRouteIs('{$this->table_name}.edit', \${$this->single_model_var_name});

        \$this->submitForm(__('{$this->lang_name}.update'), \$this->getEditFields());

        \$this->seeRouteIs('{$this->table_name}.show', \${$this->single_model_var_name});

        \$this->seeInDatabase('{$this->table_name}', \$this->getEditFields([
            'id' => \${$this->single_model_var_name}->id,
        ]));
    }

    /** @test */
    public function validate_{$this->lang_name}_name_update_is_required()
    {
        \$this->loginAsUser();
        \${$this->lang_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // name empty
        \$this->patch(route('{$this->table_name}.update', \${$this->lang_name}), \$this->getEditFields(['name' => '']));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_name_update_is_not_more_than_60_characters()
    {
        \$this->loginAsUser();
        \${$this->lang_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // name 70 characters
        \$this->patch(route('{$this->table_name}.update', \${$this->lang_name}), \$this->getEditFields([
            'name' => str_repeat('Test Title', 7),
        ]));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_description_update_is_not_more_than_255_characters()
    {
        \$this->loginAsUser();
        \${$this->lang_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // description 256 characters
        \$this->patch(route('{$this->table_name}.update', \${$this->lang_name}), \$this->getEditFields([
            'description' => str_repeat('Long description', 16),
        ]));
        \$this->assertSessionHasErrors('description');
    }

    /** @test */
    public function user_can_delete_a_{$this->lang_name}()
    {
        \$this->loginAsUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        factory({$this->model_name}::class)->create();

        \$this->visitRoute('{$this->table_name}.edit', \${$this->single_model_var_name});
        \$this->click('del-{$this->lang_name}-'.\${$this->single_model_var_name}->id);
        \$this->seeRouteIs('{$this->table_name}.edit', [\${$this->single_model_var_name}, 'action' => 'delete']);

        \$this->press(__('app.delete_confirm_button'));

        \$this->dontSeeInDatabase('{$this->table_name}', [
            'id' => \${$this->single_model_var_name}->id,
        ]);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents(base_path("tests/Feature/Manage{$this->model_name}Test.php")));
    }

    /** @test */
    public function same_base_test_case_class_name_dont_use_alias()
    {
        config(['simple-crud.base_test_path' => 'tests/TestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\TestCase']);

        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists(base_path("tests/Feature/Manage{$this->model_name}Test.php"));
        $modelClassContent = "<?php

namespace Tests\Feature;

use {$this->full_model_name};
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Manage{$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_{$this->lang_name}_list_in_{$this->lang_name}_index_page()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$this->loginAsUser();
        \$this->visitRoute('{$this->table_name}.index');
        \$this->see(\${$this->single_model_var_name}->name);
    }

    private function getCreateFields(array \$overrides = [])
    {
        return array_merge([
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], \$overrides);
    }

    /** @test */
    public function user_can_create_a_{$this->lang_name}()
    {
        \$this->loginAsUser();
        \$this->visitRoute('{$this->table_name}.index');

        \$this->click(__('{$this->lang_name}.create'));
        \$this->seeRouteIs('{$this->table_name}.create');

        \$this->submitForm(__('{$this->lang_name}.create'), \$this->getCreateFields());

        \$this->seeRouteIs('{$this->table_name}.show', {$this->model_name}::first());

        \$this->seeInDatabase('{$this->table_name}', \$this->getCreateFields());
    }

    /** @test */
    public function validate_{$this->lang_name}_name_is_required()
    {
        \$this->loginAsUser();

        // name empty
        \$this->post(route('{$this->table_name}.store'), \$this->getCreateFields(['name' => '']));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_name_is_not_more_than_60_characters()
    {
        \$this->loginAsUser();

        // name 70 characters
        \$this->post(route('{$this->table_name}.store'), \$this->getCreateFields([
            'name' => str_repeat('Test Title', 7),
        ]));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_description_is_not_more_than_255_characters()
    {
        \$this->loginAsUser();

        // description 256 characters
        \$this->post(route('{$this->table_name}.store'), \$this->getCreateFields([
            'description' => str_repeat('Long description', 16),
        ]));
        \$this->assertSessionHasErrors('description');
    }

    private function getEditFields(array \$overrides = [])
    {
        return array_merge([
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], \$overrides);
    }

    /** @test */
    public function user_can_edit_a_{$this->lang_name}()
    {
        \$this->loginAsUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        \$this->visitRoute('{$this->table_name}.show', \${$this->single_model_var_name});
        \$this->click('edit-{$this->lang_name}-'.\${$this->single_model_var_name}->id);
        \$this->seeRouteIs('{$this->table_name}.edit', \${$this->single_model_var_name});

        \$this->submitForm(__('{$this->lang_name}.update'), \$this->getEditFields());

        \$this->seeRouteIs('{$this->table_name}.show', \${$this->single_model_var_name});

        \$this->seeInDatabase('{$this->table_name}', \$this->getEditFields([
            'id' => \${$this->single_model_var_name}->id,
        ]));
    }

    /** @test */
    public function validate_{$this->lang_name}_name_update_is_required()
    {
        \$this->loginAsUser();
        \${$this->lang_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // name empty
        \$this->patch(route('{$this->table_name}.update', \${$this->lang_name}), \$this->getEditFields(['name' => '']));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_name_update_is_not_more_than_60_characters()
    {
        \$this->loginAsUser();
        \${$this->lang_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // name 70 characters
        \$this->patch(route('{$this->table_name}.update', \${$this->lang_name}), \$this->getEditFields([
            'name' => str_repeat('Test Title', 7),
        ]));
        \$this->assertSessionHasErrors('name');
    }

    /** @test */
    public function validate_{$this->lang_name}_description_update_is_not_more_than_255_characters()
    {
        \$this->loginAsUser();
        \${$this->lang_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // description 256 characters
        \$this->patch(route('{$this->table_name}.update', \${$this->lang_name}), \$this->getEditFields([
            'description' => str_repeat('Long description', 16),
        ]));
        \$this->assertSessionHasErrors('description');
    }

    /** @test */
    public function user_can_delete_a_{$this->lang_name}()
    {
        \$this->loginAsUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();
        factory({$this->model_name}::class)->create();

        \$this->visitRoute('{$this->table_name}.edit', \${$this->single_model_var_name});
        \$this->click('del-{$this->lang_name}-'.\${$this->single_model_var_name}->id);
        \$this->seeRouteIs('{$this->table_name}.edit', [\${$this->single_model_var_name}, 'action' => 'delete']);

        \$this->press(__('app.delete_confirm_button'));

        \$this->dontSeeInDatabase('{$this->table_name}', [
            'id' => \${$this->single_model_var_name}->id,
        ]);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents(base_path("tests/Feature/Manage{$this->model_name}Test.php")));
    }
}
