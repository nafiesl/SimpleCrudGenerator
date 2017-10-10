<?php

namespace Tests\Generators;

use Tests\TestCase;

class ViewsGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_index_view_content()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $indexViewPath = resource_path("views/{$this->tableName}/index.blade.php");
        $this->assertFileExists($indexViewPath);
        $indexViewContent = "@extends('layouts.app')

@section('title', trans('{$this->singleModelName}.list'))

@section('content')
<div class=\"pull-right\">
    {{ link_to_route('{$this->tableName}.index', trans('{$this->singleModelName}.create'), ['action' => 'create'], ['class' => 'btn btn-success']) }}
</div>
<h3 class=\"page-header\">
    {{ trans('{$this->singleModelName}.list') }}
    <small>{{ trans('app.total') }} : {{ \${$this->tableName}->total() }} {{ trans('{$this->singleModelName}.{$this->singleModelName}') }}</small>
</h3>
<div class=\"row\">
    <div class=\"col-md-8\">
        <div class=\"panel panel-default table-responsive\">
            <div class=\"panel-heading\">
                {{ Form::open(['method' => 'get','class' => 'form-inline']) }}
                {!! FormField::text('q', ['value' => request('q'), 'label' => trans('{$this->singleModelName}.search'), 'class' => 'input-sm']) !!}
                {{ Form::submit(trans('{$this->singleModelName}.search'), ['class' => 'btn btn-sm']) }}
                {{ link_to_route('{$this->tableName}.index', trans('app.reset')) }}
                {{ Form::close() }}
            </div>
            <table class=\"table table-condensed\">
                <thead>
                    <tr>
                        <th class=\"text-center\">{{ trans('app.table_no') }}</th>
                        <th>{{ trans('{$this->singleModelName}.name') }}</th>
                        <th>{{ trans('{$this->singleModelName}.description') }}</th>
                        <th class=\"text-center\">{{ trans('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\${$this->tableName} as \$key => \${$this->singleModelName})
                    <tr>
                        <td class=\"text-center\">{{ \${$this->tableName}->firstItem() + \$key }}</td>
                        <td>{{ \${$this->singleModelName}->name }}</td>
                        <td>{{ \${$this->singleModelName}->description }}</td>
                        <td class=\"text-center\">
                            {!! link_to_route(
                                '{$this->tableName}.index',
                                trans('app.edit'),
                                ['action' => 'edit', 'id' => \${$this->singleModelName}->id] + Request::only('page', 'q'),
                                ['id' => 'edit-{$this->singleModelName}-' . \${$this->singleModelName}->id]
                            ) !!} |
                            {!! link_to_route(
                                '{$this->tableName}.index',
                                trans('app.delete'),
                                ['action' => 'delete', 'id' => \${$this->singleModelName}->id] + Request::only('page', 'q'),
                                ['id' => 'del-{$this->singleModelName}-' . \${$this->singleModelName}->id]
                            ) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class=\"panel-body\">{{ \${$this->tableName}->appends(Request::except('page'))->render() }}</div>
        </div>
    </div>
    <div class=\"col-md-4\">
        @includeWhen(Request::has('action'), '{$this->tableName}.forms')
    </div>
</div>
@endsection
";
        $this->assertEquals($indexViewContent, file_get_contents($indexViewPath));
    }

    /** @test */
    public function it_creates_correct_forms_view_content()
    {
        $this->artisan('make:crud', ['name' => $this->modelName, '--no-interaction' => true]);

        $formViewPath = resource_path("views/{$this->tableName}/forms.blade.php");
        $this->assertFileExists($formViewPath);
        $formViewContent = "@if (Request::get('action') == 'create')
    {!! Form::open(['route' => '{$this->tableName}.store']) !!}
    {!! FormField::text('name', ['required' => true]) !!}
    {!! FormField::textarea('description') !!}
    {!! Form::submit(trans('{$this->singleModelName}.create'), ['class' => 'btn btn-success']) !!}
    {{ link_to_route('{$this->tableName}.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {!! Form::close() !!}
@endif
@if (Request::get('action') == 'edit' && \$editable{$this->modelName})
    {!! Form::model(\$editable{$this->modelName}, ['route' => ['{$this->tableName}.update', \$editable{$this->modelName}->id],'method' => 'patch']) !!}
    {!! FormField::text('name', ['required' => true]) !!}
    {!! FormField::textarea('description') !!}
    @if (request('q'))
        {{ Form::hidden('q', request('q')) }}
    @endif
    @if (request('page'))
        {{ Form::hidden('page', request('page')) }}
    @endif
    {!! Form::submit(trans('{$this->singleModelName}.update'), ['class' => 'btn btn-success']) !!}
    {{ link_to_route('{$this->tableName}.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {!! Form::close() !!}
@endif
@if (Request::get('action') == 'delete' && \$editable{$this->modelName})
    <div class=\"panel panel-default\">
        <div class=\"panel-heading\"><h3 class=\"panel-title\">{{ trans('{$this->singleModelName}.delete') }}</h3></div>
        <div class=\"panel-body\">
            <label class=\"control-label\">{{ trans('{$this->singleModelName}.name') }}</label>
            <p>{{ \$editable{$this->modelName}->name }}</p>
            {!! \$errors->first('{$this->singleModelName}_id', '<span class=\"form-error small\">:message</span>') !!}
        </div>
        <hr style=\"margin:0\">
        <div class=\"panel-body\">{{ trans('app.delete_confirm') }}</div>
        <div class=\"panel-footer\">
            {!! FormField::delete(
                ['route'=>['{$this->tableName}.destroy',\$editable{$this->modelName}->id]],
                trans('app.delete_confirm_button'),
                ['class'=>'btn btn-danger'],
                [
                    '{$this->singleModelName}_id' => \$editable{$this->modelName}->id,
                    'page' => request('page'),
                    'q' => request('q'),
                ]
            ) !!}
            {{ link_to_route('{$this->tableName}.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
        </div>
    </div>
@endif
";
        $this->assertEquals($formViewContent, file_get_contents($formViewPath));
    }
}
