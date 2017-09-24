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

use App\Item;
use Tests\BrowserKitTest as TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ManageItemsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_see_item_list_in_item_index_page()
    {
        \$item1 = factory(Item::class)->create(['name' => 'Testing name', 'description' => 'Testing 123']);
        \$item2 = factory(Item::class)->create(['name' => 'Testing name', 'description' => 'Testing 456']);

        \$this->loginAsUser();
        \$this->visit(route('items.index'));
        \$this->see(\$item1->name);
        \$this->see(\$item2->name);
    }

    /** @test */
    public function user_can_create_a_item()
    {
        \$this->loginAsUser();
        \$this->visit(route('items.index'));

        \$this->click(trans('item.create'));
        \$this->seePageIs(route('items.index', ['action' => 'create']));

        \$this->type('Item 1 name', 'name');
        \$this->type('Item 1 description', 'description');
        \$this->press(trans('item.create'));

        \$this->seePageIs(route('items.index'));

        \$this->seeInDatabase('items', [
            'name'   => 'Item 1 name',
            'description'   => 'Item 1 description',
        ]);
    }

    /** @test */
    public function user_can_edit_a_item_within_search_query()
    {
        \$this->loginAsUser();
        \$item = factory(Item::class)->create(['description' => 'Testing 123']);

        \$this->visit(route('items.index', ['q' => '123']));
        \$this->click('edit-item-'.\$item->id);
        \$this->seePageIs(route('items.index', ['action' => 'edit', 'id' => \$item->id, 'q' => '123']));

        \$this->type('Item 1 name', 'name');
        \$this->type('Item 1 description', 'description');
        \$this->press(trans('item.update'));

        \$this->visit(route('items.index', ['q' => '123']));

        \$this->seeInDatabase('items', [
            'name'   => 'Item 1 name',
            'description'   => 'Item 1 description',
        ]);
    }

    /** @test */
    public function user_can_delete_a_item()
    {
        \$this->loginAsUser();
        \$item = factory(Item::class)->create();

        \$this->visit(route('items.index', [\$item->id]));
        \$this->click('del-item-'.\$item->id);
        \$this->seePageIs(route('items.index', ['action' => 'delete', 'id' => \$item->id]));

        \$this->seeInDatabase('items', [
            'id' => \$item->id,
        ]);

        \$this->press(trans('app.delete_confirm_button'));

        \$this->dontSeeInDatabase('items', [
            'id' => \$item->id,
        ]);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents(base_path("tests/Feature/Manage{$this->pluralModelName}Test.php")));
    }
}
