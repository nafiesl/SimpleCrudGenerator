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
     * Array of defined model names
     *
     * @var array
     */
    private $modelNames = [];

    /**
     * Array of stub's model names
     *
     * @var array
     */
    public $stubModelNames = ['Masters', 'Master', 'masters', 'master'];

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
    protected $description = 'Create simple Laravel complate CRUD files of given model name.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->getModelName();

        if ( ! $this->modelExists()) {
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

        $this->error("{$this->modelNames['model_name']} model already exists.");
    }

    /**
     * Generate class properties for model names in different usage
     *
     * @return void
     */
    public function getModelName($modelName = null)
    {
        $modelName = is_null($modelName) ? $this->argument('name') : $modelName;

        return $this->modelNames = [
            'plural_model_name' => str_plural($modelName),
            'model_name' => $modelName,
            'lowercase_plural_model_name' => strtolower(str_plural($modelName)),
            'lowercase_single_model_name' => strtolower($modelName),
        ];
    }

    /**
     * Check for Model file existance
     *
     * @return void
     */
    public function modelExists()
    {
        return $this->files->exists(app_path($this->modelNames['model_name'].'.php'));
    }

    /**
     * Generate the model file
     *
     * @return void
     */
    public function generateModel()
    {
        $this->generateFile(app_path($this->modelNames['model_name'].'.php'), $this->getModelContent());

        $this->info($this->modelNames['model_name'].' model generated.');
    }

    /**
     * Generate controller for model CRUD operation
     *
     * @return void
     */
    public function generateController()
    {
        $controllerPath = $this->makeDirectory(app_path('Http/Controllers'));

        $controllerPath = $controllerPath.'/'.$this->modelNames['plural_model_name'].'Controller.php';
        $this->generateFile($controllerPath, $this->getControllerContent());

        $this->info($this->modelNames['plural_model_name'].'Controller generated.');
    }

    /**
     * Generate migration file for the model
     *
     * @return void
     */
    public function generateMigration()
    {
        $prefix = date('Y_m_d_His');
        $tableName = $this->modelNames['lowercase_plural_model_name'];

        $migrationPath = $this->makeDirectory(database_path('migrations'));

        $migrationFilePath = $migrationPath.'/'.$prefix."_create_{$tableName}_table.php";
        $this->generateFile($migrationFilePath, $this->getMigrationContent());

        $this->info($this->modelNames['model_name'].' table migration generated.');
    }

    /**
     * Generate the index view and forms view files
     *
     * @return void
     */
    public function generateViews()
    {
        $viewPath = $this->makeDirectory(resource_path('views/'.$this->modelNames['lowercase_plural_model_name']));

        $this->generateFile($viewPath.'/index.blade.php', $this->getIndexViewContent());
        $this->generateFile($viewPath.'/forms.blade.php', $this->getFormsViewContent());

        $this->info($this->modelNames['model_name'].' view files generated.');
    }

    /**
     * Generate lang file for current model
     *
     * @return void
     */
    public function generateLangFile()
    {
        $langPath = $this->makeDirectory(resource_path('lang/en'));

        $this->generateFile($langPath.'/'.$this->modelNames['lowercase_single_model_name'].'.php', $this->getLangFileContent());

        $this->info($this->modelNames['lowercase_single_model_name'].' lang files generated.');
    }

    /**
     * Generate model factory file
     *
     * @return void
     */
    public function generateModelFactory()
    {
        $modelFactoryPath = $this->makeDirectory(database_path('factories'));

        $this->generateFile(
            $modelFactoryPath.'/'.$this->modelNames['model_name'].'Factory.php',
            $this->getModelFactoryContent()
        );

        $this->info($this->modelNames['lowercase_single_model_name'].' model factory generated.');
    }

    /**
     * Generate Feature for CRUD Operation and and Unit Testing for Model behaviour
     * @return void
     */
    public function generateTests()
    {
        $this->createBrowserKitBaseTestClass();

        $featureTestPath = $this->makeDirectory(base_path('tests/Feature'));
        $this->generateFile("{$featureTestPath}/Manage{$this->modelNames['plural_model_name']}Test.php", $this->getFeatureTestContent());
        $this->info('Manage'.$this->modelNames['plural_model_name'].'Test generated.');

        $unitTestPath = $this->makeDirectory(base_path('tests/Unit/Models'));
        $this->generateFile("{$unitTestPath}/{$this->modelNames['model_name']}Test.php", $this->getUnitTestContent());
        $this->info($this->modelNames['model_name'].'Test (model) generated.');
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
            $this->generateFile($testsPath.'/BrowserKitTest.php', $this->getBrowserKitBaseTestContent());
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

        $this->info($this->modelNames['model_name'].' resource route generated on routes/web.php.');
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
            $this->generateFile($routeDirPath.'/'.$filename, "<?php\n");
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
        return str_replace($this->stubModelNames, $this->modelNames, $stub );
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
}
