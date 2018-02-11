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
    public function generate(string $type = 'full')
    {
        $unitTestPath = $this->makeDirectory(base_path('tests/Unit/Models'));

        $this->generateFile(
            "{$unitTestPath}/{$this->modelNames['model_name']}Test.php",
            $this->getContent('test-unit')
        );

        $this->command->info($this->modelNames['model_name'].'Test (model) generated.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getContent(string $stubName)
    {
        $stub = $this->getStubFileContent($stubName);
        $baseTestClass = config('simple-crud.base_test_class');
        $stub = str_replace('use Tests\BrowserKitTest', 'use '.$baseTestClass, $stub);
        return $this->replaceStubString($stub);
    }
}
