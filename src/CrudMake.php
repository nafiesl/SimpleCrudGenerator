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
    private $singleModelName;

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
        $this->generateResourceRoute();

        $this->generateModel();
        $this->generateMigration();
        $this->generateController();
        $this->generateViews();
        $this->generateLangFile();
        $this->generateModelFactory();
        $this->generateTests();

        $this->info('CRUD files generated successfully!');
    }

    public function getModelName()
    {
        $this->modelName = $this->argument('name');

        $this->pluralModelName = str_plural($this->modelName);
        $this->lowerCasePluralModel = strtolower($this->pluralModelName);
        $this->singleModelName = strtolower($this->modelName);
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

    public function generateLangFile()
    {
        $langPath = $this->makeDirectory(resource_path('lang/en'));

        $this->files->put($langPath.'/'.$this->singleModelName.'.php', $this->getLangFileContent());

        $this->info($this->singleModelName.' lang files generated.');
    }

    public function generateModelFactory()
    {
        $modelFactoryPath = $this->makeDirectory(database_path('factories'));

        $this->files->put($modelFactoryPath.'/'.$this->modelName.'Factory.php', $this->getModelFactoryContent());

        $this->info($this->singleModelName.' model factory generated.');
    }

    public function generateTests()
    {
        $this->createBrowserKitBaseTestClass();

        $featureTestPath = $this->makeDirectory(base_path('tests/Feature'));
        $this->files->put("{$featureTestPath}/Manage{$this->pluralModelName}Test.php", $this->getFeatureTestContent());
        $this->info('Manage'.$this->pluralModelName.'Test generated.');

        $unitTestPath = $this->makeDirectory(base_path('tests/Unit/Models'));
        $this->files->put("{$unitTestPath}/{$this->modelName}Test.php", $this->getUnitTestContent());
        $this->info($this->modelName.'Test (model) generated.');
    }

    public function createBrowserKitBaseTestClass()
    {
        $testsPath = base_path('tests');
        if (! $this->files->isDirectory($testsPath)) {
            $this->files->makeDirectory($testsPath, 0777, true, true);
        }

        if (! $this->files->exists($testsPath.'/BrowserKitTest.php')) {
            $this->files->put($testsPath.'/BrowserKitTest.php', $this->getBrowserKitBaseTestContent());
        }

        $this->info('BrowserKitTest generated.');
    }

    public function generateResourceRoute()
    {
        $webRoutePath = $this->makeRouteFile(base_path('routes'), 'web.php');
        $this->files->append($webRoutePath, $this->getWebRouteContent());
        $this->info($this->modelName.' resource route generated on routes/web.php.');
    }

    public function getControllerContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/controller.model.stub');
        return $this->replaceStubString($stub);
    }

    private function getMigrationContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/migration-create.stub');
        return $this->replaceStubString($stub);
    }

    public function getIndexViewContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/view-index.stub');
        return $this->replaceStubString($stub);
    }

    public function getFormsViewContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/view-forms.stub');
        return $this->replaceStubString($stub);
    }

    public function getLangFileContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/lang.stub');
        return $this->replaceStubString($stub);
    }

    public function getModelFactoryContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/model-factory.stub');
        return $this->replaceStubString($stub);
    }

    public function getBrowserKitBaseTestContent()
    {
        return $this->files->get(__DIR__.'/stubs/test-browserkit-base-class.stub');
    }

    public function getFeatureTestContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/test-feature.stub');
        return $this->replaceStubString($stub);
    }

    public function getUnitTestContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/test-unit.stub');
        return $this->replaceStubString($stub);
    }

    public function getWebRouteContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/route-web.stub');
        return $this->replaceStubString($stub);
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    protected function makeRouteFile($routeDirPath, $filename)
    {
        if (! $this->files->isDirectory($routeDirPath)) {
            $this->files->makeDirectory($routeDirPath, 0777, true, true);
        }

        if (! $this->files->exists($routeDirPath.'/'.$filename)) {
            $this->files->put($routeDirPath.'/'.$filename, "<?php\n");
        }

        return $routeDirPath.'/'.$filename;
    }

    protected function replaceStubString($stub)
    {
        $stub = str_replace(
            ['Masters', 'Master', 'master', 'masters'],
            [$this->pluralModelName, $this->modelName, $this->singleModelName, $this->lowerCasePluralModel],
            $stub
        );

        return $stub;
    }

    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }
}
