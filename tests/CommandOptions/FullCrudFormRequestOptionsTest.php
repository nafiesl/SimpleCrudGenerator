<?php

namespace Tests\CommandOptions;

use Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;

class FullCrudFormRequestOptionsTest extends TestCase
{
    /** @test */
    public function it_can_generate_form_request_classes()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true, '--form-requests' => true]);

        $this->assertNotContains("{$this->model_name} model already exists.", app(Kernel::class)->output());

        $this->assertFileExists(app_path($this->model_name.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));
        $this->assertFileExists(app_path("Http/Requests/{$this->plural_model_name}/CreateRequest.php"));
        $this->assertFileExists(app_path("Http/Requests/{$this->plural_model_name}/UpdateRequest.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$this->table_name}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/create.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/edit.blade.php"));
        $this->assertFileNotExists(resource_path("views/{$this->table_name}/forms.blade.php"));

        $localeConfig = config('app.locale');
        $this->assertFileExists(resource_path("lang/{$localeConfig}/{$this->lang_name}.php"));

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
    /**
     * Display a listing of the {$this->single_model_var_name}.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        \${$this->single_model_var_name}Query = {$this->model_name}::query();
        \${$this->single_model_var_name}Query->where('name', 'like', '%'.request('q').'%');
        \${$this->collection_model_var_name} = \${$this->single_model_var_name}Query->paginate(25);

        return view('{$this->table_name}.index', compact('{$this->collection_model_var_name}'));
    }

    /**
     * Show the form for creating a new {$this->single_model_var_name}.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        \$this->authorize('create', new {$this->model_name});

        return view('{$this->table_name}.create');
    }

    /**
     * Store a newly created {$this->single_model_var_name} in storage.
     *
     * @param  \App\Http\Requests\\{$this->plural_model_name}\CreateRequest  \${$this->single_model_var_name}CreateForm
     * @return \Illuminate\Routing\Redirector
     */
    public function store(CreateRequest \${$this->single_model_var_name}CreateForm)
    {
        \${$this->single_model_var_name} = \${$this->single_model_var_name}CreateForm->save();

        return redirect()->route('{$this->table_name}.show', \${$this->single_model_var_name});
    }

    /**
     * Display the specified {$this->single_model_var_name}.
     *
     * @param  \\{$this->full_model_name}  \${$this->single_model_var_name}
     * @return \Illuminate\View\View
     */
    public function show({$this->model_name} \${$this->single_model_var_name})
    {
        return view('{$this->table_name}.show', compact('{$this->single_model_var_name}'));
    }

    /**
     * Show the form for editing the specified {$this->single_model_var_name}.
     *
     * @param  \\{$this->full_model_name}  \${$this->single_model_var_name}
     * @return \Illuminate\View\View
     */
    public function edit({$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('update', \${$this->single_model_var_name});

        return view('{$this->table_name}.edit', compact('{$this->single_model_var_name}'));
    }

    /**
     * Update the specified {$this->single_model_var_name} in storage.
     *
     * @param  \App\Http\Requests\\{$this->plural_model_name}\UpdateRequest  \${$this->single_model_var_name}UpdateForm
     * @param  \\{$this->full_model_name}  \${$this->single_model_var_name}
     * @return \Illuminate\Routing\Redirector
     */
    public function update(UpdateRequest \${$this->single_model_var_name}UpdateForm, {$this->model_name} \${$this->single_model_var_name})
    {
        \${$this->single_model_var_name}->update(\${$this->single_model_var_name}UpdateForm->validated());

        return redirect()->route('{$this->table_name}.show', \${$this->single_model_var_name});
    }

    /**
     * Remove the specified {$this->single_model_var_name} from storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \\{$this->full_model_name}  \${$this->single_model_var_name}
     * @return \Illuminate\Routing\Redirector
     */
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
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \$this->user()->can('create', new {$this->model_name});
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required|max:60',
            'description' => 'nullable|max:255',
        ];
    }

    /**
     * Save proposal to database.
     *
     * @return \\{$this->full_model_name}
     */
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
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return \$this->user()->can('update', \$this->route('{$this->lang_name}'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'required|max:60',
            'description' => 'nullable|max:255',
        ];
    }
}
";
        $this->assertEquals($formRequestClassContent, file_get_contents($classFilePath));
    }
}
