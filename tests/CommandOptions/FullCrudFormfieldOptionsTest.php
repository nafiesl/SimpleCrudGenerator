<?php

namespace Tests\CommandOptions;

use Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;

class FullCrudFormfieldOptionsTest extends TestCase
{
    /** @test */
    public function it_can_generate_views_with_formfield_for_full_crud()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--formfield' => true]);

        $this->assertNotContains("{$this->model_name} model already exists.", app(Kernel::class)->output());

        $this->assertFileExists(app_path($this->model_name.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$this->table_name}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/create.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/edit.blade.php"));
        $this->assertFileNotExists(resource_path("views/{$this->table_name}/forms.blade.php"));

        $localeConfig = config('app.locale');
        $this->assertFileExists(resource_path("lang/{$localeConfig}/{$this->lang_name}.php"));

        $this->assertFileExists(base_path("routes/web.php"));
        $this->assertFileExists(app_path("Policies/{$this->model_name}Policy.php"));
        $this->assertFileExists(database_path("factories/{$this->model_name}Factory.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$this->model_name}Test.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$this->model_name}Test.php"));
    }

    /** @test */
    public function it_creates_correct_index_view_content_with_formfield()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--formfield' => true]);

        $indexViewPath = resource_path("views/{$this->table_name}/index.blade.php");
        $this->assertFileExists($indexViewPath);
        $indexViewContent = "@extends('layouts.app')

@section('title', __('{$this->lang_name}.list'))

@section('content')
<div class=\"mb-3\">
    <div class=\"float-right\">
        @can('create', new {$this->full_model_name})
            {{ link_to_route('{$this->table_name}.create', __('{$this->lang_name}.create'), [], ['class' => 'btn btn-success']) }}
        @endcan
    </div>
    <h1 class=\"page-title\">{{ __('{$this->lang_name}.list') }} <small>{{ __('app.total') }} : {{ \${$this->collection_model_var_name}->total() }} {{ __('{$this->lang_name}.{$this->lang_name}') }}</small></h1>
</div>

<div class=\"row\">
    <div class=\"col-md-12\">
        <div class=\"card\">
            <div class=\"card-header\">
                {{ Form::open(['method' => 'get', 'class' => 'form-inline']) }}
                {!! FormField::text('q', ['label' => __('{$this->lang_name}.search'), 'placeholder' => __('{$this->lang_name}.search_text'), 'class' => 'mx-sm-2']) !!}
                {{ Form::submit(__('{$this->lang_name}.search'), ['class' => 'btn btn-secondary']) }}
                {{ link_to_route('{$this->table_name}.index', __('app.reset'), [], ['class' => 'btn btn-link']) }}
                {{ Form::close() }}
            </div>
            <table class=\"table table-sm table-responsive-sm table-hover\">
                <thead>
                    <tr>
                        <th class=\"text-center\">{{ __('app.table_no') }}</th>
                        <th>{{ __('{$this->lang_name}.name') }}</th>
                        <th>{{ __('{$this->lang_name}.description') }}</th>
                        <th class=\"text-center\">{{ __('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\${$this->collection_model_var_name} as \$key => \${$this->single_model_var_name})
                    <tr>
                        <td class=\"text-center\">{{ \${$this->collection_model_var_name}->firstItem() + \$key }}</td>
                        <td>{{ \${$this->single_model_var_name}->name_link }}</td>
                        <td>{{ \${$this->single_model_var_name}->description }}</td>
                        <td class=\"text-center\">
                            @can('view', \${$this->single_model_var_name})
                                {{ link_to_route(
                                    '{$this->table_name}.show',
                                    __('app.show'),
                                    [\${$this->single_model_var_name}],
                                    ['id' => 'show-{$this->lang_name}-' . \${$this->single_model_var_name}->id]
                                ) }}
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class=\"card-body\">{{ \${$this->collection_model_var_name}->appends(Request::except('page'))->render() }}</div>
        </div>
    </div>
</div>
@endsection
";
        $this->assertEquals($indexViewContent, file_get_contents($indexViewPath));
    }

    /** @test */
    public function it_creates_correct_show_view_content_with_formfield()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--formfield' => true]);

        $showFormViewPath = resource_path("views/{$this->table_name}/show.blade.php");
        $this->assertFileExists($showFormViewPath);

        $showFormViewContent = "@extends('layouts.app')

