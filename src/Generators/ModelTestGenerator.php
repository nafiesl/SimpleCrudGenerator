<?php

namespace Luthfi\CrudGenerator\Generators;

/**
* Model Test Generator Class
*/
class ModelTestGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        $unitTestPath = $this->makeDirectory(base_path('tests/Unit/Models'));
        $this->generateFile("{$unitTestPath}/{$this->modelNames['model_name']}Test.php", $this->getContent());
        $this->command->info($this->modelNames['model_name'].'Test (model) generated.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getContent()
    {
        $stub = $this->files->get(__DIR__.'/../stubs/test-unit.stub');
        return $this->replaceStubString($stub);
    }
}
