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
        $modelViewPath = '';
        if ($this->modelNames['parent_table_name']) {
            $modelViewPath .= $this->modelNames['parent_table_name'].'/';
            $type = $type.'-parentmodel';
        }
        $modelViewPath .= $this->modelNames['table_name'];
        $viewPath = $this->makeDirectory(resource_path('views/'.$modelViewPath));
        $stubSuffix = $this->getStubSuffix();
        $this->generateFile($viewPath.'/show.blade.php', $this->getContent('resources/views/'.$type.'/show'.$stubSuffix));

        $this->command->info($this->modelNames['model_name'].' show view file generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        return $this->replaceStubString($this->getStubFileContent($stubName));
    }
}
