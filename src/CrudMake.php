<?php

namespace Luthfi\CrudGenerator;

class CrudMake extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {name : The model name}
                            {--p|parent= : The generated controller parent directory}
                            {--t|tests-only : Generate CRUD testcases only}
                            {--f|formfield : Generate CRUD with FormField facades}
                            {--r|form-requests : Generate CRUD with Form Request on create and update actions}
                            {--bs3 : Generate CRUD with Bootstrap 3 views}
                            {--uuid : Generate CRUD with UUID primary keys}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create simple Laravel CRUD files of given model name.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->getModelName();

        if ($this->modelExists()) {
            $confirm = $this->confirm('Model file exists, are you sure to generate CRUD files?');
            if (!$confirm) {
                $this->error("{$this->modelNames['model_name']} model already exists.");
                return;
            }
        }

        // Warn if it has no default layout view based on
        // simple-crud.default_layout_view config
        if ($this->defaultLayoutNotExists()) {
            $this->warn(config('simple-crud.default_layout_view').' view does not exists.');
        }

        if ($this->option('tests-only')) {
            $this->generateTestFiles();

            $this->info('Test files generated successfully!');
            return;
        }

        $this->generateRoutes();
        $this->generateModel();
        $this->generateController();
        $this->generateResources();
        $this->generateTestFiles();

        if ($this->option('form-requests')) {
            $this->generateRequestClasses();
        }

        $this->info('CRUD files generated successfully!');
    }

    /**
     * Generate test files
     *
     * @return void
     */
    public function generateTestFiles()
    {
        app('Luthfi\CrudGenerator\Generators\ModelTestGenerator', ['command' => $this])->generate();
        app('Luthfi\CrudGenerator\Generators\FeatureTestGenerator', ['command' => $this])->generate();
        app('Luthfi\CrudGenerator\Generators\ModelPolicyTestGenerator', ['command' => $this])->generate();
    }

    /**
     * Generate Controller
     *
     * @return void
     */
    public function generateController()
    {
        app('Luthfi\CrudGenerator\Generators\ControllerGenerator', ['command' => $this])->generate();
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
        app('Luthfi\CrudGenerator\Generators\RouteGenerator', ['command' => $this])->generate();
    }

    /**
     * Generate Resources
     *
     * @return void
     */
    public function generateResources()
    {
        app('Luthfi\CrudGenerator\Generators\LangFileGenerator', ['command' => $this])->generate();
        app('Luthfi\CrudGenerator\Generators\FormViewGenerator', ['command' => $this])->generate();
        app('Luthfi\CrudGenerator\Generators\IndexViewGenerator', ['command' => $this])->generate();
        app('Luthfi\CrudGenerator\Generators\ShowViewGenerator', ['command' => $this])->generate();
    }

    /**
     * Generate Form Requests
     */
    public function generateRequestClasses()
    {
        app('Luthfi\CrudGenerator\Generators\FormRequestGenerator', ['command' => $this])->generate();
    }
}
