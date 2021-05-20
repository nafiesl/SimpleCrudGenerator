<?php

namespace Tests\Generators\Api;

use Tests\TestCase;

class ApiControllerGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_controller_class_content()
    {
        $this->artisan('make:crud-api', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/Api/{$this->model_name}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers\Api;

use {$this->full_model_name};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class {$this->model_name}Controller extends Controller
{
    public function index(Request \$request)
    {
        \${$this->single_model_var_name}Query = {$this->model_name}::query();
        \${$this->single_model_var_name}Query->where('title', 'like', '%'.\$request->get('q').'%');
        \${$this->single_model_var_name}Query->orderBy('title');
        \${$this->collection_model_var_name} = \${$this->single_model_var_name}Query->paginate(25);

        return \${$this->collection_model_var_name};
    }

    public function store(Request \$request)
    {
        \$this->authorize('create', new {$this->model_name});

        \$new{$this->model_name} = \$request->validate([
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$new{$this->model_name}['creator_id'] = auth()->id();

        \${$this->single_model_var_name} = {$this->model_name}::create(\$new{$this->model_name});

        return response()->json([
            'message' => __('{$this->lang_name}.created'),
            'data'    => \${$this->single_model_var_name},
        ], 201);
    }

    public function show({$this->model_name} \${$this->single_model_var_name})
    {
        return \${$this->single_model_var_name};
    }

    public function update(Request \$request, {$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('update', \${$this->single_model_var_name});

        \${$this->single_model_var_name}Data = \$request->validate([
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \${$this->single_model_var_name}->update(\${$this->single_model_var_name}Data);

        return response()->json([
            'message' => __('{$this->lang_name}.updated'),
            'data'    => \${$this->single_model_var_name},
        ]);
    }

    public function destroy(Request \$request, {$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('delete', \${$this->single_model_var_name});

        \$request->validate(['{$this->lang_name}_id' => 'required']);

        if (\$request->get('{$this->lang_name}_id') == \${$this->single_model_var_name}->id && \${$this->single_model_var_name}->delete()) {
            return response()->json(['message' => __('{$this->lang_name}.deleted')]);
        }

        return response()->json('Unprocessable Entity.', 422);
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/Api/{$this->model_name}Controller.php")));
    }
}
