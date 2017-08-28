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

@section('title', trans('master.list'))

@section('content')
{{ link_to_route('masters.index', trans('master.create'), ['action' => 'create'], ['class' => 'btn btn-success pull-right']) }}
<h3 class=\"page-header\">{{ trans('master.list') }}</h3>
<div class=\"row\">
    <div class=\"col-md-8\">
        <div class=\"panel panel-default table-responsive\">
            <table class=\"table table-condensed\">
                <thead>
                    <tr>
                        <th class=\"text-center\">{{ trans('app.table_no') }}</th>
                        <th>{{ trans('master.name') }}</th>
                        <th>{{ trans('master.description') }}</th>
                        <th class=\"text-center\">{{ trans('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\$masters as \$key => \$master)
                    <tr>
                        <td class=\"text-center\">{{ 1 + \$key }}</td>
                        <td>{{ \$master->name }}</td>
                        <td>{{ \$master->description }}</td>
                        <td class=\"text-center\">
                            {!! link_to_route('masters.index', trans('app.edit'), ['action' => 'edit', 'id' => \$master->id], ['id' => 'edit-master-' . \$master->id]) !!} |
                            {!! link_to_route('masters.index', trans('app.delete'), ['action' => 'delete', 'id' => \$master->id], ['id' => 'del-master-' . \$master->id]) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class=\"col-md-4\">
        @includeWhen(Request::has('action'), 'masters.forms')
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
    {!! Form::open(['route' => 'masters.store']) !!}
    {!! FormField::text('name') !!}
    {!! FormField::textarea('description') !!}
    {!! Form::submit(trans('master.create'), ['class' => 'btn btn-success']) !!}
    {!! Form::hidden('cat', 'master') !!}
    {{ link_to_route('masters.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {!! Form::close() !!}
@endif
@if (Request::get('action') == 'edit' && \$editableMaster)
    {!! Form::model(\$editableMaster, ['route' => ['masters.update', \$editableMaster->id],'method' => 'patch']) !!}
    {!! FormField::text('name') !!}
    {!! FormField::textarea('description') !!}
    {!! Form::submit(trans('master.update'), ['class' => 'btn btn-success']) !!}
    {{ link_to_route('masters.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {!! Form::close() !!}
@endif
@if (Request::get('action') == 'delete' && \$editableMaster)
    <div class=\"panel panel-default\">
        <div class=\"panel-heading\"><h3 class=\"panel-title\">{{ trans('master.delete') }}</h3></div>
        <div class=\"panel-body\">
            <label class=\"control-label\">{{ trans('master.name') }}</label>
            <p>{{ \$editableMaster->name }}</p>
            {!! \$errors->first('master_id', '<span class=\"form-error small\">:message</span>') !!}
        </div>
        <hr style=\"margin:0\">
        <div class=\"panel-body\">{{ trans('app.delete_confirm') }}</div>
        <div class=\"panel-footer\">
            {!! FormField::delete(['route'=>['masters.destroy',\$editableMaster->id]], trans('app.delete_confirm_button'), ['class'=>'btn btn-danger'], ['master_id' => \$editableMaster->id]) !!}
            {{ link_to_route('masters.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
        </div>
    </div>
@endif
";
        $this->assertEquals($formViewContent, file_get_contents($formViewPath));
    }
}
