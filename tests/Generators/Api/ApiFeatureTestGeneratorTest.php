<?php

namespace Tests\Generators\Api;

use Tests\TestCase;

class ApiFeatureTestGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_feature_test_class_content()
    {
        $this->artisan('make:crud-api', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists(base_path("tests/Feature/Api/Manage{$this->model_name}Test.php"));
        $featureTestClassContent = "<?php

namespace Tests\Feature\Api;

use {$this->full_model_name};
use Tests\BrowserKitTest as TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Manage{$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_{$this->lang_name}_list_in_{$this->lang_name}_index_page()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$this->getJson(route('api.{$this->table_name}.index'), [
            'Authorization' => 'Bearer '.\$user->api_token
        ]);

        \$this->seeJson(['name' => \${$this->single_model_var_name}->name]);
    }

    /** @test */
    public function user_can_create_a_{$this->lang_name}()
    {
        \$user = \$this->createUser();

        \$this->postJson(route('api.{$this->table_name}.store'), [
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], [
            'Authorization' => 'Bearer '.\$user->api_token
        ]);

        \$this->seeInDatabase('{$this->table_name}', [
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ]);

        \$this->seeStatusCode(201);
        \$this->seeJson([
            'message'     => __('{$this->lang_name}.created'),
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ]);
    }

    private function getCreateFields(array \$overrides = [])
    {
        return array_merge([
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], \$overrides);
    }

    /** @test */
    public function validate_{$this->lang_name}_name_is_required()
    {
        \$user = \$this->createUser();

        // name empty
        \$requestBody = \$this->getCreateFields(['name' => '']);
        \$this->postJson(
            route('api.{$this->table_name}.store'),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['name' => []]]);
    }

    /** @test */
    public function validate_{$this->lang_name}_name_is_not_more_than_60_characters()
    {
        \$user = \$this->createUser();

        // name 70 characters
        \$requestBody = \$this->getCreateFields(['name' => str_repeat('Test Title', 7)]);
        \$this->postJson(
            route('api.{$this->table_name}.store'),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['name' => []]]);
    }

    /** @test */
    public function validate_{$this->lang_name}_description_is_not_more_than_255_characters()
    {
        \$user = \$this->createUser();

        // description 256 characters
        \$requestBody = \$this->getCreateFields(['description' => str_repeat('Long description', 16)]);
        \$this->postJson(
            route('api.{$this->table_name}.store'),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['description' => []]]);
    }

    /** @test */
    public function user_can_get_a_{$this->lang_name}_detail()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        \$this->getJson(route('api.{$this->table_name}.show', \${$this->single_model_var_name}), [
            'Authorization' => 'Bearer '.\$user->api_token
        ]);

        \$this->seeJson(['name' => 'Testing 123']);
    }

    /** @test */
    public function user_can_update_a_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        \$this->patchJson(route('api.{$this->table_name}.update', \${$this->single_model_var_name}), [
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], [
            'Authorization' => 'Bearer '.\$user->api_token
        ]);

        \$this->seeInDatabase('{$this->table_name}', [
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ]);

        \$this->seeStatusCode(200);
        \$this->seeJson([
            'message'     => __('{$this->lang_name}.updated'),
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ]);
    }

    private function getEditFields(array \$overrides = [])
    {
        return array_merge([
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], \$overrides);
    }

    /** @test */
    public function validate_{$this->lang_name}_name_update_is_required()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        // name empty
        \$requestBody = \$this->getEditFields(['name' => '']);
        \$this->patchJson(
            route('api.{$this->table_name}.update', \${$this->single_model_var_name}),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['name' => []]]);
    }

    /** @test */
    public function validate_{$this->lang_name}_name_update_is_not_more_than_60_characters()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        // name 70 characters
        \$requestBody = \$this->getEditFields(['name' => str_repeat('Test Title', 7)]);
        \$this->patchJson(
            route('api.{$this->table_name}.update', \${$this->single_model_var_name}),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['name' => []]]);
    }

    /** @test */
    public function validate_{$this->lang_name}_description_update_is_not_more_than_255_characters()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // description 256 characters
        \$requestBody = \$this->getEditFields(['description' => str_repeat('Long description', 16)]);
        \$this->patchJson(
            route('api.{$this->table_name}.update', \${$this->single_model_var_name}),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['description' => []]]);
    }

    /** @test */
    public function user_can_delete_a_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$this->deleteJson(route('api.{$this->table_name}.destroy', \${$this->single_model_var_name}), [
            '{$this->lang_name}_id' => \${$this->single_model_var_name}->id,
        ], [
            'Authorization' => 'Bearer '.\$user->api_token
        ]);

        \$this->dontSeeInDatabase('{$this->table_name}', [
            'id' => \${$this->single_model_var_name}->id,
        ]);

        \$this->seeStatusCode(200);
        \$this->seeJson([
            'message' => __('{$this->lang_name}.deleted'),
        ]);
    }
}
";
        $this->assertEquals($featureTestClassContent, file_get_contents(base_path("tests/Feature/Api/Manage{$this->model_name}Test.php")));
    }

    /** @test */
    public function it_creates_correct_feature_test_class_with_base_test_class_based_on_config_file()
    {
        config(['simple-crud.base_test_path' => 'tests/TestCase.php']);
        config(['simple-crud.base_test_class' => 'Tests\TestCase']);

        $this->artisan('make:crud-api', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists(base_path("tests/Feature/Api/Manage{$this->model_name}Test.php"));
        $featureTestClassContent = "<?php

namespace Tests\Feature\Api;

use {$this->full_model_name};
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Manage{$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_see_{$this->lang_name}_list_in_{$this->lang_name}_index_page()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$this->getJson(route('api.{$this->table_name}.index'), [
            'Authorization' => 'Bearer '.\$user->api_token
        ]);

        \$this->seeJson(['name' => \${$this->single_model_var_name}->name]);
    }

    /** @test */
    public function user_can_create_a_{$this->lang_name}()
    {
        \$user = \$this->createUser();

        \$this->postJson(route('api.{$this->table_name}.store'), [
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], [
            'Authorization' => 'Bearer '.\$user->api_token
        ]);

        \$this->seeInDatabase('{$this->table_name}', [
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ]);

        \$this->seeStatusCode(201);
        \$this->seeJson([
            'message'     => __('{$this->lang_name}.created'),
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ]);
    }

    private function getCreateFields(array \$overrides = [])
    {
        return array_merge([
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], \$overrides);
    }

    /** @test */
    public function validate_{$this->lang_name}_name_is_required()
    {
        \$user = \$this->createUser();

        // name empty
        \$requestBody = \$this->getCreateFields(['name' => '']);
        \$this->postJson(
            route('api.{$this->table_name}.store'),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['name' => []]]);
    }

    /** @test */
    public function validate_{$this->lang_name}_name_is_not_more_than_60_characters()
    {
        \$user = \$this->createUser();

        // name 70 characters
        \$requestBody = \$this->getCreateFields(['name' => str_repeat('Test Title', 7)]);
        \$this->postJson(
            route('api.{$this->table_name}.store'),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['name' => []]]);
    }

    /** @test */
    public function validate_{$this->lang_name}_description_is_not_more_than_255_characters()
    {
        \$user = \$this->createUser();

        // description 256 characters
        \$requestBody = \$this->getCreateFields(['description' => str_repeat('Long description', 16)]);
        \$this->postJson(
            route('api.{$this->table_name}.store'),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['description' => []]]);
    }

    /** @test */
    public function user_can_get_a_{$this->lang_name}_detail()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        \$this->getJson(route('api.{$this->table_name}.show', \${$this->single_model_var_name}), [
            'Authorization' => 'Bearer '.\$user->api_token
        ]);

        \$this->seeJson(['name' => 'Testing 123']);
    }

    /** @test */
    public function user_can_update_a_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        \$this->patchJson(route('api.{$this->table_name}.update', \${$this->single_model_var_name}), [
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], [
            'Authorization' => 'Bearer '.\$user->api_token
        ]);

        \$this->seeInDatabase('{$this->table_name}', [
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ]);

        \$this->seeStatusCode(200);
        \$this->seeJson([
            'message'     => __('{$this->lang_name}.updated'),
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ]);
    }

    private function getEditFields(array \$overrides = [])
    {
        return array_merge([
            'name'        => '{$this->model_name} 1 name',
            'description' => '{$this->model_name} 1 description',
        ], \$overrides);
    }

    /** @test */
    public function validate_{$this->lang_name}_name_update_is_required()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        // name empty
        \$requestBody = \$this->getEditFields(['name' => '']);
        \$this->patchJson(
            route('api.{$this->table_name}.update', \${$this->single_model_var_name}),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['name' => []]]);
    }

    /** @test */
    public function validate_{$this->lang_name}_name_update_is_not_more_than_60_characters()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        // name 70 characters
        \$requestBody = \$this->getEditFields(['name' => str_repeat('Test Title', 7)]);
        \$this->patchJson(
            route('api.{$this->table_name}.update', \${$this->single_model_var_name}),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['name' => []]]);
    }

    /** @test */
    public function validate_{$this->lang_name}_description_update_is_not_more_than_255_characters()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create(['name' => 'Testing 123']);

        // description 256 characters
        \$requestBody = \$this->getEditFields(['description' => str_repeat('Long description', 16)]);
        \$this->patchJson(
            route('api.{$this->table_name}.update', \${$this->single_model_var_name}),
            \$requestBody,
            ['Authorization' => 'Bearer '.\$user->api_token]
        );

        \$this->seeStatusCode(422);
        \$this->seeJsonSubset(['errors' => ['description' => []]]);
    }

    /** @test */
    public function user_can_delete_a_{$this->lang_name}()
    {
        \$user = \$this->createUser();
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$this->deleteJson(route('api.{$this->table_name}.destroy', \${$this->single_model_var_name}), [
            '{$this->lang_name}_id' => \${$this->single_model_var_name}->id,
        ], [
            'Authorization' => 'Bearer '.\$user->api_token
        ]);

        \$this->dontSeeInDatabase('{$this->table_name}', [
            'id' => \${$this->single_model_var_name}->id,
        ]);

        \$this->seeStatusCode(200);
        \$this->seeJson([
            'message' => __('{$this->lang_name}.deleted'),
        ]);
    }
}
";
        $this->assertEquals($featureTestClassContent, file_get_contents(base_path("tests/Feature/Api/Manage{$this->model_name}Test.php")));
    }
}
