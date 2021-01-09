<?php

namespace Luthfi\CrudGenerator;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class GeneratorCommand extends Command
{
    /**
     * The injected Filesystem class
     *
     * @var Filesystem
     */
    private $files;

    /**
     * Array of defined model names
     *
     * @var array
     */
    public $modelNames = [];

    /**
     * Array of stub's model names
     *
     * @var array
     */
    public $stubModelNames = [
        'model_namespace'           => 'mstrNmspc',
        'full_model_name'           => 'fullMstr',
        'plural_model_name'         => 'Masters',
        'model_name'                => 'Master',
        'table_name'                => 'masters',
        'lang_name'                 => 'master',
        'collection_model_var_name' => 'mstrCollections',
        'single_model_var_name'     => 'singleMstr',
    ];

    /**
     * Construct CrudMake class
     *
     * @param Filesystem $files Put generated file content to application file system
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Generate class properties for model names in different usage.
     *
     * @return array
     */
    public function getModelName($modelName = null)
    {
        $modelName = is_null($modelName) ? $this->argument('name') : $modelName;
        $model_name = ucfirst(class_basename($modelName));
        $plural_model_name = Str::plural($model_name);
        $modelPath = $this->getModelPath($modelName);
        $modelNamespace = $this->getModelNamespace($modelPath);

        return $this->modelNames = [
            'model_namespace'           => $modelNamespace,
            'full_model_name'           => $modelNamespace.'\\'.$model_name,
            'plural_model_name'         => $plural_model_name,
            'model_name'                => $model_name,
            'table_name'                => Str::snake($plural_model_name),
            'lang_name'                 => Str::snake($model_name),
            'collection_model_var_name' => Str::camel($plural_model_name),
            'single_model_var_name'     => Str::camel($model_name),
            'model_path'                => $modelPath,
        ];
    }

    /**
     * Get model path on storage
     *
     * @param  string $modelName Input model name from command argument
     * @return string            Model path on storage
     */
    protected function getModelPath($modelName)
    {
        $inputName = explode('/', ucfirst($modelName));
        array_pop($inputName);

        return implode('/', $inputName) ?: 'Models';
    }

    /**
     * Get model namespace
     *
     * @param  string $modelPath Model path
     * @return string            Model namespace
     */
    protected function getModelNamespace($modelPath)
    {
        $appNamespace = trim($this->getAppNamespace(), '\\');
        $modelNamespace = str_replace('/', '\\', $appNamespace.'/'.ucfirst($modelPath));
        $modelNamespace = trim($modelNamespace, '\\');
        return $modelNamespace == 'App\\' ? 'App' : $modelNamespace;
    }

    /**
     * Check for Model file existance
     *
     * @return void
     */
    public function modelExists()
    {
        return $this->files->exists(
            app_path($this->modelNames['model_path'].'/'.$this->modelNames['model_name'].'.php')
        );
    }

    /**
     * Check for default layout view file existance
     *
     * @return void
     */
    public function defaultLayoutNotExists()
    {
        return !$this->files->exists(
            resource_path('views/'.str_replace('.', '/', config('simple-crud.default_layout_view')).'.blade.php')
        );
    }

    protected function getAppNamespace()
    {
        return Container::getInstance()->getNamespace();
    }
}
