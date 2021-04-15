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
        $modelFactoryClassPath = $modelFactoryPath.'/'.$this->modelNames['model_name'].'Factory.php';

        if ($this->files->exists($modelFactoryClassPath)) {
            $this->command->warn('Use the existing '.$this->modelNames['model_name'].' model factory.');
            return;
        }

        $this->generateFile(
            $modelFactoryClassPath,
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

        if ($this->command->option('uuid')) {
            $string = "'title'       => \$this->faker->word,\n";
            $replacement = "'id'       => \$this->faker->uuid,\n            'title'       => \$this->faker->word,\n";
            $modelFactoryFileContent = str_replace($string, $replacement, $modelFactoryFileContent);
        }

        return $this->replaceStubString($modelFactoryFileContent);
    }
}
