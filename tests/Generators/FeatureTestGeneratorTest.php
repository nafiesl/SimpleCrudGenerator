<?php

namespace Tests\Generators;

use Tests\TestCase;

class FeatureTestGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_browser_kit_base_test_class()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $this->assertFileExists(base_path("tests/BrowserKitTest.php"));
        $browserKitTestClassContent = "<?php

namespace Tests;

use App\User;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class BrowserKitTest extends BaseTestCase
{
    use CreatesApplication;

    protected \$baseUrl = 'http://localhost';

    protected function setUp()
    {
        parent::setUp();
        \Hash::setRounds(5);
    }

    protected function loginAsUser()
    {
        \$user = factory(User::class)->create();
        \$this->actingAs(\$user);

        return \$user;
    }
}
";
        $this->assertEquals($browserKitTestClassContent, file_get_contents(base_path("tests/BrowserKitTest.php")));
    }

    /** @test */
    public function it_creates_correct_feature_test_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $this->assertFileExists(base_path("tests/Feature/Manage{$this->pluralModelName}Test.php"));
        $modelClassContent = "<?php

namespace Tests\Feature;

use App\\{$this->modelName};
use Tests\BrowserKitTest as TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Manage{$this->pluralModelName}Test extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_see_{$this->singleModelName}_list_in_{$this->singleModelName}_index_page()
    {
        \${$this->singleModelName}1 = factory({$this->modelName}::class)->create(['name' => 'Testing name', 'description' => 'Testing 123']);
        \${$this->singleModelName}2 = factory({$this->modelName}::class)->create(['name' => 'Testing name', 'description' => 'Testing 456']);

        \$this->loginAsUser();
        \$this->visit(route('{$this->tableName}.index'));
        \$this->see(\${$this->singleModelName}1->name);
        \$this->see(\${$this->singleModelName}2->name);
    }

    /** @test */
    public function user_can_create_a_{$this->singleModelName}()
    {
        \$this->loginAsUser();
        \$this->visit(route('{$this->tableName}.index'));

        \$this->click(trans('{$this->singleModelName}.create'));
        \$this->seePageIs(route('{$this->tableName}.index', ['action' => 'create']));

        \$this->type('{$this->modelName} 1 name', 'name');
        \$this->type('{$this->modelName} 1 description', 'description');
        \$this->press(trans('{$this->singleModelName}.create'));

        \$this->seePageIs(route('{$this->tableName}.index'));

        \$this->seeInDatabase('{$this->tableName}', [
            'name'   => '{$this->modelName} 1 name',
            'description'   => '{$this->modelName} 1 description',
        ]);
    }

    /** @test */
    public function user_can_edit_a_{$this->singleModelName}_within_search_query()
    {
        \$this->loginAsUser();
        \${$this->singleModelName} = factory({$this->modelName}::class)->create(['name' => 'Testing 123']);

        \$this->visit(route('{$this->tableName}.index', ['q' => '123']));
        \$this->click('edit-{$this->singleModelName}-'.\${$this->singleModelName}->id);
        \$this->seePageIs(route('{$this->tableName}.index', ['action' => 'edit', 'id' => \${$this->singleModelName}->id, 'q' => '123']));

        \$this->type('{$this->modelName} 1 name', 'name');
        \$this->type('{$this->modelName} 1 description', 'description');
        \$this->press(trans('{$this->singleModelName}.update'));

        \$this->seePageIs(route('{$this->tableName}.index', ['q' => '123']));

        \$this->seeInDatabase('{$this->tableName}', [
            'name'   => '{$this->modelName} 1 name',
            'description'   => '{$this->modelName} 1 description',
        ]);
    }

    /** @test */
    public function user_can_delete_a_{$this->singleModelName}()
    {
        \$this->loginAsUser();
        \${$this->singleModelName} = factory({$this->modelName}::class)->create();

        \$this->visit(route('{$this->tableName}.index', [\${$this->singleModelName}->id]));
        \$this->click('del-{$this->singleModelName}-'.\${$this->singleModelName}->id);
        \$this->seePageIs(route('{$this->tableName}.index', ['action' => 'delete', 'id' => \${$this->singleModelName}->id]));

        \$this->seeInDatabase('{$this->tableName}', [
            'id' => \${$this->singleModelName}->id,
        ]);

        \$this->press(trans('app.delete_confirm_button'));

        \$this->dontSeeInDatabase('{$this->tableName}', [
            'id' => \${$this->singleModelName}->id,
        ]);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents(base_path("tests/Feature/Manage{$this->pluralModelName}Test.php")));
    }
}
