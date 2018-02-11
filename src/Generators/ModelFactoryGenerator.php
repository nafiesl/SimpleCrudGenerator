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
            $this->getContent('model-factory')
        );

        $this->command->info($this->modelNames['model_name'].' model factory generated.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getContent(string $stubName)
    {
        return $this->replaceStubString($this->getStubFileContent($stubName));
    }
}
