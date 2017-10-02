<?php

namespace Luthfi\CrudGenerator;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CrudMake extends Command
{
    /**
     * The injected Filesystem class
     *
     * @var Filesystem
     */
    private $files;

    /**
     * Model name that will be generated
     *
     * @var string
     */
    private $modelName;

    /**
     * Model name in plural
     *
     * @var string
     */
    private $pluralModelName;

    /**
     * Lowercased plural model name, used as table name and collection variable name
     *
     * @var string
     */
    private $lowerCasePluralModel;

    /**
     * Lowercased model name, used for single model variable.
     *
     * @var string
     */
    private $singleModelName;

    /**
     * Construct CrudMake class
     * @param Filesystem $files Put generated file content to application file system
     */
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

    /**
     * Generate class properties for model names in different usage
     *
     * @return void
     */
    public function getModelName()
    {
        $this->modelName = $this->argument('name');

        $this->pluralModelName = str_plural($this->modelName);
        $this->lowerCasePluralModel = strtolower($this->pluralModelName);
        $this->singleModelName = strtolower($this->modelName);
    }

    /**
     * Generate the model file
     *
     * @return void
     */
    public function generateModel()
    {
        $this->files->put(app_path($this->modelName.'.php'), $this->getModelContent());

        $this->info($this->modelName.' model generated.');
    }

    /**
     * Generate controller for model CRUD operation
     *
     * @return void
     */
    public function generateController()
    {
        $controllerPath = $this->makeDirectory(app_path('Http/Controllers'));

        $controllerPath = $controllerPath.'/'.$this->pluralModelName.'Controller.php';
        $this->files->put($controllerPath, $this->getControllerContent());

        $this->info($this->pluralModelName.'Controller generated.');
    }

    /**
     * Generate migration file for the model
     *
     * @return void
     */
    public function generateMigration()
    {
        $prefix = date('Y_m_d_His');
        $tableName = $this->lowerCasePluralModel;
        $migrationFilePath = database_path("migrations/{$prefix}_create_{$tableName}_table.php");
        $this->files->put($migrationFilePath, $this->getMigrationContent());

        $this->info($this->modelName.' table migration generated.');
    }

    /**
     * Generate the index view and forms view files
     *
     * @return void
     */
    public function generateViews()
    {
        $viewPath = $this->makeDirectory(resource_path('views/'.$this->lowerCasePluralModel));

        $this->files->put($viewPath.'/index.blade.php', $this->getIndexViewContent());
        $this->files->put($viewPath.'/forms.blade.php', $this->getFormsViewContent());

        $this->info($this->modelName.' view files generated.');
    }

    /**
     * Generate lang file for current model
     *
     * @return void
     */
    public function generateLangFile()
    {
        $langPath = $this->makeDirectory(resource_path('lang/en'));

        $this->files->put($langPath.'/'.$this->singleModelName.'.php', $this->getLangFileContent());

        $this->info($this->singleModelName.' lang files generated.');
    }

    /**
     * Generate model factory file
     *
     * @return void
     */
    public function generateModelFactory()
    {
        $modelFactoryPath = $this->makeDirectory(database_path('factories'));

        $this->files->put($modelFactoryPath.'/'.$this->modelName.'Factory.php', $this->getModelFactoryContent());

        $this->info($this->singleModelName.' model factory generated.');
    }

    /**
     * Generate Feature for CRUD Operation and and Unit Testing for Model behaviour
     * @return void
     */
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

    /**
     * Generate BrowserKitTest class for BaseTestCase
     *
     * @return void
     */
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

    /**
     * Generate API resource version route for CRUD Operation
     * @return [type] [description]
     */
    public function generateResourceRoute()
    {
        $webRoutePath = $this->makeRouteFile(base_path('routes'), 'web.php');
        $this->files->append($webRoutePath, $this->getWebRouteContent());

        $this->info($this->modelName.' resource route generated on routes/web.php.');
    }

    /**
     * Get controller content from controller stub
     *
     * @return string Replaced proper model names in controller file content
     */
    public function getControllerContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/controller.model.stub');
        return $this->replaceStubString($stub);
    }

    /**
     * Get model content from model stub
     *
     * @return string Replaced proper model names in model file content
     */
    public function getModelContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/model.stub');
        return $this->replaceStubString($stub);
    }

    /**
     * Get migration file content from migration stub
     *
     * @return string Replaced proper model names in migration file content
     */
    private function getMigrationContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/migration-create.stub');
        return $this->replaceStubString($stub);
    }

    /**
     * Get index view file content from index view stub
     *
     * @return string Replaced proper model names in view file content
     */
    public function getIndexViewContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/view-index.stub');
        return $this->replaceStubString($stub);
    }

    /**
     * Get forms view file content from forms view stub
     *
     * @return string Replaced proper model names in forms view file content
     */
    public function getFormsViewContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/view-forms.stub');
        return $this->replaceStubString($stub);
    }

    /**
     * Get lang file content from lang file stub
     *
     * @return string Replaced proper model names in lang file content
     */
    public function getLangFileContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/lang.stub');
        return $this->replaceStubString($stub);
    }

    /**
     * Get model factory file content from model factory stub
     *
     * @return string Replaced proper model names in model factory file content
     */
    public function getModelFactoryContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/model-factory.stub');
        return $this->replaceStubString($stub);
    }

    /**
     * Get BrowserKitBaseTest class file content
     *
     * @return string
     */
    public function getBrowserKitBaseTestContent()
    {
        return $this->files->get(__DIR__.'/stubs/test-browserkit-base-class.stub');
    }

    /**
     * Get feature test file content from feature test stub
     *
     * @return string Replaced proper model names in feature test file content
     */
    public function getFeatureTestContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/test-feature.stub');
        return $this->replaceStubString($stub);
    }

    /**
     * Get unit test file content from unit test stub
     *
     * @return string Replaced proper model names in unit test file content
     */
    public function getUnitTestContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/test-unit.stub');
        return $this->replaceStubString($stub);
    }

    /**
     * Get web route content from route web stub
     *
     * @return string Replaced proper model names in route web file content
     */
    public function getWebRouteContent()
    {
        $stub = $this->files->get(__DIR__.'/stubs/route-web.stub');
        return $this->replaceStubString($stub);
    }

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
     * Create php route file if not exists
     * @param  string $routeDirPath Absolute directory path
     * @param  string $filename     File name to be created
     * @return string               Absolute path of create route file
     */
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

    /**
     * Replace all string of model names
     *
     * @param  string $stub String of file or class stub with default content
     * @return string       Replaced content
     */
    protected function replaceStubString($stub)
    {
        $stub = str_replace(
            ['Masters', 'Master', 'master', 'masters'],
            [$this->pluralModelName, $this->modelName, $this->singleModelName, $this->lowerCasePluralModel],
            $stub
        );

        return $stub;
    }
}
