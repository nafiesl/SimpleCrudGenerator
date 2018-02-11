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
    public function generate(string $type = 'full')
    {
        $parentControllerDirectory = '';
        if (!is_null($this->command->option('parent'))) {
            $parentControllerDirectory = '/'.$this->command->option('parent');
        }
        $controllerPath = $this->makeDirectory(app_path('Http/Controllers'.$parentControllerDirectory));

        $controllerPath = $controllerPath.'/'.$this->modelNames['plural_model_name'].'Controller.php';
        $this->generateFile($controllerPath, $this->getContent('controller.'.$type));

        $this->command->info($this->modelNames['plural_model_name'].'Controller generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        $stub = $this->getStubFileContent($stubName);

        $controllerFileContent = $this->replaceStubString($stub);

        $appNamespace = $this->getAppNamespace();

        $controllerFileContent = str_replace(
            "App\Http\Controllers;",
            "{$appNamespace}Http\Controllers;",
            $controllerFileContent
        );

        if (!is_null($parentName = $this->command->option('parent'))) {
            $searches = [
                "{$appNamespace}Http\Controllers;",
                "use {$this->modelNames['full_model_name']};\n",
            ];

            $replacements = [
                "{$appNamespace}Http\Controllers\\{$parentName};",
                "use {$this->modelNames['full_model_name']};\nuse {$appNamespace}Http\Controllers\Controller;\n",
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
