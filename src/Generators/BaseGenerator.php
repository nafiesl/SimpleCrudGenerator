<?php

namespace Luthfi\CrudGenerator\Generators;

use Illuminate\Filesystem\Filesystem;
use Luthfi\CrudGenerator\CrudMake;

/**
* Base Generator Class
*/
abstract class BaseGenerator
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
     * The CrudMake class
     *
     * @var CrudMake
     */
    protected $command;

    public function __construct(Filesystem $files, CrudMake $command)
    {
        $this->files = $files;

        $this->command = $command;

        $this->getModelNames();
        $this->getStubModelNames();
    }

    /**
     * Generate class properties for model names in different usage
     *
     * @return array
     */
    public function getModelNames($modelName = null)
    {
        $modelName = is_null($modelName) ? $this->command->argument('name') : $modelName;
        $model_name = ucfirst(class_basename($modelName));
        $plural_model_name = str_plural($model_name);
        $modelPath = $this->getModelPath($modelName);
        $modelNamespace = $this->getModelNamespace($modelPath);

        return $this->modelNames = [
            'model_namespace' => $modelNamespace,
            'full_model_name' => $modelNamespace.'\\'.$model_name,
            'plural_model_name' => $plural_model_name,
            'model_name' => $model_name,
            'table_name' => snake_case($plural_model_name),
            'lang_name' => snake_case($model_name),
            'collection_model_var_name' => camel_case($plural_model_name),
            'single_model_var_name' => camel_case($model_name),
            'model_path' => $modelPath,
        ];
    }

    /**
     * Get stub's model names
     *
     * @return array
     */
    protected function getStubModelNames()
    {
        return $this->stubModelNames = [
            'model_namespace' => 'mstrNmspc',
            'full_model_name' => 'fullMstr',
            'plural_model_name' => 'Masters',
            'model_name' => 'Master',
            'table_name' => 'masters',
            'lang_name' => 'master',
            'collection_model_var_name' => 'mstrCollections',
            'single_model_var_name' => 'singleMstr',
        ];
    }

    /**
     * Generate class file content
     *
     * @return void
     */
    abstract public function generate();

    /**
     * Make directory if the path is not exists
     * @param  string $path Absolute path of targetted directory
     * @return string       Absolute path
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
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
     * Get model path on storage
     * @param  string $modelName Input model name from command argument
     * @return string            Model path on storage
     */
    protected function getModelPath($modelName)
    {
        $inputName = explode('/', ucfirst($modelName));
        array_pop($inputName);

        return implode('/', $inputName);
    }

    protected function getModelNamespace($modelPath)
    {
        $modelNamespace = str_replace('/', '\\', 'App/'.ucfirst($modelPath));
        return $modelNamespace == 'App\\' ? 'App' : $modelNamespace;
    }
}