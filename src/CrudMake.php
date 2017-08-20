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

        $this->callSilent('make:model', ['name' => $model]);
        $this->info($model.' model generated.');
        $this->callSilent('make:controller', ['name' => $pluralModel.'Controller']);
        $this->info($pluralModel.'Controller generated.');

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$lowerCasePluralModel.'_table.php');
        $this->files->put($migrationFilePath, $this->files->get(__DIR__.'/stubs/migration-create.stub'));
        $this->info($model.' table migration generated.');

        $viewPath = resource_path('views/'.$lowerCasePluralModel);
        if (! $this->files->isDirectory($viewPath)) {
            $this->files->makeDirectory($viewPath, 0777, true, true);
        }

        $this->files->put($viewPath.'/index.blade.php', $this->files->get(__DIR__.'/stubs/view-index.stub'));
        $this->files->put($viewPath.'/forms.blade.php', $this->files->get(__DIR__.'/stubs/view-forms.stub'));
        $this->info($model.' view files generated.');

        $this->callSilent('make:test', ['name' => 'Manage'.$pluralModel.'Test']);
        $this->info('Manage'.$pluralModel.'Test generated.');
        $this->callSilent('make:test', ['name' => 'Models/'.$model.'Test', '--unit' => true]);
        $this->info($model.'Test (model) generated.');

        $this->info('CRUD files generated successfully!');
    }
}
