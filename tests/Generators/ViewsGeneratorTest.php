<?php

namespace Tests\Generators;

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
<div class=\"pull-right\">
    {{ link_to_route('{$this->table_name}.index', trans('{$this->lang_name}.create'), ['action' => 'create'], ['class' => 'btn btn-success']) }}
</div>
<h3 class=\"page-header\">
    {{ trans('{$this->lang_name}.list') }}
    <small>{{ trans('app.total') }} : {{ \${$this->collection_model_var_name}->total() }} {{ trans('{$this->lang_name}.{$this->single_model_var_name}') }}</small>
</h3>
<div class=\"row\">
    <div class=\"col-md-8\">
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
                        <td>{{ \${$this->single_model_var_name}->name }}</td>
                        <td>{{ \${$this->single_model_var_name}->description }}</td>
                        <td class=\"text-center\">
                            {!! link_to_route(
                                '{$this->table_name}.index',
                                trans('app.edit'),
                                ['action' => 'edit', 'id' => \${$this->single_model_var_name}->id] + Request::only('page', 'q'),
                                ['id' => 'edit-{$this->single_model_var_name}-' . \${$this->single_model_var_name}->id]
                            ) !!} |
                            {!! link_to_route(
                                '{$this->table_name}.index',
                                trans('app.delete'),
                                ['action' => 'delete', 'id' => \${$this->single_model_var_name}->id] + Request::only('page', 'q'),
                                ['id' => 'del-{$this->single_model_var_name}-' . \${$this->single_model_var_name}->id]
                            ) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class=\"panel-body\">{{ \${$this->collection_model_var_name}->appends(Request::except('page'))->render() }}</div>
        </div>
    </div>
    <div class=\"col-md-4\">
        @includeWhen(Request::has('action'), '{$this->table_name}.forms')
    </div>
</div>
@endsection
";
        $this->assertEquals($indexViewContent, file_get_contents($indexViewPath));
    }

    /** @test */
    public function it_creates_correct_forms_view_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

        $formViewPath = resource_path("views/{$this->table_name}/forms.blade.php");
        $this->assertFileExists($formViewPath);
        $formViewContent = "@if (Request::get('action') == 'create')
    {!! Form::open(['route' => '{$this->table_name}.store']) !!}
    {!! FormField::text('name', ['required' => true]) !!}
    {!! FormField::textarea('description') !!}
    {!! Form::submit(trans('{$this->lang_name}.create'), ['class' => 'btn btn-success']) !!}
    {{ link_to_route('{$this->table_name}.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {!! Form::close() !!}
@endif
@if (Request::get('action') == 'edit' && \$editable{$this->model_name})
    {!! Form::model(\$editable{$this->model_name}, ['route' => ['{$this->table_name}.update', \$editable{$this->model_name}->id],'method' => 'patch']) !!}
    {!! FormField::text('name', ['required' => true]) !!}
    {!! FormField::textarea('description') !!}
    @if (request('q'))
        {{ Form::hidden('q', request('q')) }}
    @endif
    @if (request('page'))
        {{ Form::hidden('page', request('page')) }}
    @endif
    {!! Form::submit(trans('{$this->lang_name}.update'), ['class' => 'btn btn-success']) !!}
    {{ link_to_route('{$this->table_name}.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {!! Form::close() !!}
@endif
@if (Request::get('action') == 'delete' && \$editable{$this->model_name})
    <div class=\"panel panel-default\">
        <div class=\"panel-heading\"><h3 class=\"panel-title\">{{ trans('{$this->lang_name}.delete') }}</h3></div>
        <div class=\"panel-body\">
            <label class=\"control-label\">{{ trans('{$this->lang_name}.name') }}</label>
            <p>{{ \$editable{$this->model_name}->name }}</p>
            {!! \$errors->first('{$this->single_model_var_name}_id', '<span class=\"form-error small\">:message</span>') !!}
        </div>
        <hr style=\"margin:0\">
        <div class=\"panel-body\">{{ trans('app.delete_confirm') }}</div>
        <div class=\"panel-footer\">
            {!! FormField::delete(
                ['route'=>['{$this->table_name}.destroy',\$editable{$this->model_name}->id]],
                trans('app.delete_confirm_button'),
                ['class'=>'btn btn-danger'],
                [
                    '{$this->single_model_var_name}_id' => \$editable{$this->model_name}->id,
                    'page' => request('page'),
                    'q' => request('q'),
                ]
            ) !!}
            {{ link_to_route('{$this->table_name}.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
        </div>
    </div>
@endif
";
        $this->assertEquals($formViewContent, file_get_contents($formViewPath));
    }
}
