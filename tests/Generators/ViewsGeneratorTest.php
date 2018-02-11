<?php

namespace Tests\Generators;

use Illuminate\Contracts\Console\Kernel;
use Tests\TestCase;

class ViewsGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_index_view_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $indexViewPath = resource_path("views/{$this->table_name}/index.blade.php");
        $this->assertFileExists($indexViewPath);
        $indexViewContent = "@extends('layouts.app')

@section('title', trans('{$this->lang_name}.list'))

@section('content')
<h1 class=\"page-header\">
    <div class=\"pull-right\">
    @can('create', new {$this->full_model_name})
        {{ link_to_route('{$this->table_name}.create', trans('{$this->lang_name}.create'), [], ['class' => 'btn btn-success']) }}
    @endcan
    </div>
    {{ trans('{$this->lang_name}.list') }}
    <small>{{ trans('app.total') }} : {{ \${$this->collection_model_var_name}->total() }} {{ trans('{$this->lang_name}.{$this->lang_name}') }}</small>
</h1>
<div class=\"row\">
    <div class=\"col-md-12\">
        <div class=\"panel panel-default table-responsive\">
            <div class=\"panel-heading\">
                {{ Form::open(['method' => 'get','class' => 'form-inline']) }}
                {!! FormField::text('q', ['value' => request('q'), 'label' => trans('{$this->lang_name}.search'), 'class' => 'input-sm']) !!}
                {{ Form::submit(trans('{$this->lang_name}.search'), ['class' => 'btn btn-sm']) }}
                {{ link_to_route('{$this->table_name}.index', trans('app.reset')) }}
                {{ Form::close() }}
            </div>
            <table class=\"table table-condensed\">
                <thead>
                    <tr>
                        <th class=\"text-center\">{{ trans('app.table_no') }}</th>
                        <th>{{ trans('{$this->lang_name}.name') }}</th>
                        <th>{{ trans('{$this->lang_name}.description') }}</th>
                        <th class=\"text-center\">{{ trans('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\${$this->collection_model_var_name} as \$key => \${$this->single_model_var_name})
                    <tr>
                        <td class=\"text-center\">{{ \${$this->collection_model_var_name}->firstItem() + \$key }}</td>
                        <td>{{ \${$this->single_model_var_name}->nameLink() }}</td>
                        <td>{{ \${$this->single_model_var_name}->description }}</td>
                        <td class=\"text-center\">
                        @can('view', \${$this->single_model_var_name})
                            {!! link_to_route(
                                '{$this->table_name}.show',
                                trans('app.show'),
                                [\${$this->single_model_var_name}],
                                ['class' => 'btn btn-default btn-xs', 'id' => 'show-{$this->lang_name}-' . \${$this->single_model_var_name}->id]
                            ) !!}
                        @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class=\"panel-body\">{{ \${$this->collection_model_var_name}->appends(Request::except('page'))->render() }}</div>
        </div>
    </div>
</div>
@endsection
";
        $this->assertEquals($indexViewContent, file_get_contents($indexViewPath));
    }

    /** @test */
    public function it_creates_correct_show_view_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $showFormViewPath = resource_path("views/{$this->table_name}/show.blade.php");
        $this->assertFileExists($showFormViewPath);
        $showFormViewContent = "@extends('layouts.app')

@section('title', trans('{$this->lang_name}.detail'))

@section('content')
<div class=\"row\">
    <div class=\"col-md-6 col-md-offset-3\">
        <div class=\"panel panel-default\">
            <div class=\"panel-heading\"><h3 class=\"panel-title\">{{ trans('{$this->lang_name}.detail') }}</h3></div>
            <table class=\"table table-condensed\">
                <tbody>
                    <tr>
                        <td>{{ trans('{$this->lang_name}.name') }}</td>
                        <td>{{ \${$this->single_model_var_name}->name }}</td>
                    </tr>
                    <tr>
                        <td>{{ trans('{$this->lang_name}.description') }}</td>
                        <td>{{ \${$this->single_model_var_name}->description }}</td>
                    </tr>
                </tbody>
            </table>
            <div class=\"panel-footer\">
                {{ link_to_route('{$this->table_name}.edit', trans('{$this->lang_name}.edit'), [\${$this->single_model_var_name}], ['class' => 'btn btn-warning', 'id' => 'edit-{$this->lang_name}-'.\${$this->single_model_var_name}->id]) }}
                {{ link_to_route('{$this->table_name}.index', trans('{$this->lang_name}.back_to_index'), [], ['class' => 'btn btn-default']) }}
            </div>
        </div>
    </div>
</div>
@endsection
";
        $this->assertEquals($showFormViewContent, file_get_contents($showFormViewPath));
    }

    /** @test */
    public function it_creates_correct_create_view_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $createFormViewPath = resource_path("views/{$this->table_name}/create.blade.php");
        $this->assertFileExists($createFormViewPath);
        $createFormViewContent = "@extends('layouts.app')

@section('title', trans('{$this->lang_name}.create'))

