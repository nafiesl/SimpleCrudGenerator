<?php

namespace Luthfi\CrudGenerator\Generators;

/**
 * Feature Test Generator Class
 */
class FeatureTestGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate(string $type = 'full')
    {
        $this->createBrowserKitBaseTestClass();

        $featureTestPath = 'tests/Feature';

        if ($this->isForApi()) {
            $featureTestPath .= '/Api';
        }

        $featureTestPath = $this->makeDirectory(base_path($featureTestPath));

        $this->generateFile(
            "{$featureTestPath}/Manage{$this->modelNames['model_name']}Test.php",
            $this->getContent('testcases/feature/'.$type)
        );

        $this->command->info('Manage'.$this->modelNames['model_name'].'Test generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        $stub = $this->getStubFileContent($stubName);
        $baseTestClass = config('simple-crud.base_test_class');
        $stub = str_replace('use Tests\BrowserKitTest', 'use '.$baseTestClass, $stub);
        $stub = str_replace('use Tests\TestCase as TestCase', 'use Tests\TestCase', $stub);

        return $this->replaceStubString($stub);
    }

    /**
     * Generate BrowserKitTest class for BaseTestCase
     *
     * @return void
     */
    private function createBrowserKitBaseTestClass()
    {
        $testsPath = base_path('tests');
        if (!$this->files->isDirectory($testsPath)) {
            $this->files->makeDirectory($testsPath, 0777, true, true);
        }

        $userModel = config('auth.providers.users.model');
        $baseTestPath = base_path(config('simple-crud.base_test_path'));
        $baseTestClass = class_basename(config('simple-crud.base_test_class'));

        if (!$this->files->exists($baseTestPath)) {
            $browserKitTestClassContent = str_replace(
                ['class BrowserKitTest extends', 'App\User'],
                ["class {$baseTestClass} extends", $userModel],
                $this->getBrowserKitBaseTestContent()
            );

            $this->generateFile($baseTestPath, $browserKitTestClassContent);

            $this->command->info('BrowserKitTest generated.');
        }
    }

    /**
     * Get BrowserKitBaseTest class file content
     *
     * @return string
     */
    public function getBrowserKitBaseTestContent()
    {
        return $this->files->get(__DIR__.'/../stubs/testcases/browserkit-base-class.stub');
    }
}
