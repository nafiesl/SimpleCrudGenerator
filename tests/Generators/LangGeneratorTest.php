<?php

namespace Tests\Generators;

use Tests\TestCase;

class LangGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_model_lang_content()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $langPath = resource_path('lang/en/'.$this->singleModelName.'.php');
        $this->assertFileExists($langPath);
        $langFileContent = "<?php

return [
    // Labels
    'item'           => 'Item',
    'list'           => 'Item List',
    'search'         => 'Search Item',
    'not_found'      => 'Item not found.',
    'empty'          => 'Item is empty.',
    'back_to_show'   => 'Back to Item Detail',
    'back_to_index'  => 'Back to Item List',

    // Actions
    'create'         => 'Create new Item',
    'created'        => 'Create new Item succeded.',
    'edit'           => 'Edit Item',
    'update'         => 'Update Item',
    'updated'        => 'Update Item succeded.',
    'delete'         => 'Delete Item',
    'delete_confirm' => 'Are you sure to delete this Item?',
    'deleted'        => 'Delete Item succeded.',
    'undeleted'      => 'Item not deleted.',

    // Attributes
    'name'           => 'Item Name',
    'description'    => 'Item Description',
];
";
        $this->assertEquals($langFileContent, file_get_contents($langPath));
    }
}