@section('content')
<div class=\"row\">
    <div class=\"col-md-6 col-md-offset-3\">
        <div class=\"panel panel-default\">
            <div class=\"panel-heading\"><h3 class=\"panel-title\">{{ trans('{$this->lang_name}.create') }}</h3></div>
            {!! Form::open(['route' => '{$this->table_name}.store']) !!}
            <div class=\"panel-body\">
                {!! FormField::text('name', ['required' => true, 'label' => trans('{$this->lang_name}.name')]) !!}
                {!! FormField::textarea('description', ['label' => trans('{$this->lang_name}.description')]) !!}
            </div>
            <div class=\"panel-footer\">
                {!! Form::submit(trans('{$this->lang_name}.create'), ['class' => 'btn btn-success']) !!}
                {{ link_to_route('{$this->table_name}.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
";
        $this->assertEquals($createFormViewContent, file_get_contents($createFormViewPath));
    }

    /** @test */
    public function it_creates_correct_edit_view_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $editFormViewPath = resource_path("views/{$this->table_name}/edit.blade.php");
        $this->assertFileExists($editFormViewPath);
        $editFormViewContent = "@extends('layouts.app')

@section('title', trans('{$this->lang_name}.edit'))

@section('content')

<div class=\"row\">
    <div class=\"col-md-6 col-md-offset-3\">
        @if (request('action') == 'delete' && \${$this->single_model_var_name})
        @can('delete', \${$this->single_model_var_name})
            <div class=\"panel panel-default\">
                <div class=\"panel-heading\"><h3 class=\"panel-title\">{{ trans('{$this->lang_name}.delete') }}</h3></div>
                <div class=\"panel-body\">
                    <label class=\"control-label\">{{ trans('{$this->lang_name}.name') }}</label>
                    <p>{{ \${$this->single_model_var_name}->name }}</p>
                    <label class=\"control-label\">{{ trans('{$this->lang_name}.description') }}</label>
                    <p>{{ \${$this->single_model_var_name}->description }}</p>
                    {!! \$errors->first('{$this->lang_name}_id', '<span class=\"form-error small\">:message</span>') !!}
                </div>
                <hr style=\"margin:0\">
                <div class=\"panel-body\">{{ trans('app.delete_confirm') }}</div>
                <div class=\"panel-footer\">
                    {!! FormField::delete(
                        ['route'=>['{$this->table_name}.destroy', \${$this->single_model_var_name}]],
                        trans('app.delete_confirm_button'),
                        ['class'=>'btn btn-danger'],
                        [
                            '{$this->lang_name}_id' => \${$this->single_model_var_name}->id,
                            'page' => request('page'),
                            'q' => request('q'),
                        ]
                    ) !!}
                    {{ link_to_route('{$this->table_name}.edit', trans('app.cancel'), [\${$this->single_model_var_name}], ['class' => 'btn btn-default']) }}
                </div>
            </div>
        @endcan
        @else
        <div class=\"panel panel-default\">
            <div class=\"panel-heading\"><h3 class=\"panel-title\">{{ trans('{$this->lang_name}.edit') }}</h3></div>
            {!! Form::model(\${$this->single_model_var_name}, ['route' => ['{$this->table_name}.update', \${$this->single_model_var_name}->id],'method' => 'patch']) !!}
            <div class=\"panel-body\">
                {!! FormField::text('name', ['required' => true, 'label' => trans('{$this->lang_name}.name')]) !!}
                {!! FormField::textarea('description', ['label' => trans('{$this->lang_name}.description')]) !!}
            </div>
            <div class=\"panel-footer\">
                {!! Form::submit(trans('{$this->lang_name}.update'), ['class' => 'btn btn-success']) !!}
                {{ link_to_route('{$this->table_name}.show', trans('app.cancel'), [\${$this->single_model_var_name}], ['class' => 'btn btn-default']) }}
                {{ link_to_route('{$this->table_name}.edit', trans('app.delete'), [\${$this->single_model_var_name}, 'action' => 'delete'], ['class' => 'btn btn-danger pull-right', 'id' => 'del-{$this->lang_name}-'.\${$this->single_model_var_name}->id]) }}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endif
@endsection
";
        $this->assertEquals($editFormViewContent, file_get_contents($editFormViewPath));
    }

    /** @test */
    public function it_not_gives_warning_message_if_default_layout_view_does_exists()
    {
        $defaultLayoutView = config('simple-crud.default_layout_view');
        $this->generateDefaultLayoutView($defaultLayoutView);

        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertNotRegExp("/{$defaultLayoutView} view does not exists./", app(Kernel::class)->output());
    }

    /** @test */
    public function it_gives_warning_message_if_default_layout_view_does_not_exists()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);
        $defaultLayoutView = config('simple-crud.default_layout_view');

        $this->assertRegExp("/{$defaultLayoutView} view does not exists./", app(Kernel::class)->output());
    }

    public function generateDefaultLayoutView($defaultLayoutView)
    {
        $dataViewPathArray = explode('.', $defaultLayoutView);
        $fileName = array_pop($dataViewPathArray);
        $defaultLayoutPath = resource_path('views/'.implode('/', $dataViewPathArray));

        $files = app('Illuminate\Filesystem\Filesystem');
        $files->makeDirectory($defaultLayoutPath);
        $files->put($defaultLayoutPath.'/'.$fileName.'.blade.php', '');
    }
}
