<?php

namespace Luthfi\CrudGenerator;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Luthfi\CrudGenerator\Generators\ControllerGenerator;
use Luthfi\CrudGenerator\Generators\MigrationGenerator;
use Luthfi\CrudGenerator\Generators\ModelGenerator;

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
     * @param Filesystem $files Put generated file content to application file system
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;

        $this->stubModelNames = [
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

        if (! $this->modelExists()) {
            $this->generateResourceRoute();

            app(ModelGenerator::class, ['command' => $this])->generate();
            app(MigrationGenerator::class, ['command' => $this])->generate();
            app(ControllerGenerator::class, ['command' => $this])->generate();
            $this->generateViews();
            $this->generateLangFile();
            $this->generateModelFactory();
            $this->generateTests();

            $this->info('CRUD files generated successfully!');
        } else {
            $this->error("{$this->modelNames['model_name']} model already exists.");
        }
    }

    /**
     * Generate class properties for model names in different usage
     *
     * @return void
     */
    public function getModelName($modelName = null)
    {
        $modelName = is_null($modelName) ? $this->argument('name') : $modelName;
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
     * Generate the index view and forms view files
     *
     * @return void
     */
    public function generateViews()
    {
        $viewPath = $this->makeDirectory(resource_path('views/'.$this->modelNames['table_name']));

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

        $this->createAppLangFile($langPath);
        $this->generateFile($langPath.'/'.$this->modelNames['lang_name'].'.php', $this->getLangFileContent());

        $this->info($this->modelNames['lang_name'].' lang files generated.');
    }

    /**
     * Generate lang/app.php file if it doesn't exists
     *
     * @param  string $langPath Directory path of lang files
     * @return void
     */
    public function createAppLangFile($langPath)
    {
        if (! $this->files->exists($langPath.'/app.php')) {
            $this->generateFile($langPath.'/app.php', $this->getAppLangFileContent());
            $this->info('lang/app.php generated.');
        }
    }

    /**
     * Get lang/app.php file content
     *
     * @return string
     */
    public function getAppLangFileContent()
    {
        return $this->files->get(__DIR__.'/stubs/lang-app.stub');
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

        $this->info($this->modelNames['model_name'].' model factory generated.');
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

            $this->info('BrowserKitTest generated.');
        }
    }

    /**
     * Generate API resource version route for CRUD Operation
     *
     * @return void
     */
    public function generateResourceRoute()
    {
        $webRoutePath = $this->makeRouteFile(base_path('routes'), 'web.php');
        $this->files->append($webRoutePath, $this->getWebRouteContent());

        $this->info($this->modelNames['model_name'].' resource route generated on routes/web.php.');
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

        $displayModelName = ucwords(str_replace('_', ' ', snake_case($this->modelNames['model_name'])));

        $properLangFileContent = str_replace(
            $this->modelNames['model_name'],
            $displayModelName,
            $this->replaceStubString($stub)
        );

        return $properLangFileContent;
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

        $webRouteFileContent = $this->replaceStubString($stub);

        if (! is_null($parentName = $this->option('parent'))) {

            $pluralModelName = $this->modelNames['plural_model_name'];

            $webRouteFileContent = str_replace(
                $pluralModelName.'Controller',
                $parentName.'\\'.$pluralModelName.'Controller',
                $webRouteFileContent
            );
        }

        return $webRouteFileContent;
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
        return str_replace($this->stubModelNames, $this->modelNames, $stub);
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
