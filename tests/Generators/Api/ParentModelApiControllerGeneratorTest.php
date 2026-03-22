<?php

namespace Tests\Generators\Api;

use Tests\TestCase;

class ParentModelApiControllerGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_controller_class_content()
    {
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud-api', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/Api/{$this->plural_parent_model_name}/{$this->model_name}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers\Api\\{$this->plural_parent_model_name};

use {$this->full_model_name};
use {$this->full_parent_model_name};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class {$this->model_name}Controller extends Controller
{
    public function index(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name})
    {
        \${$this->single_model_var_name}Query = \${$this->single_parent_model_var_name}->{$this->collection_model_var_name}();
        \${$this->single_model_var_name}Query->where('title', 'like', '%'.\$request->get('q').'%');
        \${$this->single_model_var_name}Query->orderBy('title');
        \${$this->collection_model_var_name} = \${$this->single_model_var_name}Query->paginate(25);

        return \${$this->collection_model_var_name};
    }

    public function store(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name})
    {
        \$this->authorize('create', new {$this->model_name});

        \$new{$this->model_name} = \$request->validate([
            'title' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$new{$this->model_name}['creator_id'] = auth()->id();

        \${$this->single_model_var_name} = \${$this->single_parent_model_var_name}->{$this->collection_model_var_name}()->create(\$new{$this->model_name});

        return response()->json([
            'message' => __('{$this->lang_name}.created'),
            'data' => \${$this->single_model_var_name},
        ], 201);
    }

    public function show({$this->parent_model_name} \${$this->single_parent_model_var_name}, {$this->model_name} \${$this->single_model_var_name})
    {
        return \${$this->single_model_var_name};
    }

    public function update(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name}, {$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('update', \${$this->single_model_var_name});

        \${$this->single_model_var_name}Data = \$request->validate([
            'title' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \${$this->single_model_var_name}->update(\${$this->single_model_var_name}Data);

        return response()->json([
            'message' => __('{$this->lang_name}.updated'),
            'data' => \${$this->single_model_var_name},
        ]);
    }

    public function destroy(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name}, {$this->model_name} \${$this->single_model_var_name})
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
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/Api/{$this->plural_parent_model_name}/{$this->model_name}Controller.php")));
    }
}
