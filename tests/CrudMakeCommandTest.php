<?php

use Orchestra\Testbench\TestCase;

class CrudMakeCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Luthfi\CrudGenerator\ServiceProvider'];
    }

    /** @test */
    public function it_can_generate_simple_crud_files()
    {
        $this->artisan('make:crud', ['name' => 'Item', '--no-interaction' => true]);

        $this->assertFileExists(app_path('Item.php'));
        $this->assertFileExists(app_path('Http/Controllers/ItemsController.php'));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_items_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path('views/items/index.blade.php'));
        $this->assertFileExists(resource_path('views/items/forms.blade.php'));
        $this->assertFileExists(base_path('tests/Feature/ManageItemsTest.php'));
        $this->assertFileExists(base_path('tests/Unit/Models/ItemTest.php'));

        exec('rm '.app_path('Item.php'));
        exec('rm -r '.app_path('Http'));
        exec('rm '.database_path('migrations/*'));
        exec('rm -r '.resource_path('views/items'));
        exec('rm -r '.base_path('tests/Feature'));
        exec('rm -r '.base_path('tests/Unit'));
    }

    /** @test */
    public function it_creates_correct_model_class_content()
    {
        $this->artisan('make:crud', ['name' => 'Item', '--no-interaction' => true]);

        $this->assertFileExists(app_path('Item.php'));
        $modelClassContent = "<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //
}
";
        $this->assertEquals($modelClassContent, file_get_contents(app_path('Item.php')));
        exec('rm '.app_path('Item.php'));
        exec('rm -r '.app_path('Http'));
        exec('rm '.database_path('migrations/*'));
        exec('rm -r '.resource_path('views/items'));
        exec('rm -r '.base_path('tests/Feature'));
        exec('rm -r '.base_path('tests/Unit'));
    }

    /** @test */
    public function it_creates_correct_controller_class_content()
    {
        $this->artisan('make:crud', ['name' => 'Item', '--no-interaction' => true]);

        $this->assertFileExists(app_path('Item.php'));
        $ctrlClassContent = "<?php

namespace DummyNamespace;

use DummyFullModelClass;
use Illuminate\Http\Request;
use DummyRootNamespaceHttp\Controllers\Controller;

class DummyClass extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Http\Response
     */
    public function store(Request \$request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \DummyFullModelClass  \$DummyModelVariable
     * @return \Illuminate\Http\Response
     */
    public function show(DummyModelClass \$DummyModelVariable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \DummyFullModelClass  \$DummyModelVariable
     * @return \Illuminate\Http\Response
     */
    public function update(Request \$request, DummyModelClass \$DummyModelVariable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \DummyFullModelClass  \$DummyModelVariable
     * @return \Illuminate\Http\Response
     */
    public function destroy(DummyModelClass \$DummyModelVariable)
    {
        //
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path('Http/Controllers/ItemsController.php')));
        exec('rm '.app_path('Item.php'));
        exec('rm -r '.app_path('Http'));
        exec('rm '.database_path('migrations/*'));
        exec('rm -r '.resource_path('views/items'));
        exec('rm -r '.base_path('tests/Feature'));
        exec('rm -r '.base_path('tests/Unit'));
    }
}
