<?php

namespace Tests\CommandOptions;

use Tests\TestCase;

class UuidOptionsTest extends TestCase
{
    /** @test */
    public function it_creates_correct_migration_class_content_for_uuid_primary_key()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--uuid' => true, '--no-interaction' => true]);

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);
        $modelClassContent = "<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create{$this->plural_model_name}Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{$this->table_name}', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
            \$table->string('title', 60);
            \$table->string('description')->nullable();
            \$table->foreignId('creator_id')->constrained('users')->onDelete('restrict');
            \$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{$this->table_name}');
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($migrationFilePath));
    }
    /** @test */
    public function it_creates_correct_model_factory_content_for_uuid_primary_key()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--uuid' => true, '--no-interaction' => true]);

        $modelFactoryPath = database_path('factories/'.$this->model_name.'Factory.php');
        $this->assertFileExists($modelFactoryPath);
        $modelFactoryContent = "<?php

namespace Database\Factories;

use App\Models\User;
use {$this->full_model_name};
use Illuminate\Database\Eloquent\Factories\Factory;

class {$this->model_name}Factory extends Factory
{
    protected \$model = {$this->model_name}::class;

    public function definition()
    {
        return [
            'id'       => \$this->faker->uuid,
            'title'       => \$this->faker->word,
            'description' => \$this->faker->sentence,
            'creator_id'  => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
";
        $this->assertEquals($modelFactoryContent, file_get_contents($modelFactoryPath));
    }
    /** @test */
    public function it_creates_correct_model_class_content_for_uuid_primary_key()
    {
        config(['auth.providers.users.model' => 'App\Models\User']);
        $this->artisan('make:crud', ['name' => $this->model_name, '--uuid' => true, '--no-interaction' => true]);

        $modelPath = app_path('Models/'.$this->model_name.'.php');
        $this->assertFileExists($modelPath);
        $modelClassContent = "<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$this->model_name} extends Model
{
    use HasFactory;

    public \$incrementing = false;

    protected \$keyType = 'string';

    protected \$fillable = ['id', 'title', 'description', 'creator_id'];

    public function getTitleLinkAttribute()
    {
        \$title = __('app.show_detail_title', [
            'title' => \$this->title, 'type' => __('{$this->lang_name}.{$this->lang_name}'),
        ]);
        \$link = '<a href=\"'.route('{$this->table_name}.show', \$this).'\"';
        \$link .= ' title=\"'.\$title.'\">';
        \$link .= \$this->title;
        \$link .= '</a>';

        return \$link;
    }

    public function creator()
    {
        return \$this->belongsTo(User::class);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($modelPath));
    }
    /** @test */
    public function it_creates_correct_controller_class_content_for_uuid_primary_key()
    {
        $this->artisan('make:crud-simple', ['name' => $this->model_name, '--uuid' => true, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

use {$this->full_model_name};
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class {$this->model_name}Controller extends Controller
{
    /**
     * Display a listing of the {$this->single_model_var_name}.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\View\View
     */
    public function index(Request \$request)
    {
        \$editable{$this->model_name} = null;
        \${$this->single_model_var_name}Query = {$this->model_name}::query();
        \${$this->single_model_var_name}Query->where('title', 'like', '%'.\$request->get('q').'%');
        \${$this->single_model_var_name}Query->orderBy('title');
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
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$new{$this->model_name}['id'] = Uuid::uuid4()->toString();
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
            'title'       => 'required|max:60',
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
    public function it_creates_correct_full_controller_class_content_for_uuid_primary_key()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--uuid' => true, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

use {$this->full_model_name};
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class {$this->model_name}Controller extends Controller
{
    /**
     * Display a listing of the {$this->single_model_var_name}.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\View\View
     */
    public function index(Request \$request)
    {
        \${$this->single_model_var_name}Query = {$this->model_name}::query();
        \${$this->single_model_var_name}Query->where('title', 'like', '%'.\$request->get('q').'%');
        \${$this->single_model_var_name}Query->orderBy('title');
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
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Routing\Redirector
     */
    public function store(Request \$request)
    {
        \$this->authorize('create', new {$this->model_name});

        \$new{$this->model_name} = \$request->validate([
            'title'       => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);
        \$new{$this->model_name}['id'] = Uuid::uuid4()->toString();
        \$new{$this->model_name}['creator_id'] = auth()->id();

        \${$this->single_model_var_name} = {$this->model_name}::create(\$new{$this->model_name});

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
     * @param  \Illuminate\Http\Request  \$request
     * @param  \\{$this->full_model_name}  \${$this->single_model_var_name}
     * @return \Illuminate\Routing\Redirector
     */
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
}
