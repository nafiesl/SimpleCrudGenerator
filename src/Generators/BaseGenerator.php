<?php

namespace Luthfi\CrudGenerator\Generators;

use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Luthfi\CrudGenerator\Contracts\Generator as GeneratorContract;
use Luthfi\CrudGenerator\GeneratorCommand;

/**
 * Base Generator Class
 */
abstract class BaseGenerator implements GeneratorContract
{
    /**
     * The injected Filesystem class
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Array of defined model names
     *
     * @var array
     */
    protected $modelNames;

    /**
     * Array of stub's model names
     *
     * @var array
     */
    protected $stubModelNames;

    /**
     * The Generator Command implementation class
     *
     * @var GeneratorCommand
     */
    protected $command;

    public function __construct(Filesystem $files, GeneratorCommand $command)
    {
        $this->files = $files;

        $this->command = $command;

        $this->modelNames = $this->command->modelNames;
        $this->getStubModelNames();
    }

    /**
     * Get stub's model names
     *
     * @return array
     */
    protected function getStubModelNames()
    {
        return $this->stubModelNames = [
            'model_namespace'           => 'mstrNmspc',
            'full_model_name'           => 'fullMstr',
            'plural_model_name'         => 'Masters',
            'model_name'                => 'Master',
            'table_name'                => 'masters',
            'lang_name'                 => 'master',
            'collection_model_var_name' => 'mstrCollections',
            'single_model_var_name'     => 'singleMstr',
        ];
    }

    /**
     * Make directory if the path is not exists
     *
     * @param  string $path Absolute path of targetted directory
     * @return string       Absolute path
     */
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Generate file on filesystem
     * @param  string $path    Absoute path of file
     * @param  string $content Generated file content
     * @return string          Absolute path of file
     */
    protected function generateFile($path, $content)
    {
        $this->files->put($path, $content);

        return $path;
    }

    /**
     * Replace all string of model names
     *
     * @param  string $stub String of file or class stub with default content
     * @return string       Replaced content
     */
    protected function replaceStubString($stub)
    {
        return str_replace($this->stubModelNames, $this->command->modelNames, $stub);
    }

    /**
     * Get correct stub file content
     *
     * @param  string $stubName The stub file name
     * @return string           The stub file content
     */
    protected function getStubFileContent(string $stubName)
    {
        $publishedStubPath = base_path('stubs/simple-crud/'.$stubName.'.stub');

        if (is_file($publishedStubPath)) {
            return $this->files->get($publishedStubPath);
        }

        return $this->files->get(__DIR__.'/../stubs/'.$stubName.'.stub');
    }

    /**
     * Check whether we generating API CRUD or not.
     *
     * @return bool
     */
    protected function isForApi()
    {
        return $this->command->getName() == 'make:crud-api';
    }

    protected function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }
}
