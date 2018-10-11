<?php

namespace Tests\CommandOptions;

use Tests\TestCase;

class FullCrudFormRequestOptionsTest extends TestCase
{
    /** @test */
    public function it_can_generate_controller_file_with_form_requests_class()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true, '--form-requests' => true]);

        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

use {$this->full_model_name};
use Illuminate\Http\Request;

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
     * @param  \App\Http\Requests\\{$this->plural_model_name}\CreateRequest  \$create{$this->model_name}Form
     * @return \Illuminate\Routing\Redirector
     */
    public function store(CreateRequest \$create{$this->model_name}Form)
    {
        \${$this->single_model_var_name} = \$create{$this->model_name}Form->save();

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
     * @param  \\{$this->full_model_name}  \${$this->single_model_var_name}
     * @return \Illuminate\Routing\Redirector
     */
    public function destroy({$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('delete', \${$this->single_model_var_name});

        request()->validate([
            '{$this->lang_name}_id' => 'required',
        ]);

        if (request('{$this->lang_name}_id') == \${$this->single_model_var_name}->id && \${$this->single_model_var_name}->delete()) {
            \$routeParam = request()->only('page', 'q');

            return redirect()->route('{$this->table_name}.index', \$routeParam);
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/{$this->model_name}Controller.php")));
    }
}
