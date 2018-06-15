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

        $this->generateFile($viewPath.'/show.blade.php', $this->getContent('view-show'));

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
