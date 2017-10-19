<?php

namespace Luthfi\CrudGenerator\Generators;

/**
* Form View Generator Class
*/
class FormViewGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        $viewPath = $this->makeDirectory(resource_path('views/'.$this->modelNames['table_name']));

        $this->generateFile($viewPath.'/forms.blade.php', $this->getContent());

        $this->command->info($this->modelNames['model_name'].' form view file generated.');
    }

    /**
     * {@inheritDoc}
     */
    protected function getContent()
    {
        $stub = $this->files->get(__DIR__.'/../stubs/view-forms.stub');
        return $this->replaceStubString($stub);
    }
}
