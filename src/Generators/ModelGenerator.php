<?php

namespace Luthfi\CrudGenerator\Generators;

/**
 * Model Generator Class
 */
class ModelGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate(string $type = 'full')
    {
        $modelPath = $this->modelNames['model_path'];
        $modelDirectory = $this->makeDirectory(app_path($modelPath));
        $modelClassPath = $modelDirectory.'/'.$this->modelNames['model_name'].'.php';

        if ($this->files->exists($modelClassPath)) {
            return;
        }

        $this->generateFile($modelClassPath, $this->getContent('models/model'));

        $this->command->info($this->modelNames['model_name'].' model generated.');
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

        $userModel = config('auth.providers.users.model');

        if ('App\User' !== $userModel) {
            $modelFileContent = str_replace('App\User', $userModel, $modelFileContent);
        }

        return $this->replaceStubString($modelFileContent);
    }
}
