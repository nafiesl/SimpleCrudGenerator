<?php

namespace Luthfi\CrudGenerator\Generators;

/**
* Model Policy Generator Class
*/
class ModelPolicyGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        $parentDirectory = '';
        if (! is_null($this->command->option('parent'))) {
            $parentDirectory = '/'.$this->command->option('parent');
        }
        $modelPolicyPath = $this->makeDirectory(app_path('Policies'.$parentDirectory));

        $this->generateFile(
            $modelPolicyPath.'/'.$this->modelNames['model_name'].'Policy.php',
            $this->getContent()
        );

        $this->command->info($this->modelNames['model_name'].' model policy generated.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getContent()
    {
        $stub = $this->files->get(__DIR__.'/../stubs/model-policy.stub');

        $policyFileContent = $this->replaceStubString($stub);

        $userModel = config('auth.providers.users.model');

        if ('App\User' !== $userModel) {
            $policyFileContent = str_replace('App\User', $userModel, $policyFileContent);
        }

        if (! is_null($parentName = $this->command->option('parent'))) {
            $policyFileContent = str_replace(
                'App\Policies;',
                "App\Policies\\{$parentName};",
                $policyFileContent
            );
        }

        return $policyFileContent;
    }
}
