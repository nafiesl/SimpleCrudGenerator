<?php

namespace Tests\Generators;

use Tests\TestCase;

class ControllerGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_controller_class_content()
    {
        $this->artisan('make:crud', ['name' => 'Item', '--no-interaction' => true]);

        $this->assertFileExists(app_path('Item.php'));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemsController extends Controller
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
     * @param  \App\Item  \$item
     * @return \Illuminate\Http\Response
     */
    public function show(Item \$item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \App\Item  \$item
     * @return \Illuminate\Http\Response
     */
    public function update(Request \$request, Item \$item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  \$item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item \$item)
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