@section('title', __('{$this->lang_name}.detail'))

@section('content')
<div class=\"row justify-content-center\">
    <div class=\"col-md-6\">
        <div class=\"card\">
            <div class=\"card-header\">{{ __('{$this->lang_name}.detail') }}</div>
            <div class=\"card-body\">
                <table class=\"table table-sm\">
                    <tbody>
                        <tr><td>{{ __('{$this->lang_name}.name') }}</td><td>{{ \${$this->single_model_var_name}->name }}</td></tr>
                        <tr><td>{{ __('{$this->lang_name}.description') }}</td><td>{{ \${$this->single_model_var_name}->description }}</td></tr>
                    </tbody>
                </table>
            </div>
            <div class=\"card-footer\">
                @can('update', \${$this->single_model_var_name})
                    {{ link_to_route('{$this->table_name}.edit', __('{$this->lang_name}.edit'), [\${$this->single_model_var_name}], ['class' => 'btn btn-warning', 'id' => 'edit-{$this->lang_name}-'.\${$this->single_model_var_name}->id]) }}
                @endcan
                {{ link_to_route('{$this->table_name}.index', __('{$this->lang_name}.back_to_index'), [], ['class' => 'btn btn-link']) }}
            </div>
        </div>
    </div>
</div>
@endsection
";
        $this->assertEquals($showFormViewContent, file_get_contents($showFormViewPath));
    }

    /** @test */
    public function it_creates_correct_create_view_content_with_formfield()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--formfield' => true]);

        $createFormViewPath = resource_path("views/{$this->table_name}/create.blade.php");
        $this->assertFileExists($createFormViewPath);
        $createFormViewContent = "@extends('layouts.app')

@section('title', __('{$this->lang_name}.create'))

@section('content')
<div class=\"row justify-content-center\">
    <div class=\"col-md-6\">
        <div class=\"card\">
            <div class=\"card-header\">{{ __('{$this->lang_name}.create') }}</div>
            {{ Form::open(['route' => '{$this->table_name}.store']) }}
            <div class=\"card-body\">
                {!! FormField::text('name', ['required' => true, 'label' => __('{$this->lang_name}.name')]) !!}
                {!! FormField::textarea('description', ['label' => __('{$this->lang_name}.description')]) !!}
            </div>
            <div class=\"card-footer\">
                {{ Form::submit(__('{$this->lang_name}.create'), ['class' => 'btn btn-success']) }}
                {{ link_to_route('{$this->table_name}.index', __('app.cancel'), [], ['class' => 'btn btn-link']) }}
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
";
        $this->assertEquals($createFormViewContent, file_get_contents($createFormViewPath));
    }

    /** @test */
    public function it_creates_correct_edit_view_content_with_formfield()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--formfield' => true]);

        $editFormViewPath = resource_path("views/{$this->table_name}/edit.blade.php");
        $this->assertFileExists($editFormViewPath);
        $editFormViewContent = "@extends('layouts.app')

@section('title', __('{$this->lang_name}.edit'))

