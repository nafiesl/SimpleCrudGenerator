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
        $viewPath = $this->makeDirectory(resource_path('views/'.$this->modelNames['table_name']));
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
