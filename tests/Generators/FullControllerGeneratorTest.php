<?php

namespace Tests\Generators;

use Tests\TestCase;

class FullControllerGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_controller_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

use {$this->full_model_name};
use Illuminate\Http\Request;

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

    public function store(Request \$request)
    {
        \$this->authorize('create', new {$this->model_name});

        \$new{$this->model_name} = \$request->validate([
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$new{$this->model_name}['creator_id'] = auth()->id();

        \${$this->single_model_var_name} = {$this->model_name}::create(\$new{$this->model_name});

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

    public function update(Request \$request, {$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('update', \${$this->single_model_var_name});

        \${$this->single_model_var_name}Data = \$request->validate([
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \${$this->single_model_var_name}->update(\${$this->single_model_var_name}Data);

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
    public function it_creates_correct_controller_class_content_for_namespaced_model()
    {
        $this->artisan('make:crud', ['name' => 'Entities/References/Category', '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/CategoryController.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

use App\Entities\References\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request \$request)
    {
        \$categoryQuery = Category::query();
        \$categoryQuery->where('title', 'like', '%'.\$request->get('q').'%');
        \$categoryQuery->orderBy('title');
        \$categories = \$categoryQuery->paginate(25);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        \$this->authorize('create', new Category);

        return view('categories.create');
    }

    public function store(Request \$request)
    {
        \$this->authorize('create', new Category);

        \$newCategory = \$request->validate([
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$newCategory['creator_id'] = auth()->id();

        \$category = Category::create(\$newCategory);

        return redirect()->route('categories.show', \$category);
    }

    public function show(Category \$category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category \$category)
    {
        \$this->authorize('update', \$category);

        return view('categories.edit', compact('category'));
    }

    public function update(Request \$request, Category \$category)
    {
        \$this->authorize('update', \$category);

        \$categoryData = \$request->validate([
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$category->update(\$categoryData);

        return redirect()->route('categories.show', \$category);
    }

    public function destroy(Request \$request, Category \$category)
    {
        \$this->authorize('delete', \$category);

        \$request->validate(['category_id' => 'required']);

        if (\$request->get('category_id') == \$category->id && \$category->delete()) {
            return redirect()->route('categories.index');
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/CategoryController.php")));
    }

    /** @test */
    public function it_creates_correct_controller_with_parent()
    {
        $this->artisan('make:crud', ['name' => 'Entities/References/Category', '--parent' => 'Projects', '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/Projects/CategoryController.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers\Projects;

use App\Entities\References\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request \$request)
    {
        \$categoryQuery = Category::query();
        \$categoryQuery->where('title', 'like', '%'.\$request->get('q').'%');
        \$categoryQuery->orderBy('title');
        \$categories = \$categoryQuery->paginate(25);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        \$this->authorize('create', new Category);

        return view('categories.create');
    }

    public function store(Request \$request)
    {
        \$this->authorize('create', new Category);

        \$newCategory = \$request->validate([
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$newCategory['creator_id'] = auth()->id();

        \$category = Category::create(\$newCategory);

        return redirect()->route('categories.show', \$category);
    }

    public function show(Category \$category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category \$category)
    {
        \$this->authorize('update', \$category);

        return view('categories.edit', compact('category'));
    }

    public function update(Request \$request, Category \$category)
    {
        \$this->authorize('update', \$category);

        \$categoryData = \$request->validate([
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$category->update(\$categoryData);

        return redirect()->route('categories.show', \$category);
    }

    public function destroy(Request \$request, Category \$category)
    {
        \$this->authorize('delete', \$category);

        \$request->validate(['category_id' => 'required']);

        if (\$request->get('category_id') == \$category->id && \$category->delete()) {
            return redirect()->route('categories.index');
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/Projects/CategoryController.php")));
    }
}
