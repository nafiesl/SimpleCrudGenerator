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
        $this->generateFile($viewPath.'/index.blade.php', $this->getContent('resources/views/'.$type.'/index'.$stubSuffix));

        $this->command->info($this->modelNames['model_name'].' index view file generated.');
    }

    /**
     * {@inheritDoc}
     */
    public function getContent(string $stubName)
    {
        return $this->replaceStubString($this->getStubFileContent($stubName));
    }
}
