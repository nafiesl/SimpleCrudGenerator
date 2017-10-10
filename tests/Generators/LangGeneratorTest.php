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
    '{$this->singleModelName}'         => '{$this->modelName}',
    'list'           => '{$this->modelName} List',
    'search'         => 'Search {$this->modelName}',
    'not_found'      => '{$this->modelName} not found.',
    'empty'          => '{$this->modelName} is empty.',
    'back_to_show'   => 'Back to {$this->modelName} Detail',
    'back_to_index'  => 'Back to {$this->modelName} List',

    // Actions
    'create'         => 'Create new {$this->modelName}',
    'created'        => 'Create new {$this->modelName} succeded.',
    'edit'           => 'Edit {$this->modelName}',
    'update'         => 'Update {$this->modelName}',
    'updated'        => 'Update {$this->modelName} succeded.',
    'delete'         => 'Delete {$this->modelName}',
    'delete_confirm' => 'Are you sure to delete this {$this->modelName}?',
    'deleted'        => 'Delete {$this->modelName} succeded.',
    'undeleted'      => '{$this->modelName} not deleted.',

    // Attributes
    'name'           => '{$this->modelName} Name',
    'description'    => '{$this->modelName} Description',
];
";
        $this->assertEquals($langFileContent, file_get_contents($langPath));
    }
}
