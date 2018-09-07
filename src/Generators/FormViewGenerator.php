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
    public function generate(string $type = 'full')
    {
        $viewPath = $this->makeDirectory(resource_path('views/'.$this->modelNames['table_name']));

        if ($type == 'simple') {
            $this->generateFile($viewPath.'/forms.blade.php', $this->getContent('resources/views/simple/forms'));
        } else {
            $this->generateFile($viewPath.'/create.blade.php', $this->getContent('resources/views/full/create'));
            $this->generateFile($viewPath.'/edit.blade.php', $this->getContent('resources/views/full/edit'));
        }

        $this->command->info($this->modelNames['model_name'].' form view file generated.');
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
