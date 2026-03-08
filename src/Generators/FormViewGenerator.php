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
        $modelViewPath = '';
        if ($this->modelNames['parent_table_name']) {
            $modelViewPath .= $this->modelNames['parent_table_name'].'/';
            $type = $type.'-parentmodel';
        }
        $modelViewPath .= $this->modelNames['table_name'];
        $viewPath = $this->makeDirectory(resource_path('views/'.$modelViewPath));
        $stubSuffix = $this->getStubSuffix();

        if (in_array($type, ['simple', 'simple-parentmodel'])) {
            $this->generateFile($viewPath.'/forms.blade.php', $this->getContent('resources/views/'.$type.'/forms'.$stubSuffix));
        } else {
            $this->generateFile($viewPath.'/create.blade.php', $this->getContent('resources/views/'.$type.'/create'.$stubSuffix));
            $this->generateFile($viewPath.'/edit.blade.php', $this->getContent('resources/views/'.$type.'/edit'.$stubSuffix));
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
