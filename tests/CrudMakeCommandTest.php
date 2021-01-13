<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

class CrudMakeCommandTest extends TestCase
{
    /** @test */
    public function it_can_generate_simple_crud_files()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertStringNotContainsString("{$this->model_name} model already exists.", app(Kernel::class)->output());

        $this->assertFileExists(app_path('Models/'.$this->model_name.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$this->table_name}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/create.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/edit.blade.php"));
        $this->assertFileDoesNotExist(resource_path("views/{$this->table_name}/forms.blade.php"));

        $localeConfig = config('app.locale');
        $this->assertFileExists(resource_path("lang/{$localeConfig}/{$this->lang_name}.php"));

        $this->assertFileExists(base_path("routes/web.php"));
        $this->assertFileExists(app_path("Policies/{$this->model_name}Policy.php"));
        $this->assertFileExists(database_path("factories/{$this->model_name}Factory.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$this->model_name}Test.php"));
        $this->assertFileExists(base_path("tests/Unit/Policies/{$this->model_name}PolicyTest.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$this->model_name}Test.php"));
    }

    /** @test */
    public function it_generates_crud_files_for_existing_model()
    {
        $this->mockConsoleOutput = true;
        $this->artisan('make:model', ['name' => 'Models/'.$this->model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true])
            ->expectsQuestion('Model file exists, are you sure to generate CRUD files?', true);

        $this->assertFileExists(app_path('Models/'.$this->model_name.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$this->table_name}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/create.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/edit.blade.php"));
        $this->assertFileDoesNotExist(resource_path("views/{$this->table_name}/forms.blade.php"));

        $localeConfig = config('app.locale');
        $this->assertFileExists(resource_path("lang/{$localeConfig}/{$this->lang_name}.php"));

        $this->assertFileExists(base_path("routes/web.php"));
        $this->assertFileExists(app_path("Policies/{$this->model_name}Policy.php"));
        $this->assertFileExists(database_path("factories/{$this->model_name}Factory.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$this->model_name}Test.php"));
        $this->assertFileExists(base_path("tests/Unit/Policies/{$this->model_name}PolicyTest.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$this->model_name}Test.php"));
    }

    /** @test */
    public function it_cannot_generate_crud_files_if_namespaced_model_exists()
    {
        $this->mockConsoleOutput = true;
        $this->artisan('make:model', ['name' => 'App/Entities/Projects/Problem', '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => 'Entities/Projects/Problem', '--no-interaction' => true])
            ->expectsQuestion('Model file exists, are you sure to generate CRUD files?', false)
            ->expectsOutput('Problem model already exists.');

        $this->assertFileExists(app_path('Entities/Projects/Problem.php'));
        $this->assertFileDoesNotExist(app_path("Http/Controllers/ProblemsController.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_problems_table.php');
        $this->assertFileDoesNotExist($migrationFilePath);

        $this->assertFileDoesNotExist(resource_path("views/problems/index.blade.php"));
        $this->assertFileDoesNotExist(resource_path("views/problems/create.blade.php"));
        $this->assertFileDoesNotExist(resource_path("views/problems/edit.blade.php"));
        $this->assertFileDoesNotExist(resource_path("views/problems/forms.blade.php"));

        $localeConfig = config('app.locale');
        $this->assertFileDoesNotExist(resource_path("lang/{$localeConfig}/{$this->lang_name}.php"));

        $this->assertFileDoesNotExist(app_path("Policies/ProblemPolicy.php"));
        $this->assertFileDoesNotExist(database_path("factories/ProblemFactory.php"));
        $this->assertFileDoesNotExist(base_path("tests/Unit/Models/ProblemTest.php"));
        $this->assertFileDoesNotExist(base_path("tests/Unit/Policies/ProblemPolicyTest.php"));
        $this->assertFileDoesNotExist(base_path("tests/Feature/ManageProblemTest.php"));

        $this->removeFileOrDir(app_path('Entities/Projects'));
        $this->removeFileOrDir(resource_path('views/problems'));
        $this->removeFileOrDir(resource_path("lang/en/problem.php"));
    }

    /** @test */
    public function it_can_generate_crud_files_for_namespaced_model()
    {
        $inputName = 'Entities/References/Category';
        $modelName = 'Category';
        $pluralModelName = 'Categories';
        $tableName = 'categories';
        $langName = 'category';
        $modelPath = 'Entities/References';

        $this->artisan('make:crud', ['name' => $inputName, '--no-interaction' => true]);

        $this->assertStringNotContainsString("{$modelName} model already exists.", app(Kernel::class)->output());

        $this->assertFileExists(app_path($modelPath.'/'.$modelName.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$modelName}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$tableName.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$tableName}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$tableName}/create.blade.php"));
        $this->assertFileExists(resource_path("views/{$tableName}/edit.blade.php"));
        $this->assertFileDoesNotExist(resource_path("views/{$tableName}/forms.blade.php"));

        $localeConfig = config('app.locale');
        $this->assertFileExists(resource_path("lang/{$localeConfig}/{$langName}.php"));

        $this->assertFileExists(app_path("Policies/{$modelName}Policy.php"));
        $this->assertFileExists(database_path("factories/{$modelName}Factory.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$modelName}Test.php"));
        $this->assertFileExists(base_path("tests/Unit/Policies/{$modelName}PolicyTest.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$modelName}Test.php"));
    }

    /** @test */
    public function it_can_generate_crud_files_with_parent_option()
    {
        $inputName = 'Entities/References/Category';
        $modelName = 'Category';
        $parentName = 'Projects';
        $pluralModelName = 'Categories';
        $tableName = 'categories';
        $langName = 'category';
        $modelPath = 'Entities/References';

        $this->artisan('make:crud', ['name' => $inputName, '--parent' => $parentName, '--no-interaction' => true]);

        $this->assertStringNotContainsString("{$modelName} model already exists.", app(Kernel::class)->output());

        $this->assertFileExists(app_path($modelPath.'/'.$modelName.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$parentName}/{$modelName}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$tableName.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$tableName}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$tableName}/create.blade.php"));
        $this->assertFileExists(resource_path("views/{$tableName}/edit.blade.php"));
        $this->assertFileDoesNotExist(resource_path("views/{$tableName}/forms.blade.php"));

        $localeConfig = config('app.locale');
        $this->assertFileExists(resource_path("lang/{$localeConfig}/{$langName}.php"));

        $this->assertFileExists(database_path("factories/{$modelName}Factory.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$modelName}Test.php"));
        $this->assertFileExists(base_path("tests/Unit/Policies/{$modelName}PolicyTest.php"));
        $this->assertFileExists(app_path("Policies/{$parentName}/{$modelName}Policy.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$modelName}Test.php"));
    }
}
