<?php

namespace Luthfi\CrudGenerator\Generators;

/**
* Index View Generator Class
*/
class IndexViewGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        $viewPath = $this->makeDirectory(resource_path('views/'.$this->modelNames['table_name']));

        $this->generateFile($viewPath.'/index.blade.php', $this->getContent());

        $this->command->info($this->modelNames['model_name'].' index view file generated.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getContent()
    {
        $stub = $this->files->get(__DIR__.'/../stubs/view-index.stub');
        return $this->replaceStubString($stub);
    }
}