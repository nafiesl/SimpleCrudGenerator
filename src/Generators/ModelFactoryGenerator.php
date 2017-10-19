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
    public function generate()
    {
        $modelFactoryPath = $this->makeDirectory(database_path('factories'));

        $this->generateFile(
            $modelFactoryPath.'/'.$this->modelNames['model_name'].'Factory.php',
            $this->getContent()
        );

        $this->command->info($this->modelNames['model_name'].' model factory generated.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getContent()
    {
        $stub = $this->files->get(__DIR__.'/../stubs/model-factory.stub');
        return $this->replaceStubString($stub);
    }
}
