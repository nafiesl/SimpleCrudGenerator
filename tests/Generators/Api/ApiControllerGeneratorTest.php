<?php

namespace Tests\Generators\Api;

use Tests\TestCase;

class ApiControllerGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_controller_class_content()
    {
        $this->artisan('make:crud-api', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/Api/{$this->plural_model_name}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers\Api;

use {$this->full_model_name};
use Illuminate\Http\Request;

class {$this->plural_model_name}Controller extends Controller
{
    /**
     * Display a listing of the {$this->single_model_var_name}.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \${$this->collection_model_var_name} = {$this->model_name}::where(function (\$query) {
            \$query->where('name', 'like', '%'.request('q').'%');
        })->paginate(25);

        return \${$this->collection_model_var_name};
    }

    /**
     * Store a newly created {$this->single_model_var_name} in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Http\Response
     */
    public function store(Request \$request)
    {
        \$this->authorize('create', new {$this->model_name});

        \$this->validate(\$request, [
            'name' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        \$new{$this->model_name} = \$request->only('name', 'description');
        \$new{$this->model_name}['creator_id'] = auth()->id();

        \${$this->single_model_var_name} = {$this->model_name}::create(\$new{$this->model_name});

        return \${$this->single_model_var_name};
    }

    /**
     * Update the specified {$this->single_model_var_name} in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \\{$this->full_model_name}  \${$this->single_model_var_name}
     * @return \Illuminate\Http\Response
     */
    public function update(Request \$request, {$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('update', \${$this->single_model_var_name});

        \$this->validate(\$request, [
            'name' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        \${$this->single_model_var_name}->update(\$request->only('name', 'description'));

        return \${$this->single_model_var_name};
    }

    /**
     * Remove the specified {$this->single_model_var_name} from storage.
     *
     * @param  \\{$this->full_model_name}  \${$this->single_model_var_name}
     * @return \Illuminate\Http\Response
     */
    public function destroy({$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('delete', \${$this->single_model_var_name});

        \$this->validate(request(), [
            '{$this->lang_name}_id' => 'required',
        ]);

        if (request('{$this->lang_name}_id') == \${$this->single_model_var_name}->id && \${$this->single_model_var_name}->delete()) {
            return response()->json('{$this->lang_name} deleted.', 204);
        }

        return response()->json('Unprocessable Entity.', 422);
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/Api/{$this->plural_model_name}Controller.php")));
    }
}
