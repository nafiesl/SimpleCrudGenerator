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
        $modelName = $this->modelNames['model_name'];
        $parentControllerDirectory = '';
        if (!is_null($this->command->option('parent'))) {
            $parentControllerDirectory = '/'.$this->command->option('parent');
        }

        if ($this->isForApi()) {
            $parentControllerDirectory = '/Api'.$parentControllerDirectory;
        }

        $controllerPath = $this->makeDirectory(app_path('Http/Controllers'.$parentControllerDirectory));
        $controllerPath = $controllerPath.'/'.$modelName.'Controller.php';

        $this->generateFile($controllerPath, $this->getContent('controllers/'.$type));

        if ($this->isForApi()) {
            $modelName = 'Api/'.$modelName;
        }

        $this->command->info($modelName.'Controller generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        if ($this->command->option('form-requests')) {
            $stubName .= '-formrequests';
        }

        $stub = $this->getStubFileContent($stubName);

        $controllerFileContent = $this->replaceStubString($stub);

        $appNamespace = $this->getAppNamespace();

        $controllerFileContent = str_replace(
            ["App\Http\Controllers", "App\Http\Requests"],
            ["{$appNamespace}Http\Controllers", "{$appNamespace}Http\Requests"],
            $controllerFileContent
        );

        if (!is_null($parentName = $this->command->option('parent'))) {
            $searches = [
                "{$appNamespace}Http\Controllers",
                "use {$this->modelNames['full_model_name']};\n",
            ];

            $replacements = [
                "{$appNamespace}Http\Controllers\\{$parentName}",
                "use {$this->modelNames['full_model_name']};\nuse {$appNamespace}Http\Controllers\Controller;\n",
            ];

            $controllerFileContent = str_replace(
                $searches,
                $replacements,
                $controllerFileContent
            );
        }

        if ($this->command->option('uuid')) {
            $string = "use Illuminate\Http\Request;\n";
            $replacement = "use Illuminate\Http\Request;\nuse Ramsey\Uuid\Uuid;\n";
            $controllerFileContent = str_replace($string, $replacement, $controllerFileContent);

            $string = "\$new{$this->modelNames['model_name']}['creator_id'] = auth()->id();\n";
            $replacement = "\$new{$this->modelNames['model_name']}['id'] = Uuid::uuid4()->toString();\n";
            $replacement .= "        \$new{$this->modelNames['model_name']}['creator_id'] = auth()->id();\n";

            $controllerFileContent = str_replace($string, $replacement, $controllerFileContent);
        }

        return $controllerFileContent;
    }
}
