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
            $this->getContent('testcases/unit/model')
        );

        $this->command->info($this->modelNames['model_name'].'Test (model) generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        if ($this->command->option('formfield')) {
            $stubName .= '-formfield';
        }

        $modelFileContent = $this->getStubFileContent($stubName);

        $baseTestClass = config('simple-crud.base_test_class');
        $modelFileContent = str_replace('use Tests\BrowserKitTest', 'use '.$baseTestClass, $modelFileContent);
        $modelFileContent = str_replace('use Tests\TestCase as TestCase', 'use Tests\TestCase', $modelFileContent);

        $userModel = config('auth.providers.users.model');

        if ('App\User' !== $userModel) {
            $modelFileContent = str_replace('App\User', $userModel, $modelFileContent);
        }

        return $this->replaceStubString($modelFileContent);
    }
}
