<?php

namespace Tests\Generators;

use Tests\TestCase;

class LangGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_model_lang_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $langPath = resource_path('lang/en/'.$this->single_model_var_name.'.php');
        $this->assertFileExists($langPath);
        $langFileContent = "<?php

return [
    // Labels
    '{$this->single_model_var_name}'         => '{$this->model_name}',
    'list'           => '{$this->model_name} List',
    'search'         => 'Search {$this->model_name}',
    'not_found'      => '{$this->model_name} not found.',
    'empty'          => '{$this->model_name} is empty.',
    'back_to_show'   => 'Back to {$this->model_name} Detail',
    'back_to_index'  => 'Back to {$this->model_name} List',

    // Actions
    'create'         => 'Create new {$this->model_name}',
    'created'        => 'Create new {$this->model_name} succeded.',
    'edit'           => 'Edit {$this->model_name}',
    'update'         => 'Update {$this->model_name}',
    'updated'        => 'Update {$this->model_name} succeded.',
    'delete'         => 'Delete {$this->model_name}',
    'delete_confirm' => 'Are you sure to delete this {$this->model_name}?',
    'deleted'        => 'Delete {$this->model_name} succeded.',
    'undeleted'      => '{$this->model_name} not deleted.',

    // Attributes
    'name'           => '{$this->model_name} Name',
    'description'    => '{$this->model_name} Description',
];
";
        $this->assertEquals($langFileContent, file_get_contents($langPath));
    }
}
