<?php

namespace Tests\Generators\Simple;

use Tests\TestCase;

class SimpleControllerGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_controller_class_content()
    {
        $this->artisan('make:crud-simple', ['name' => $this->model_name, '--no-interaction' => true]);

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
        \$editable{$this->model_name} = null;
        \${$this->single_model_var_name}Query = {$this->model_name}::query();
        \${$this->single_model_var_name}Query->where('name', 'like', '%'.request('q').'%');
        \${$this->collection_model_var_name} = \${$this->single_model_var_name}Query->paginate(25);

        if (in_array(request('action'), ['edit', 'delete']) && request('id') != null) {
            \$editable{$this->model_name} = {$this->model_name}::find(request('id'));
        }

        return view('{$this->table_name}.index', compact('{$this->collection_model_var_name}', 'editable{$this->model_name}'));
    }

    /**
     * Store a newly created {$this->single_model_var_name} in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Routing\Redirector
     */
    public function store(Request \$request)
    {
        \$this->authorize('create', new {$this->model_name});

        \$new{$this->model_name} = \$request->validate([
            'name'        => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$new{$this->model_name}['creator_id'] = auth()->id();

        {$this->model_name}::create(\$new{$this->model_name});

        return redirect()->route('{$this->table_name}.index');
    }

    /**
     * Update the specified {$this->single_model_var_name} in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \\{$this->full_model_name}  \${$this->single_model_var_name}
     * @return \Illuminate\Routing\Redirector
     */
    public function update(Request \$request, {$this->model_name} \${$this->single_model_var_name})
    {
        \$this->authorize('update', \${$this->single_model_var_name});

        \${$this->single_model_var_name}Data = \$request->validate([
            'name'        => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \${$this->single_model_var_name}->update(\${$this->single_model_var_name}Data);

        \$routeParam = request()->only('page', 'q');

        return redirect()->route('{$this->table_name}.index', \$routeParam);
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
            \$routeParam = request()->only('page', 'q');

            return redirect()->route('{$this->table_name}.index', \$routeParam);
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
        $this->artisan('make:crud-simple', ['name' => 'Entities/References/Category', '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/CategoryController.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

use App\Entities\References\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the category.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        \$editableCategory = null;
        \$categoryQuery = Category::query();
        \$categoryQuery->where('name', 'like', '%'.request('q').'%');
        \$categories = \$categoryQuery->paginate(25);

        if (in_array(request('action'), ['edit', 'delete']) && request('id') != null) {
            \$editableCategory = Category::find(request('id'));
        }

        return view('categories.index', compact('categories', 'editableCategory'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Routing\Redirector
     */
    public function store(Request \$request)
    {
        \$this->authorize('create', new Category);

        \$newCategory = \$request->validate([
            'name'        => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$newCategory['creator_id'] = auth()->id();

        Category::create(\$newCategory);

        return redirect()->route('categories.index');
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \App\Entities\References\Category  \$category
     * @return \Illuminate\Routing\Redirector
     */
    public function update(Request \$request, Category \$category)
    {
        \$this->authorize('update', \$category);

        \$categoryData = \$request->validate([
            'name'        => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$category->update(\$categoryData);

        \$routeParam = request()->only('page', 'q');

        return redirect()->route('categories.index', \$routeParam);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \App\Entities\References\Category  \$category
     * @return \Illuminate\Routing\Redirector
     */
    public function destroy(Request \$request, Category \$category)
    {
        \$this->authorize('delete', \$category);

        \$request->validate(['category_id' => 'required']);

        if (\$request->get('category_id') == \$category->id && \$category->delete()) {
            \$routeParam = request()->only('page', 'q');

            return redirect()->route('categories.index', \$routeParam);
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
        $this->artisan('make:crud-simple', ['name' => 'Entities/References/Category', '--parent' => 'Projects', '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/Projects/CategoryController.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers\Projects;

use App\Entities\References\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the category.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        \$editableCategory = null;
        \$categoryQuery = Category::query();
        \$categoryQuery->where('name', 'like', '%'.request('q').'%');
        \$categories = \$categoryQuery->paginate(25);

        if (in_array(request('action'), ['edit', 'delete']) && request('id') != null) {
            \$editableCategory = Category::find(request('id'));
        }

        return view('categories.index', compact('categories', 'editableCategory'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Routing\Redirector
     */
    public function store(Request \$request)
    {
        \$this->authorize('create', new Category);

        \$newCategory = \$request->validate([
            'name'        => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$newCategory['creator_id'] = auth()->id();

        Category::create(\$newCategory);

        return redirect()->route('categories.index');
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \App\Entities\References\Category  \$category
     * @return \Illuminate\Routing\Redirector
     */
    public function update(Request \$request, Category \$category)
    {
        \$this->authorize('update', \$category);

        \$categoryData = \$request->validate([
            'name'        => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$category->update(\$categoryData);

        \$routeParam = request()->only('page', 'q');

        return redirect()->route('categories.index', \$routeParam);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \App\Entities\References\Category  \$category
     * @return \Illuminate\Routing\Redirector
     */
    public function destroy(Request \$request, Category \$category)
    {
        \$this->authorize('delete', \$category);

        \$request->validate(['category_id' => 'required']);

        if (\$request->get('category_id') == \$category->id && \$category->delete()) {
            \$routeParam = request()->only('page', 'q');

            return redirect()->route('categories.index', \$routeParam);
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/Projects/CategoryController.php")));
    }
}
