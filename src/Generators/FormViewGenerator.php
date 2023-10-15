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
        $stubSuffix = $this->getStubSuffix();

        if ($type == 'simple') {
            $this->generateFile($viewPath.'/forms.blade.php', $this->getContent('resources/views/simple/forms'.$stubSuffix));
        } else {
            $this->generateFile($viewPath.'/create.blade.php', $this->getContent('resources/views/full/create'.$stubSuffix));
            $this->generateFile($viewPath.'/edit.blade.php', $this->getContent('resources/views/full/edit'.$stubSuffix));
        }

        $this->command->info($this->modelNames['model_name'].' form view file generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        return $this->replaceStubString($this->getStubFileContent($stubName));
    }
}
