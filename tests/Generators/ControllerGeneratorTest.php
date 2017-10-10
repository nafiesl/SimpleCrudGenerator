<?php

namespace Tests\Generators;

use Tests\TestCase;

class ControllerGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_controller_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $this->assertFileExists(app_path("Http/Controllers/{$this->pluralModelName}Controller.php"));
        $ctrlClassContent = "<?php

namespace App\Http\Controllers;

use App\\{$this->modelName};
use Illuminate\Http\Request;

class {$this->pluralModelName}Controller extends Controller
{
    /**
     * Display a listing of the {$this->singleModelName}.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \$editable{$this->modelName} = null;
        \${$this->tableName} = {$this->modelName}::where(function (\$query) {
            \$query->where('name', 'like', '%'.request('q').'%');
        })->paginate(25);

        if (in_array(request('action'), ['edit', 'delete']) && request('id') != null) {
            \$editable{$this->modelName} = {$this->modelName}::find(request('id'));
        }

        return view('{$this->tableName}.index', compact('{$this->tableName}', 'editable{$this->modelName}'));
    }

    /**
     * Store a newly created {$this->singleModelName} in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Http\Response
     */
    public function store(Request \$request)
    {
        \$this->validate(\$request, [
            'name' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        {$this->modelName}::create(\$request->only('name', 'description'));

        return redirect()->route('{$this->tableName}.index');
    }

    /**
     * Update the specified {$this->singleModelName} in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  \App\\{$this->modelName}  \${$this->singleModelName}
     * @return \Illuminate\Http\Response
     */
    public function update(Request \$request, {$this->modelName} \${$this->singleModelName})
    {
        \$this->validate(\$request, [
            'name' => 'required|max:60',
            'description' => 'nullable|max:255',
        ]);

        \$routeParam = request()->only('page', 'q');

        \${$this->singleModelName} = \${$this->singleModelName}->update(\$request->only('name', 'description'));

        return redirect()->route('{$this->tableName}.index', \$routeParam);
    }

    /**
     * Remove the specified {$this->singleModelName} from storage.
     *
     * @param  \App\\{$this->modelName}  \${$this->singleModelName}
     * @return \Illuminate\Http\Response
     */
    public function destroy({$this->modelName} \${$this->singleModelName})
    {
        \$this->validate(request(), [
            '{$this->singleModelName}_id' => 'required',
        ]);

        \$routeParam = request()->only('page', 'q');

        if (request('{$this->singleModelName}_id') == \${$this->singleModelName}->id && \${$this->singleModelName}->delete()) {
            return redirect()->route('{$this->tableName}.index', \$routeParam);
        }

        return back();
    }
}
";
        $this->assertEquals($ctrlClassContent, file_get_contents(app_path("Http/Controllers/{$this->pluralModelName}Controller.php")));
    }
}
