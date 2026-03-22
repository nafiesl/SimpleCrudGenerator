<?php

namespace Tests\Generators;

use Tests\TestCase;

class ParentModelFullControllerGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_controller_class_content()
    {
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => $this->model_name, '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/{$this->plural_parent_model_name}/{$this->model_name}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers\\{$this->plural_parent_model_name};

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

        return view('{$this->parent_table_name}.{$this->table_name}.index', compact('{$this->single_parent_model_var_name}', '{$this->collection_model_var_name}'));
    }

    public function create({$this->parent_model_name} \${$this->single_parent_model_var_name})
    {
        \$this->authorize('create', new {$this->model_name});

        return view('{$this->parent_table_name}.{$this->table_name}.create', compact('{$this->single_parent_model_var_name}'));
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

        return redirect()->route('{$this->parent_table_name}.{$this->table_name}.show', [\${$this->single_parent_model_var_name}, \${$this->single_model_var_name}]);
    }

    public function show({$this->parent_model_name} \${$this->single_parent_model_var_name}, {$this->model_name} \${$this->single_model_var_name})
    {
        return view('{$this->parent_table_name}.{$this->table_name}.show', compact('{$this->single_parent_model_var_name}', '{$this->single_model_var_name}'));
    }

    public function edit({$this->parent_model_name} \${$this->single_parent_model_var_name}, {$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('update', \${$this->single_model_var_name});

        return view('{$this->parent_table_name}.{$this->table_name}.edit', compact('{$this->single_parent_model_var_name}', '{$this->single_model_var_name}'));
    }

    public function update(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name}, {$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('update', \${$this->single_model_var_name});

        \${$this->single_model_var_name}Data = \$request->validate([
            'title' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \${$this->single_model_var_name}->update(\${$this->single_model_var_name}Data);

        return redirect()->route('{$this->parent_table_name}.{$this->table_name}.show', [\${$this->single_parent_model_var_name}, \${$this->single_model_var_name}]);
    }

    public function destroy(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name}, {$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('delete', \${$this->single_model_var_name});

        \$request->validate(['{$this->lang_name}_id' => 'required']);

        if (\$request->get('{$this->lang_name}_id') == \${$this->single_model_var_name}->id && \${$this->single_model_var_name}->delete()) {
            return redirect()->route('{$this->parent_table_name}.{$this->table_name}.index', \${$this->single_parent_model_var_name});
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/{$this->plural_parent_model_name}/{$this->model_name}Controller.php")));
    }

    /** @test */
    public function it_creates_correct_controller_class_content_for_namespaced_model()
    {
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => 'Entities/References/Category', '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/{$this->plural_parent_model_name}/CategoryController.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers\\{$this->plural_parent_model_name};

use App\Entities\References\Category;
use {$this->full_parent_model_name};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name})
    {
        \$categoryQuery = \${$this->single_parent_model_var_name}->categories();
        \$categoryQuery->where('title', 'like', '%'.\$request->get('q').'%');
        \$categoryQuery->orderBy('title');
        \$categories = \$categoryQuery->paginate(25);

        return view('{$this->parent_table_name}.categories.index', compact('{$this->single_parent_model_var_name}', 'categories'));
    }

    public function create({$this->parent_model_name} \${$this->single_parent_model_var_name})
    {
        \$this->authorize('create', new Category);

        return view('{$this->parent_table_name}.categories.create', compact('{$this->single_parent_model_var_name}'));
    }

    public function store(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name})
    {
        \$this->authorize('create', new Category);

        \$newCategory = \$request->validate([
            'title' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$newCategory['creator_id'] = auth()->id();

        \$category = \${$this->single_parent_model_var_name}->categories()->create(\$newCategory);

        return redirect()->route('{$this->parent_table_name}.categories.show', [\${$this->single_parent_model_var_name}, \$category]);
    }

    public function show({$this->parent_model_name} \${$this->single_parent_model_var_name}, Category \$category)
    {
        return view('{$this->parent_table_name}.categories.show', compact('{$this->single_parent_model_var_name}', 'category'));
    }

    public function edit({$this->parent_model_name} \${$this->single_parent_model_var_name}, Category \$category)
    {
        \$this->authorize('update', \$category);

        return view('{$this->parent_table_name}.categories.edit', compact('{$this->single_parent_model_var_name}', 'category'));
    }

    public function update(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name}, Category \$category)
    {
        \$this->authorize('update', \$category);

        \$categoryData = \$request->validate([
            'title' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$category->update(\$categoryData);

        return redirect()->route('{$this->parent_table_name}.categories.show', [\${$this->single_parent_model_var_name}, \$category]);
    }

    public function destroy(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name}, Category \$category)
    {
        \$this->authorize('delete', \$category);

        \$request->validate(['category_id' => 'required']);

        if (\$request->get('category_id') == \$category->id && \$category->delete()) {
            return redirect()->route('{$this->parent_table_name}.categories.index', \${$this->single_parent_model_var_name});
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/{$this->plural_parent_model_name}/CategoryController.php")));
    }

    /** @test */
    public function it_creates_correct_controller_with_parent_model_and_disobey_parent_option()
    {
        $this->artisan('make:model', ['name' => $this->parent_model_name, '--no-interaction' => true]);
        $this->artisan('make:crud', ['name' => 'Entities/References/Category', '--parent' => 'Projects', '--parent-model' => $this->parent_model_name, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/{$this->plural_parent_model_name}/CategoryController.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers\\{$this->plural_parent_model_name};

use App\Entities\References\Category;
use {$this->full_parent_model_name};
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name})
    {
        \$categoryQuery = \${$this->single_parent_model_var_name}->categories();
        \$categoryQuery->where('title', 'like', '%'.\$request->get('q').'%');
        \$categoryQuery->orderBy('title');
        \$categories = \$categoryQuery->paginate(25);

        return view('{$this->parent_table_name}.categories.index', compact('{$this->single_parent_model_var_name}', 'categories'));
    }

    public function create({$this->parent_model_name} \${$this->single_parent_model_var_name})
    {
        \$this->authorize('create', new Category);

        return view('{$this->parent_table_name}.categories.create', compact('{$this->single_parent_model_var_name}'));
    }

    public function store(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name})
    {
        \$this->authorize('create', new Category);

        \$newCategory = \$request->validate([
            'title' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$newCategory['creator_id'] = auth()->id();

        \$category = \${$this->single_parent_model_var_name}->categories()->create(\$newCategory);

        return redirect()->route('{$this->parent_table_name}.categories.show', [\${$this->single_parent_model_var_name}, \$category]);
    }

    public function show({$this->parent_model_name} \${$this->single_parent_model_var_name}, Category \$category)
    {
        return view('{$this->parent_table_name}.categories.show', compact('{$this->single_parent_model_var_name}', 'category'));
    }

    public function edit({$this->parent_model_name} \${$this->single_parent_model_var_name}, Category \$category)
    {
        \$this->authorize('update', \$category);

        return view('{$this->parent_table_name}.categories.edit', compact('{$this->single_parent_model_var_name}', 'category'));
    }

    public function update(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name}, Category \$category)
    {
        \$this->authorize('update', \$category);

        \$categoryData = \$request->validate([
            'title' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$category->update(\$categoryData);

        return redirect()->route('{$this->parent_table_name}.categories.show', [\${$this->single_parent_model_var_name}, \$category]);
    }

    public function destroy(Request \$request, {$this->parent_model_name} \${$this->single_parent_model_var_name}, Category \$category)
    {
        \$this->authorize('delete', \$category);

        \$request->validate(['category_id' => 'required']);

        if (\$request->get('category_id') == \$category->id && \$category->delete()) {
            return redirect()->route('{$this->parent_table_name}.categories.index', \${$this->single_parent_model_var_name});
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/{$this->plural_parent_model_name}/CategoryController.php")));
    }
}
