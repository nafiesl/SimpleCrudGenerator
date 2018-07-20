<?php

namespace Luthfi\CrudGenerator\Generators;

/**
 * Model Factory Generator Class
 */
class ModelFactoryGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate(string $type = 'full')
    {
        $modelFactoryPath = $this->makeDirectory(database_path('factories'));

        $this->generateFile(
            $modelFactoryPath.'/'.$this->modelNames['model_name'].'Factory.php',
            $this->getContent('database/factories/model-factory')
        );

        $this->command->info($this->modelNames['model_name'].' model factory generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        $modelFactoryFileContent = $this->getStubFileContent($stubName);

        $userModel = config('auth.providers.users.model');

        if ('App\User' !== $userModel) {
            $modelFactoryFileContent = str_replace('App\User', $userModel, $modelFactoryFileContent);
        }

        return $this->replaceStubString($modelFactoryFileContent);
    }
}
