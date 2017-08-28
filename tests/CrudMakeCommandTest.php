<?php

namespace Tests;

class CrudMakeCommandTest extends TestCase
{
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
}
