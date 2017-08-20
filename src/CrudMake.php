<?php

namespace Luthfi\CrudGenerator;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class CrudMake extends Command
{
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

        $this->callSilent('make:model', ['name' => $model]);
        $this->info($model.' model generated.');
        $this->callSilent('make:controller', ['name' => $pluralModel.'Controller']);
        $this->info($pluralModel.'Controller generated.');
        $this->callSilent('make:test', ['name' => 'Manage'.$pluralModel.'Test']);
        $this->callSilent('make:test', ['name' => 'Models/'.$model.'Test', '--unit' => true]);

        $this->info('CRUD files generated successfully!');
    }
}
