<?php

namespace Luthfi\CrudGenerator\Generators;

/**
 * Form Request Generator Class
 */
class FormRequestGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate(string $type = 'full')
    {
        $modelName = $this->modelNames['model_name'];
        $pluralModelName = $this->modelNames['plural_model_name'];

        $requestPath = $this->makeDirectory(app_path('Http/Requests/'.$pluralModelName));
        $createRequestPath = $requestPath.'/CreateRequest.php';
        $this->generateFile($createRequestPath, $this->getContent('requests/create-request'));

        $updateRequestPath = $requestPath.'/UpdateRequest.php';
        $this->generateFile($updateRequestPath, $this->getContent('requests/update-request'));

        $this->command->info($modelName.' Form Requests generated.');
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
            "App\Http\Controllers",
            "{$appNamespace}Http\Controllers",
            $controllerFileContent
        );

        return $controllerFileContent;
    }
}
