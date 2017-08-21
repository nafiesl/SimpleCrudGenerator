<?php

namespace Luthfi\CrudGenerator;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CrudMake extends Command
{
    private $files;

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
    public function fire()
    {
        $model = $this->argument('name');
        $pluralModel = str_plural($model);
        $lowerCasePluralModel = strtolower($pluralModel);

        $this->generateModel($model);
        $this->generateController($pluralModel);
        $this->generateMigration($model, $lowerCasePluralModel);
        $this->generateViews($model, $lowerCasePluralModel);
        $this->generateTests($model, $pluralModel);

        $this->info('CRUD files generated successfully!');
    }

    public function generateModel($model)
    {
        $this->callSilent('make:model', ['name' => $model]);;

        $this->info($model.' model generated.');
    }

    public function generateController($pluralModelName)
    {
        if (! $this->files->isDirectory(app_path('Http/Controllers'))) {
            $this->files->makeDirectory(app_path('Http/Controllers'), 0777, true, true);
        }

        $controllerPath = app_path('Http/Controllers/'.$pluralModelName.'Controller.php');
        $this->files->put($controllerPath, $this->files->get(__DIR__.'/stubs/controller.model.stub'));

        $this->info($pluralModelName.'Controller generated.');
    }
    public function generateMigration($model, $lowerCasePluralModel)
    {
        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$lowerCasePluralModel.'_table.php');
        $this->files->put($migrationFilePath, $this->files->get(__DIR__.'/stubs/migration-create.stub'));

        $this->info($model.' table migration generated.');
    }

    public function generateViews($model, $lowerCasePluralModel)
    {
        $viewPath = resource_path('views/'.$lowerCasePluralModel);
        if (! $this->files->isDirectory($viewPath)) {
            $this->files->makeDirectory($viewPath, 0777, true, true);
        }

        $this->files->put($viewPath.'/index.blade.php', $this->files->get(__DIR__.'/stubs/view-index.stub'));
        $this->files->put($viewPath.'/forms.blade.php', $this->files->get(__DIR__.'/stubs/view-forms.stub'));

        $this->info($model.' view files generated.');
    }

    public function generateTests($model, $pluralModelName)
    {
        $this->callSilent('make:test', ['name' => 'Manage'.$pluralModelName.'Test']);
        $this->info('Manage'.$pluralModelName.'Test generated.');

        $this->callSilent('make:test', ['name' => 'Models/'.$model.'Test', '--unit' => true]);
        $this->info($model.'Test (model) generated.');
    }
}
