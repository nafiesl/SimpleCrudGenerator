<?php

namespace Luthfi\CrudGenerator\Generators;

/**
 * Show View Generator Class
 */
class ShowViewGenerator extends BaseGenerator
{
    /**
     * {@inheritDoc}
     */
    public function generate(string $type = 'full')
    {
        $viewPath = $this->makeDirectory(resource_path('views/'.$this->modelNames['table_name']));

        $this->generateFile($viewPath.'/show.blade.php', $this->getContent('resources/views/full/show'));

        $this->command->info($this->modelNames['model_name'].' show view file generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        if ($this->command->option('formfield')) {
            $stubName .= '-formfield';
        }

        if ($this->command->option('bs3')) {
            $stubName .= '-bs3';
        }

        return $this->replaceStubString($this->getStubFileContent($stubName));
    }
}
