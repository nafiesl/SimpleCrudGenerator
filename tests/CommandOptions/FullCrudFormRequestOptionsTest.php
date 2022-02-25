<?php

namespace Tests\CommandOptions;

use Illuminate\Contracts\Console\Kernel;
use Tests\TestCase;

class FullCrudFormRequestOptionsTest extends TestCase
{
    /** @test */
    public function it_can_generate_form_request_classes()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true, '--form-requests' => true]);

        $this->assertStringNotContainsString("{$this->model_name} model already exists.", app(Kernel::class)->output());

        $this->assertFileExists(app_path('Models/'.$this->model_name.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));
        $this->assertFileExists(app_path("Http/Requests/{$this->plural_model_name}/CreateRequest.php"));
        $this->assertFileExists(app_path("Http/Requests/{$this->plural_model_name}/UpdateRequest.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$this->table_name}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/create.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/edit.blade.php"));
        $this->assertFileDoesNotExist(resource_path("views/{$this->table_name}/forms.blade.php"));

        $localeConfig = config('app.locale');
        $this->assertFileExists(base_path("lang/{$localeConfig}/{$this->lang_name}.php"));

        $this->assertFileExists(base_path("routes/web.php"));
        $this->assertFileExists(app_path("Policies/{$this->model_name}Policy.php"));
        $this->assertFileExists(database_path("factories/{$this->model_name}Factory.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$this->model_name}Test.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$this->model_name}Test.php"));
    }

    /** @test */
    public function it_can_generate_controller_file_with_form_requests_class()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true, '--form-requests' => true]);

        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

use {$this->full_model_name};
use Illuminate\Http\Request;
use App\Http\Requests\\{$this->plural_model_name}\CreateRequest;
use App\Http\Requests\\{$this->plural_model_name}\UpdateRequest;

class {$this->model_name}Controller extends Controller
{
    public function index(Request \$request)
    {
        \${$this->single_model_var_name}Query = {$this->model_name}::query();
        \${$this->single_model_var_name}Query->where('title', 'like', '%'.\$request->get('q').'%');
        \${$this->single_model_var_name}Query->orderBy('title');
        \${$this->collection_model_var_name} = \${$this->single_model_var_name}Query->paginate(25);

        return view('{$this->table_name}.index', compact('{$this->collection_model_var_name}'));
    }

    public function create()
    {
        \$this->authorize('create', new {$this->model_name});

        return view('{$this->table_name}.create');
    }

    public function store(CreateRequest \${$this->single_model_var_name}CreateForm)
    {
        \${$this->single_model_var_name} = \${$this->single_model_var_name}CreateForm->save();

        return redirect()->route('{$this->table_name}.show', \${$this->single_model_var_name});
    }

    public function show({$this->model_name} \${$this->single_model_var_name})
    {
        return view('{$this->table_name}.show', compact('{$this->single_model_var_name}'));
    }

    public function edit({$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('update', \${$this->single_model_var_name});

        return view('{$this->table_name}.edit', compact('{$this->single_model_var_name}'));
    }

    public function update(UpdateRequest \${$this->single_model_var_name}UpdateForm, {$this->model_name} \${$this->single_model_var_name})
    {
        \${$this->single_model_var_name}->update(\${$this->single_model_var_name}UpdateForm->validated());

        return redirect()->route('{$this->table_name}.show', \${$this->single_model_var_name});
    }

    public function destroy(Request \$request, {$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('delete', \${$this->single_model_var_name});

        \$request->validate(['{$this->lang_name}_id' => 'required']);

        if (\$request->get('{$this->lang_name}_id') == \${$this->single_model_var_name}->id && \${$this->single_model_var_name}->delete()) {
            return redirect()->route('{$this->table_name}.index');
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/{$this->model_name}Controller.php")));
    }

    /** @test */
    public function it_generates_correct_create_form_request_file_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true, '--form-requests' => true]);

        $classFilePath = app_path("Http/Requests/{$this->plural_model_name}/CreateRequest.php");

        $this->assertFileExists($classFilePath);
        $formRequestClassContent = "<?php

namespace App\Http\Requests\\{$this->plural_model_name};

use {$this->full_model_name};
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    public function authorize()
    {
        return \$this->user()->can('create', new {$this->model_name});
    }

    public function rules()
    {
        return [
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ];
    }

    public function save()
    {
        \$new{$this->model_name} = \$this->validated();
        \$new{$this->model_name}['creator_id'] = auth()->id();

        return {$this->model_name}::create(\$new{$this->model_name});
    }
}
";
        $this->assertEquals($formRequestClassContent, file_get_contents($classFilePath));
    }

    /** @test */
    public function it_generates_correct_update_form_request_file_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true, '--form-requests' => true]);

        $classFilePath = app_path("Http/Requests/{$this->plural_model_name}/UpdateRequest.php");

        $this->assertFileExists($classFilePath);
        $formRequestClassContent = "<?php

namespace App\Http\Requests\\{$this->plural_model_name};

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return \$this->user()->can('update', \$this->route('{$this->lang_name}'));
    }

    public function rules()
    {
        return [
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ];
    }
}
";
        $this->assertEquals($formRequestClassContent, file_get_contents($classFilePath));
    }
}