@section('content')
<div class=\"row justify-content-center\">
    <div class=\"col-md-6\">
        @if (request('action') == 'delete' && \${$this->single_model_var_name})
        @can('delete', \${$this->single_model_var_name})
            <div class=\"card\">
                <div class=\"card-header\">{{ __('{$this->lang_name}.delete') }}</div>
                <div class=\"card-body\">
                    <label class=\"control-label text-primary\">{{ __('{$this->lang_name}.name') }}</label>
                    <p>{{ \${$this->single_model_var_name}->name }}</p>
                    <label class=\"control-label text-primary\">{{ __('{$this->lang_name}.description') }}</label>
                    <p>{{ \${$this->single_model_var_name}->description }}</p>
                    {!! \$errors->first('{$this->lang_name}_id', '<span class=\"form-error small\">:message</span>') !!}
                </div>
                <hr style=\"margin:0\">
                <div class=\"card-body text-danger\">{{ __('{$this->lang_name}.delete_confirm') }}</div>
                <div class=\"card-footer\">
                    {!! FormField::delete(
                        ['route' => ['{$this->table_name}.destroy', \${$this->single_model_var_name}]],
                        __('app.delete_confirm_button'),
                        ['class' => 'btn btn-danger'],
                        ['{$this->lang_name}_id' => \${$this->single_model_var_name}->id]
                    ) !!}
                    {{ link_to_route('{$this->table_name}.edit', __('app.cancel'), [\${$this->single_model_var_name}], ['class' => 'btn btn-link']) }}
                </div>
            </div>
        @endcan
        @else
        <div class=\"card\">
            <div class=\"card-header\">{{ __('{$this->lang_name}.edit') }}</div>
            {{ Form::model(\${$this->single_model_var_name}, ['route' => ['{$this->table_name}.update', \${$this->single_model_var_name}], 'method' => 'patch']) }}
            <div class=\"card-body\">
                {!! FormField::text('name', ['required' => true, 'label' => __('{$this->lang_name}.name')]) !!}
                {!! FormField::textarea('description', ['label' => __('{$this->lang_name}.description')]) !!}
            </div>
            <div class=\"card-footer\">
                {{ Form::submit(__('{$this->lang_name}.update'), ['class' => 'btn btn-success']) }}
                {{ link_to_route('{$this->table_name}.show', __('app.cancel'), [\${$this->single_model_var_name}], ['class' => 'btn btn-link']) }}
                @can('delete', \${$this->single_model_var_name})
                    {{ link_to_route('{$this->table_name}.edit', __('app.delete'), [\${$this->single_model_var_name}, 'action' => 'delete'], ['class' => 'btn btn-danger float-right', 'id' => 'del-{$this->lang_name}-'.\${$this->single_model_var_name}->id]) }}
                @endcan
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endif
@endsection
";
        $this->assertEquals($editFormViewContent, file_get_contents($editFormViewPath));
    }

    /** @test */
    public function it_creates_correct_model_class_with_link_to_route_helper()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--formfield' => true]);

        $modelPath = app_path($this->model_name.'.php');
        $this->assertFileExists($modelPath);
        $modelClassContent = "<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class {$this->model_name} extends Model
{
    protected \$fillable = ['name', 'description', 'creator_id'];

    public function getNameLinkAttribute()
    {
        return link_to_route('{$this->table_name}.show', \$this->name, [\$this], [
            'title' => __(
                'app.show_detail_title',
                ['name' => \$this->name, 'type' => __('{$this->lang_name}.{$this->lang_name}')]
            ),
        ]);
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
    public function it_creates_correct_unit_test_class_with_link_to_route_helper()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--formfield' => true]);

        $uniTestPath = base_path("tests/Unit/Models/{$this->model_name}Test.php");
        $this->assertFileExists($uniTestPath);
        $modelClassContent = "<?php

namespace Tests\Unit\Models;

use App\User;
use {$this->full_model_name};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\BrowserKitTest as TestCase;

class {$this->model_name}Test extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_{$this->lang_name}_has_name_link_attribute()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->create();

        \$this->assertEquals(
            link_to_route('{$this->table_name}.show', \${$this->single_model_var_name}->name, [\${$this->single_model_var_name}], [
                'title' => __(
                    'app.show_detail_title',
                    ['name' => \${$this->single_model_var_name}->name, 'type' => __('{$this->lang_name}.{$this->lang_name}')]
                ),
            ]), \${$this->single_model_var_name}->name_link
        );
    }

    /** @test */
    public function a_{$this->lang_name}_has_belongs_to_creator_relation()
    {
        \${$this->single_model_var_name} = factory({$this->model_name}::class)->make();

        \$this->assertInstanceOf(User::class, \${$this->single_model_var_name}->creator);
        \$this->assertEquals(\${$this->single_model_var_name}->creator_id, \${$this->single_model_var_name}->creator->id);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($uniTestPath));
    }
}
