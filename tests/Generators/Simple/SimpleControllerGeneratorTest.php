<?php

namespace Tests\Generators\Simple;

use Tests\TestCase;

class SimpleControllerGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_controller_class_content()
    {
        $this->artisan('make:crud-simple', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/{$this->plural_model_name}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

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
        \$editable{$this->model_name} = null;
        \${$this->collection_model_var_name} = {$this->model_name}::where(function (\$query) {
            \$query->where('name', 'like', '%'.request('q').'%');
        })->paginate(25);

        if (in_array(request('action'), ['edit', 'delete']) && request('id') != null) {
            \$editable{$this->model_name} = {$this->model_name}::find(request('id'));
        }

        return view('{$this->table_name}.index', compact('{$this->collection_model_var_name}', 'editable{$this->model_name}'));
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

        {$this->model_name}::create(\$request->only('name', 'description'));

        return redirect()->route('{$this->table_name}.index');
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

        \$routeParam = request()->only('page', 'q');

        \${$this->single_model_var_name} = \${$this->single_model_var_name}->update(\$request->only('name', 'description'));

        return redirect()->route('{$this->table_name}.index', \$routeParam);
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

        \$routeParam = request()->only('page', 'q');

        if (request('{$this->lang_name}_id') == \${$this->single_model_var_name}->id && \${$this->single_model_var_name}->delete()) {
            return redirect()->route('{$this->table_name}.index', \$routeParam);
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/{$this->plural_model_name}Controller.php")));
    }

    /** @test */
    public function it_creates_correct_controller_class_content_for_namespaced_model()
    {
        $this->artisan('make:crud-simple', ['name' => 'Entities/References/Category', '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/CategoriesController.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

use App\Entities\References\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the category.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \$editableCategory = null;
        \$categories = Category::where(function (\$query) {
            \$query->where('name', 'like', '%'.request('q').'%');
        })->paginate(25);

        if (in_array(request('action'), ['edit', 'delete']) && request('id') != null) {
            \$editableCategory = Category::find(request('id'));
        }

        return view('categories.index', compact('categories', 'editableCategory'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Http\Response
     */
    public function store(Request \$request)
    {
        \$this->authorize('create', new Category);

        \$this->validate(\$request, [
            'name' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        Category::create(\$request->only('name', 'description'));

        return redirect()->route('categories.index');
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \App\Entities\References\Category  \$category
     * @return \Illuminate\Http\Response
     */
    public function update(Request \$request, Category \$category)
    {
        \$this->authorize('update', \$category);

        \$this->validate(\$request, [
            'name' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        \$routeParam = request()->only('page', 'q');

        \$category = \$category->update(\$request->only('name', 'description'));

        return redirect()->route('categories.index', \$routeParam);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  \App\Entities\References\Category  \$category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category \$category)
    {
        \$this->authorize('delete', \$category);

        \$this->validate(request(), [
            'category_id' => 'required',
        ]);

        \$routeParam = request()->only('page', 'q');

        if (request('category_id') == \$category->id && \$category->delete()) {
            return redirect()->route('categories.index', \$routeParam);
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/CategoriesController.php")));
    }

    /** @test */
    public function it_creates_correct_controller_with_parent()
    {
        $this->artisan('make:crud-simple', ['name' => 'Entities/References/Category', '--parent' => 'Projects', '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/Projects/CategoriesController.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers\Projects;

use App\Entities\References\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the category.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \$editableCategory = null;
        \$categories = Category::where(function (\$query) {
            \$query->where('name', 'like', '%'.request('q').'%');
        })->paginate(25);

        if (in_array(request('action'), ['edit', 'delete']) && request('id') != null) {
            \$editableCategory = Category::find(request('id'));
        }

        return view('categories.index', compact('categories', 'editableCategory'));
    }

    /**
     * Store a newly created category in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Http\Response
     */
    public function store(Request \$request)
    {
        \$this->authorize('create', new Category);

        \$this->validate(\$request, [
            'name' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        Category::create(\$request->only('name', 'description'));

        return redirect()->route('categories.index');
    }

    /**
     * Update the specified category in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \App\Entities\References\Category  \$category
     * @return \Illuminate\Http\Response
     */
    public function update(Request \$request, Category \$category)
    {
        \$this->authorize('update', \$category);

        \$this->validate(\$request, [
            'name' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        \$routeParam = request()->only('page', 'q');

        \$category = \$category->update(\$request->only('name', 'description'));

        return redirect()->route('categories.index', \$routeParam);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param  \App\Entities\References\Category  \$category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category \$category)
    {
        \$this->authorize('delete', \$category);

        \$this->validate(request(), [
            'category_id' => 'required',
        ]);

        \$routeParam = request()->only('page', 'q');

        if (request('category_id') == \$category->id && \$category->delete()) {
            return redirect()->route('categories.index', \$routeParam);
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/Projects/CategoriesController.php")));
    }
}
