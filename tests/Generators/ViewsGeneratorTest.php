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

@section('title', trans('item.list'))

@section('content')
<div class=\"pull-right\">
    {{ link_to_route('items.index', trans('item.create'), ['action' => 'create'], ['class' => 'btn btn-success']) }}
</div>
<h3 class=\"page-header\">
    {{ trans('item.list') }}
    <small>{{ trans('app.total') }} : {{ \$items->total() }} {{ trans('item.item') }}</small>
</h3>
<div class=\"row\">
    <div class=\"col-md-8\">
        <div class=\"panel panel-default table-responsive\">
            <div class=\"panel-heading\">
                {{ Form::open(['method' => 'get','class' => 'form-inline']) }}
                {!! FormField::text('q', ['value' => request('q'), 'label' => trans('item.search'), 'class' => 'input-sm']) !!}
                {{ Form::submit(trans('item.search'), ['class' => 'btn btn-sm']) }}
                {{ link_to_route('items.index', trans('app.reset')) }}
                {{ Form::close() }}
            </div>
            <table class=\"table table-condensed\">
                <thead>
                    <tr>
                        <th class=\"text-center\">{{ trans('app.table_no') }}</th>
                        <th>{{ trans('item.name') }}</th>
                        <th>{{ trans('item.description') }}</th>
                        <th class=\"text-center\">{{ trans('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\$items as \$key => \$item)
                    <tr>
                        <td class=\"text-center\">{{ 1 + \$key }}</td>
                        <td>{{ \$item->name }}</td>
                        <td>{{ \$item->description }}</td>
                        <td class=\"text-center\">
                            {!! link_to_route(
                                'items.index',
                                trans('app.edit'),
                                ['action' => 'edit', 'id' => \$item->id] + Request::only('page', 'q'),
                                ['id' => 'edit-item-' . \$item->id]
                            ) !!} |
                            {!! link_to_route(
                                'items.index',
                                trans('app.delete'),
                                ['action' => 'delete', 'id' => \$item->id] + Request::only('page', 'q'),
                                ['id' => 'del-item-' . \$item->id]
                            ) !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class=\"col-md-4\">
        @includeWhen(Request::has('action'), 'items.forms')
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
    {!! Form::open(['route' => 'items.store']) !!}
    {!! FormField::text('name') !!}
    {!! FormField::textarea('description') !!}
    {!! Form::submit(trans('item.create'), ['class' => 'btn btn-success']) !!}
    {!! Form::hidden('cat', 'item') !!}
    {{ link_to_route('items.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {!! Form::close() !!}
@endif
@if (Request::get('action') == 'edit' && \$editableItem)
    {!! Form::model(\$editableItem, ['route' => ['items.update', \$editableItem->id],'method' => 'patch']) !!}
    {!! FormField::text('name') !!}
    {!! FormField::textarea('description') !!}
    @if (request('q'))
        {{ Form::hidden('q', request('q')) }}
    @endif
    @if (request('page'))
        {{ Form::hidden('page', request('page')) }}
    @endif
    {!! Form::submit(trans('item.update'), ['class' => 'btn btn-success']) !!}
    {{ link_to_route('items.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
    {!! Form::close() !!}
@endif
@if (Request::get('action') == 'delete' && \$editableItem)
    <div class=\"panel panel-default\">
        <div class=\"panel-heading\"><h3 class=\"panel-title\">{{ trans('item.delete') }}</h3></div>
        <div class=\"panel-body\">
            <label class=\"control-label\">{{ trans('item.name') }}</label>
            <p>{{ \$editableItem->name }}</p>
            {!! \$errors->first('item_id', '<span class=\"form-error small\">:message</span>') !!}
        </div>
        <hr style=\"margin:0\">
        <div class=\"panel-body\">{{ trans('app.delete_confirm') }}</div>
        <div class=\"panel-footer\">
            {!! FormField::delete(
                ['route'=>['items.destroy',\$editableItem->id]],
                trans('app.delete_confirm_button'),
                ['class'=>'btn btn-danger'],
                [
                    'item_id' => \$editableItem->id,
                    'page' => request('page'),
                    'q' => request('q'),
                ]
            ) !!}
            {{ link_to_route('items.index', trans('app.cancel'), [], ['class' => 'btn btn-default']) }}
        </div>
    </div>
@endif
";
        $this->assertEquals($formViewContent, file_get_contents($formViewPath));
    }
}
