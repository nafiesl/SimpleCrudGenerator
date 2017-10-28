<?php

namespace Luthfi\CrudGenerator;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Luthfi\CrudGenerator\Generators\ControllerGenerator;
use Luthfi\CrudGenerator\Generators\FeatureTestGenerator;
use Luthfi\CrudGenerator\Generators\FormViewGenerator;
use Luthfi\CrudGenerator\Generators\IndexViewGenerator;
use Luthfi\CrudGenerator\Generators\LangFileGenerator;
use Luthfi\CrudGenerator\Generators\MigrationGenerator;
use Luthfi\CrudGenerator\Generators\ModelFactoryGenerator;
use Luthfi\CrudGenerator\Generators\ModelGenerator;
use Luthfi\CrudGenerator\Generators\ModelPolicyGenerator;
use Luthfi\CrudGenerator\Generators\ModelPolicyTestGenerator;
use Luthfi\CrudGenerator\Generators\ModelTestGenerator;
use Luthfi\CrudGenerator\Generators\WebRouteGenerator;

class CrudMake extends Command
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
    public $stubModelNames;

    /**
     * Construct CrudMake class
     *
     * @param Filesystem $files Put generated file content to application file system
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;

        $this->stubModelNames = [
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
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {name} {--parent=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create simple Laravel complate CRUD files of given model name.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->getModelName();

        if ($this->modelExists()) {
            $this->error("{$this->modelNames['model_name']} model already exists.");
            return;
        }

        // Warn if it has no default layout view based on
        // simple-crud.default_layout_view config
        if ($this->defaultLayoutNotExists()) {
            $this->warn(config('simple-crud.default_layout_view').' view does not exists.');
        }

        app(WebRouteGenerator::class, ['command' => $this])->generate();
        app(ModelGenerator::class, ['command' => $this])->generate();
        app(MigrationGenerator::class, ['command' => $this])->generate();
        app(ControllerGenerator::class, ['command' => $this])->generate();
        app(IndexViewGenerator::class, ['command' => $this])->generate();
        app(FormViewGenerator::class, ['command' => $this])->generate();
        app(LangFileGenerator::class, ['command' => $this])->generate();
        app(ModelFactoryGenerator::class, ['command' => $this])->generate();
        app(ModelPolicyGenerator::class, ['command' => $this])->generate();
        app(FeatureTestGenerator::class, ['command' => $this])->generate();
        app(ModelTestGenerator::class, ['command' => $this])->generate();
        app(ModelPolicyTestGenerator::class, ['command' => $this])->generate();

        $this->info('CRUD files generated successfully!');
    }

    /**
     * Generate class properties for model names in different usage
     *
     * @return void
     */
    public function getModelName($modelName = null)
    {
        $modelName         = is_null($modelName) ? $this->argument('name') : $modelName;
        $model_name        = ucfirst(class_basename($modelName));
        $plural_model_name = str_plural($model_name);
        $modelPath         = $this->getModelPath($modelName);
        $modelNamespace    = $this->getModelNamespace($modelPath);

        return $this->modelNames = [
            'model_namespace'           => $modelNamespace,
            'full_model_name'           => $modelNamespace.'\\'.$model_name,
            'plural_model_name'         => $plural_model_name,
            'model_name'                => $model_name,
            'table_name'                => snake_case($plural_model_name),
            'lang_name'                 => snake_case($model_name),
            'collection_model_var_name' => camel_case($plural_model_name),
            'single_model_var_name'     => camel_case($model_name),
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

        return implode('/', $inputName);
    }

    /**
     * Get model namespace
     *
     * @param  string $modelPath Model path
     * @return string            Model namespace
     */
    protected function getModelNamespace($modelPath)
    {
        $modelNamespace = str_replace('/', '\\', 'App/'.ucfirst($modelPath));
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
        return  ! $this->files->exists(
            resource_path('views/'.str_replace('.', '/', config('simple-crud.default_layout_view')).'.blade.php')
        );
    }
}
