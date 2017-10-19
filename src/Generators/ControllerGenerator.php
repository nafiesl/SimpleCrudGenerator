<?php

namespace Luthfi\CrudGenerator\Generators;

/**
* Controller Generator Class
*/
class ControllerGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        $parentControllerDirectory = '';
        if (! is_null($this->command->option('parent'))) {
            $parentControllerDirectory = '/'.$this->command->option('parent');
        }
        $controllerPath = $this->makeDirectory(app_path('Http/Controllers'.$parentControllerDirectory));

        $controllerPath = $controllerPath.'/'.$this->modelNames['plural_model_name'].'Controller.php';
        $this->generateFile($controllerPath, $this->getControllerContent());

        $this->command->info($this->modelNames['plural_model_name'].'Controller generated.');
    }

    /**
     * Get controller content from controller stub
     *
     * @return string Replaced proper model names in controller file content
     */
    public function getControllerContent()
    {
        $stub = $this->files->get(__DIR__.'/../stubs/controller.model.stub');

        $controllerFileContent = $this->replaceStubString($stub);

        if (! is_null($parentName = $this->command->option('parent'))) {

            $searches = [
                'App\Http\Controllers;',
                "use {$this->modelNames['full_model_name']};\n"
            ];

            $replacements = [
                "App\Http\Controllers\\{$parentName};",
                "use {$this->modelNames['full_model_name']};\nuse App\Http\Controllers\Controller;\n"
            ];

            $controllerFileContent = str_replace(
                $searches,
                $replacements,
                $controllerFileContent
            );
        }

        return $controllerFileContent;
    }
}