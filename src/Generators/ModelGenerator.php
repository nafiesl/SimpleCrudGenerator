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
    public function generate()
    {
        $modelPath = $this->modelNames['model_path'];
        $modelDirectory = $this->makeDirectory(app_path($modelPath));

        $this->generateFile($modelDirectory.'/'.$this->modelNames['model_name'].'.php', $this->getContent());

        $this->command->info($this->modelNames['model_name'].' model generated.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getContent()
    {
        $stub = $this->files->get(__DIR__.'/../stubs/model.stub');
        return $this->replaceStubString($stub);
    }
}
