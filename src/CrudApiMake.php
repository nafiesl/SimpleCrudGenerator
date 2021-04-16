<?php

namespace Luthfi\CrudGenerator;

class CrudApiMake extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud-api {name : The model name}
                            {--p|parent= : The generated API controller parent directory}
                            {--t|tests-only : Generate API CRUD testcases only}
                            {--r|form-requests : Generate CRUD with Form Request on create and update actions}
                            {--f|formfield : Generate CRUD with FormField facades}
                            {--uuid : Generate CRUD with UUID primary keys}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create simple Laravel API CRUD files of given model name.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->getModelName();

        if ($this->modelExists()) {
            $this->warn("We will use existing {$this->modelNames['model_name']} model.\n");
        }

        // Warn if it has no default layout view based on
        // simple-crud.default_layout_view config
        if ($this->defaultLayoutNotExists()) {
            $this->warn(config('simple-crud.default_layout_view').' view does not exists.');
        }

        if ($this->option('tests-only')) {
            $this->generateTestFiles();

            $this->info('API Test files generated successfully!');
            return;
        }

        $this->generateRoutes();
        $this->generateController();
        $this->generateTestFiles();
        if ($this->modelExists() == false) {
            $this->generateModel();
            $this->generateResources();
        }

        $this->info('API CRUD files generated successfully!');
    }

    /**
     * Generate test files
     *
     * @return void
     */
    public function generateTestFiles()
    {
        app('Luthfi\CrudGenerator\Generators\FeatureTestGenerator', ['command' => $this])->generate('api');

        if ($this->modelExists() == false) {
            app('Luthfi\CrudGenerator\Generators\ModelTestGenerator', ['command' => $this])->generate();
            app('Luthfi\CrudGenerator\Generators\ModelPolicyTestGenerator', ['command' => $this])->generate();
        }
    }

    /**
     * Generate Controller
     *
     * @return void
     */
    public function generateController()
    {
        app('Luthfi\CrudGenerator\Generators\ControllerGenerator', ['command' => $this])->generate('api');
    }

    /**
     * Generate Model
     *
     * @return void
     */
    public function generateModel()
    {
        app('Luthfi\CrudGenerator\Generators\ModelGenerator', ['command' => $this])->generate();
        app('Luthfi\CrudGenerator\Generators\MigrationGenerator', ['command' => $this])->generate();
        app('Luthfi\CrudGenerator\Generators\ModelPolicyGenerator', ['command' => $this])->generate();
        app('Luthfi\CrudGenerator\Generators\ModelFactoryGenerator', ['command' => $this])->generate();
    }

    /**
     * Generate Route Route
     *
     * @return void
     */
    public function generateRoutes()
    {
        app('Luthfi\CrudGenerator\Generators\RouteGenerator', ['command' => $this])->generate('api');
    }

    /**
     * Generate Resources
     *
     * @return void
     */
    public function generateResources()
    {
        app('Luthfi\CrudGenerator\Generators\LangFileGenerator', ['command' => $this])->generate();
    }
}
