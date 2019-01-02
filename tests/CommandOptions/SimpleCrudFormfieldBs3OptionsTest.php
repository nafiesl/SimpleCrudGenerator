<?php

namespace Tests\CommandOptions;

use Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;

class SimpleCrudFormfieldBs3OptionsTest extends TestCase
{
    /** @test */
    public function it_can_generate_views_with_formfield_for_simple_crud()
    {
        $this->artisan('make:crud-simple', ['name' => $this->model_name, '--formfield' => true, '--bs3' => true]);

        $this->assertNotContains("{$this->model_name} model already exists.", app(Kernel::class)->output());

        $this->assertFileExists(app_path($this->model_name.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$this->table_name}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/forms.blade.php"));

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
        $this->artisan('make:crud-simple', ['name' => $this->model_name, '--formfield' => true, '--bs3' => true]);

        $indexViewPath = resource_path("views/{$this->table_name}/index.blade.php");
        $this->assertFileExists($indexViewPath);
        $indexViewContent = "@extends('layouts.app')

@section('title', __('{$this->lang_name}.list'))

@section('content')
<h1 class=\"page-header\">
    <div class=\"pull-right\">
        @can('create', new {$this->full_model_name})
            {{ link_to_route('{$this->table_name}.index', __('{$this->lang_name}.create'), ['action' => 'create'], ['class' => 'btn btn-success']) }}
        @endcan
    </div>
    {{ __('{$this->lang_name}.list') }}
    <small>{{ __('app.total') }} : {{ \${$this->collection_model_var_name}->total() }} {{ __('{$this->lang_name}.{$this->lang_name}') }}</small>
</h1>
<div class=\"row\">
    <div class=\"col-md-8\">
        <div class=\"panel panel-default table-responsive\">
            <div class=\"panel-heading\">
                {{ Form::open(['method' => 'get', 'class' => 'form-inline']) }}
                {!! FormField::text('q', ['label' => __('{$this->lang_name}.search'), 'placeholder' => __('{$this->lang_name}.search_text'), 'class' => 'input-sm']) !!}
                {{ Form::submit(__('{$this->lang_name}.search'), ['class' => 'btn btn-sm']) }}
                {{ link_to_route('{$this->table_name}.index', __('app.reset')) }}
                {{ Form::close() }}
            </div>
            <table class=\"table table-condensed table-hover\">
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
                        <td>{{ \${$this->single_model_var_name}->name }}</td>
                        <td>{{ \${$this->single_model_var_name}->description }}</td>
                        <td class=\"text-center\">
                            @can('update', \${$this->single_model_var_name})
                                {{ link_to_route(
                                    '{$this->table_name}.index',
                                    __('app.edit'),
                                    ['action' => 'edit', 'id' => \${$this->single_model_var_name}->id] + Request::only('page', 'q'),
                                    ['id' => 'edit-{$this->lang_name}-'.\${$this->single_model_var_name}->id]
                                ) }}
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class=\"panel-body\">{{ \${$this->collection_model_var_name}->appends(Request::except('page'))->render() }}</div>
        </div>
    </div>
    <div class=\"col-md-4\">
        @if(Request::has('action'))
        @include('{$this->table_name}.forms')
        @endif
    </div>
</div>
@endsection
";
        $this->assertEquals($indexViewContent, file_get_contents($indexViewPath));
    }

    /** @test */
    public function it_creates_correct_forms_view_content_with_formfield()
    {
        $this->artisan('make:crud-simple', ['name' => $this->model_name, '--formfield' => true, '--bs3' => true]);

        $formViewPath = resource_path("views/{$this->table_name}/forms.blade.php");
        $this->assertFileExists($formViewPath);
        $formViewContent = "@if (Request::get('action') == 'create')
@can('create', new {$this->full_model_name})
    {{ Form::open(['route' => '{$this->table_name}.store']) }}
    {!! FormField::text('name', ['required' => true, 'label' => __('{$this->lang_name}.name')]) !!}
    {!! FormField::textarea('description', ['label' => __('{$this->lang_name}.description')]) !!}
    {{ Form::submit(__('{$this->lang_name}.create'), ['class' => 'btn btn-success']) }}
    {{ link_to_route('{$this->table_name}.index', __('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {{ Form::close() }}
@endcan
@endif
@if (Request::get('action') == 'edit' && \$editable{$this->model_name})
@can('update', \$editable{$this->model_name})
    {{ Form::model(\$editable{$this->model_name}, ['route' => ['{$this->table_name}.update', \$editable{$this->model_name}], 'method' => 'patch']) }}
    {!! FormField::text('name', ['required' => true, 'label' => __('{$this->lang_name}.name')]) !!}
    {!! FormField::textarea('description', ['label' => __('{$this->lang_name}.description')]) !!}
    @if (request('q'))
        {{ Form::hidden('q', request('q')) }}
    @endif
    @if (request('page'))
        {{ Form::hidden('page', request('page')) }}
    @endif
    {{ Form::submit(__('{$this->lang_name}.update'), ['class' => 'btn btn-success']) }}
    {{ link_to_route('{$this->table_name}.index', __('app.cancel'), Request::only('page', 'q'), ['class' => 'btn btn-default']) }}
    @can('delete', \$editable{$this->model_name})
        {{ link_to_route(
            '{$this->table_name}.index',
            __('app.delete'),
            ['action' => 'delete', 'id' => \$editable{$this->model_name}->id] + Request::only('page', 'q'),
            ['id' => 'del-{$this->lang_name}-'.\$editable{$this->model_name}->id, 'class' => 'btn btn-danger pull-right']
        ) }}
    @endcan
    {{ Form::close() }}
@endcan
@endif
@if (Request::get('action') == 'delete' && \$editable{$this->model_name})
@can('delete', \$editable{$this->model_name})
    <div class=\"panel panel-default\">
        <div class=\"panel-heading\"><h3 class=\"panel-title\">{{ __('{$this->lang_name}.delete') }}</h3></div>
        <div class=\"panel-body\">
            <label class=\"control-label text-primary\">{{ __('{$this->lang_name}.name') }}</label>
            <p>{{ \$editable{$this->model_name}->name }}</p>
            <label class=\"control-label text-primary\">{{ __('{$this->lang_name}.description') }}</label>
            <p>{{ \$editable{$this->model_name}->description }}</p>
            {!! \$errors->first('{$this->lang_name}_id', '<span class=\"form-error small\">:message</span>') !!}
        </div>
        <hr style=\"margin:0\">
        <div class=\"panel-body text-danger\">{{ __('{$this->lang_name}.delete_confirm') }}</div>
        <div class=\"panel-footer\">
            {!! FormField::delete(
                ['route' => ['{$this->table_name}.destroy', \$editable{$this->model_name}]],
                __('app.delete_confirm_button'),
                ['class' => 'btn btn-danger'],
                [
                    '{$this->lang_name}_id' => \$editable{$this->model_name}->id,
                    'page' => request('page'),
                    'q' => request('q'),
                ]
            ) !!}
            {{ link_to_route('{$this->table_name}.index', __('app.cancel'), Request::only('page', 'q'), ['class' => 'btn btn-default']) }}
        </div>
    </div>
@endcan
@endif
";
        $this->assertEquals($formViewContent, file_get_contents($formViewPath));
    }
}
