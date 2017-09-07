<?php

namespace Luthfi\CrudGenerator;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CrudMake extends Command
{
    private $files;
    private $modelName;
    private $pluralModelName;
    private $lowerCasePluralModel;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {name}';

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

        $this->generateModel();
        $this->generateMigration();
        $this->generateController();
        $this->generateViews();
        $this->generateTests();

        $this->info('CRUD files generated successfully!');
    }

    public function getModelName()
    {
        $this->modelName = $this->argument('name');

        $this->pluralModelName = str_plural($this->modelName);
        $this->lowerCasePluralModel = strtolower($this->pluralModelName);
    }

    public function generateModel()
    {
        $this->callSilent('make:model', ['name' => $this->modelName]);;

        $this->info($this->modelName.' model generated.');
    }

    public function generateController()
    {
        $controllerPath = $this->makeDirectory(app_path('Http/Controllers'));

        $controllerPath = $controllerPath.'/'.$this->pluralModelName.'Controller.php';
        $this->files->put($controllerPath, $this->getControllerContent());

        $this->info($this->pluralModelName.'Controller generated.');
    }

    public function generateMigration()
    {
        $prefix = date('Y_m_d_His');
        $tableName = $this->lowerCasePluralModel;
        $migrationFilePath = database_path("migrations/{$prefix}_create_{$tableName}_table.php");
        $this->files->put($migrationFilePath, $this->getMigrationContent());

        $this->info($this->modelName.' table migration generated.');
    }

    public function generateViews()
    {
        $viewPath = $this->makeDirectory(resource_path('views/'.$this->lowerCasePluralModel));

        $this->files->put($viewPath.'/index.blade.php', $this->getIndexViewContent());
        $this->files->put($viewPath.'/forms.blade.php', $this->getFormsViewContent());

        $this->info($this->modelName.' view files generated.');
    }

    public function generateTests()
    {
        $featureTestPath = $this->makeDirectory(base_path('tests/Feature'));
        $this->files->put("{$featureTestPath}/Manage{$this->pluralModelName}Test.php", $this->getFeatureTestContent());
        $this->info('Manage'.$this->pluralModelName.'Test generated.');

        $unitTestPath = $this->makeDirectory(base_path('tests/Unit/Models'));
        $this->files->put("{$unitTestPath}/{$this->modelName}Test.php", $this->getUnitTestContent());
        $this->info($this->modelName.'Test (model) generated.');
    }

    public function getControllerContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/controller.model.stub');
        return $this->replaceControllerDummyStrings($stub)->replaceClass($stub);
    }

    private function getMigrationContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/migration-create.stub');
        return $this->replaceMigrationDummyStrings($stub)->replaceClass($stub);
    }

    public function getIndexViewContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/view-index.stub');
        return $this->replaceViewDummyStrings($stub)->replaceClass($stub);
    }

    public function getFormsViewContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/view-forms.stub');
        return $this->replaceViewDummyStrings($stub)->replaceClass($stub);
    }

    public function getFeatureTestContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/test.stub');
        return $this->replaceFeatureTestDummyStrings($stub)->replaceClass($stub);
    }

    public function getUnitTestContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/unit-test.stub');
        return $this->replaceUnitTestDummyStrings($stub)->replaceClass($stub);
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    protected function replaceControllerDummyStrings(&$stub)
    {
        $stub = str_replace(
            ['master', 'Master'],
            [strtolower($this->modelName), $this->modelName],
            $stub
        );

        return $this;
    }

    protected function replaceFeatureTestDummyStrings(&$stub)
    {
        $stub = str_replace(
            ['Masters'],
            [$this->pluralModelName],
            $stub
        );

        return $this;
    }

    protected function replaceUnitTestDummyStrings(&$stub)
    {
        $stub = str_replace(
            ['Master'],
            [$this->modelName],
            $stub
        );

        return $this;
    }

    protected function replaceMigrationDummyStrings(&$stub)
    {
        $stub = str_replace(
            ['masters', 'Masters'],
            [$this->lowerCasePluralModel, $this->pluralModelName],
            $stub
        );

        return $this;
    }

    protected function replaceViewDummyStrings(&$stub)
    {
        $stub = str_replace(
            ['Master', 'master', 'masters'],
            [$this->modelName, strtolower($this->modelName), $this->lowerCasePluralModel],
            $stub
        );

        return $this;
    }

    protected function replaceClass($stub)
    {
        $class = str_plural($this->modelName);

        return str_replace('DummyClass', $class, $stub);
    }

    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }
}
