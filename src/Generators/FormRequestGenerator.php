<?php

namespace Luthfi\CrudGenerator\Generators;

/**
 * Form Request Generator Class
 */
class FormRequestGenerator extends BaseGenerator
{
    /**
     * Generate class file content.
     *
     * @param  string  $type Type of crud
     * @return void
     */
    public function generate(string $type = 'full')
    {
        $modelName = $this->modelNames['model_name'];
        $pluralModelName = $this->modelNames['plural_model_name'];

        $requestPath = $this->makeDirectory(app_path('Http/Requests/'.$pluralModelName));

        $this->generateFile(
            $requestPath.'/CreateRequest.php', $this->getContent('requests/create-request')
        );
        $this->generateFile(
            $requestPath.'/UpdateRequest.php', $this->getContent('requests/update-request')
        );

        $this->command->info($modelName.' Form Requests generated.');
    }

    /**
     * Get class file content.
     *
     * @param  string  $stubName Name of stub file
     * @return string
     */
    public function getContent(string $stubName)
    {
        $stub = $this->getStubFileContent($stubName);

        $requestFileContent = $this->replaceStubString($stub);

        $appNamespace = $this->getAppNamespace();

        $requestFileContent = str_replace(
            "App\Http\Requests",
            "{$appNamespace}Http\Requests",
            $requestFileContent
        );

        return $requestFileContent;
    }
}
