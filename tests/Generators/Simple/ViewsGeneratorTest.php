<?php

namespace Tests\Generators\Simple;

use Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;

class ViewsGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_index_view_content()
    {
        $this->artisan('make:crud-simple', ['name' => $this->model_name, '--no-interaction' => true]);

        $indexViewPath = resource_path("views/{$this->table_name}/index.blade.php");
        $this->assertFileExists($indexViewPath);
        $indexViewContent = "@extends('layouts.app')

@section('title', __('{$this->lang_name}.list'))

@section('content')
<div class=\"mb-3\">
    <div class=\"float-right\">
        @can('create', new {$this->full_model_name})
            <a href=\"{{ route('{$this->table_name}.index', ['action' => 'create']) }}\" class=\"btn btn-success\">{{ __('{$this->lang_name}.create') }}</a>
        @endcan
    </div>
    <h1 class=\"page-title\">{{ __('{$this->lang_name}.list') }} <small>{{ __('app.total') }} : {{ \${$this->collection_model_var_name}->total() }} {{ __('{$this->lang_name}.{$this->lang_name}') }}</small></h1>
</div>

<div class=\"row\">
    <div class=\"col-md-8\">
        <div class=\"card\">
            <div class=\"card-header\">
                <form method=\"GET\" action=\"\" accept-charset=\"UTF-8\" class=\"form-inline\">
                    <div class=\"form-group\">
                        <label for=\"q\" class=\"form-label\">{{ __('{$this->lang_name}.search') }}</label>
                        <input placeholder=\"{{ __('{$this->lang_name}.search_text') }}\" name=\"q\" type=\"text\" id=\"q\" class=\"form-control mx-sm-2\" value=\"{{ request('q') }}\">
                    </div>
                    <input type=\"submit\" value=\"{{ __('{$this->lang_name}.search') }}\" class=\"btn btn-secondary\">
                    <a href=\"{{ route('{$this->table_name}.index') }}\" class=\"btn btn-link\">{{ __('app.reset') }}</a>
                </form>
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
                        <td>{{ \${$this->single_model_var_name}->name }}</td>
                        <td>{{ \${$this->single_model_var_name}->description }}</td>
                        <td class=\"text-center\">
                            @can('update', \${$this->single_model_var_name})
                                <a href=\"{{ route('{$this->table_name}.index', ['action' => 'edit', 'id' => \${$this->single_model_var_name}->id] + Request::only('page', 'q')) }}\" id=\"edit-{$this->lang_name}-{{ \${$this->single_model_var_name}->id }}\">{{ __('app.edit') }}</a>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class=\"card-body\">{{ \${$this->collection_model_var_name}->appends(Request::except('page'))->render() }}</div>
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
    public function it_creates_correct_forms_view_content()
    {
        $this->artisan('make:crud-simple', ['name' => $this->model_name, '--no-interaction' => true]);

        $formViewPath = resource_path("views/{$this->table_name}/forms.blade.php");
        $this->assertFileExists($formViewPath);
        $formViewContent = "@if (Request::get('action') == 'create')
@can('create', new {$this->full_model_name})
    <form method=\"POST\" action=\"{{ route('{$this->table_name}.store') }}\" accept-charset=\"UTF-8\">
        {{ csrf_field() }}
        <div class=\"form-group\">
            <label for=\"name\" class=\"form-label\">{{ __('{$this->lang_name}.name') }} <span class=\"form-required\">*</span></label>
            <input id=\"name\" type=\"text\" class=\"form-control{{ \$errors->has('name') ? ' is-invalid' : '' }}\" name=\"name\" value=\"{{ old('name') }}\" required>
            {!! \$errors->first('name', '<span class=\"invalid-feedback\" role=\"alert\">:message</span>') !!}
        </div>
        <div class=\"form-group\">
            <label for=\"description\" class=\"form-label\">{{ __('{$this->lang_name}.description') }}</label>
            <textarea id=\"description\" class=\"form-control{{ \$errors->has('description') ? ' is-invalid' : '' }}\" name=\"description\" rows=\"4\">{{ old('description') }}</textarea>
            {!! \$errors->first('description', '<span class=\"invalid-feedback\" role=\"alert\">:message</span>') !!}
        </div>
        <input type=\"submit\" value=\"{{ __('{$this->lang_name}.create') }}\" class=\"btn btn-success\">
        <a href=\"{{ route('{$this->table_name}.index') }}\" class=\"btn btn-link\">{{ __('app.cancel') }}</a>
    </form>
@endcan
@endif
@if (Request::get('action') == 'edit' && \$editable{$this->model_name})
@can('update', \$editable{$this->model_name})
    <form method=\"POST\" action=\"{{ route('{$this->table_name}.update', \$editable{$this->model_name}) }}\" accept-charset=\"UTF-8\">
        {{ csrf_field() }} {{ method_field('patch') }}
        <div class=\"form-group\">
            <label for=\"name\" class=\"form-label\">{{ __('{$this->lang_name}.name') }} <span class=\"form-required\">*</span></label>
            <input id=\"name\" type=\"text\" class=\"form-control{{ \$errors->has('name') ? ' is-invalid' : '' }}\" name=\"name\" value=\"{{ old('name', \$editable{$this->model_name}->name) }}\" required>
            {!! \$errors->first('name', '<span class=\"invalid-feedback\" role=\"alert\">:message</span>') !!}
        </div>
        <div class=\"form-group\">
            <label for=\"description\" class=\"form-label\">{{ __('{$this->lang_name}.description') }}</label>
            <textarea id=\"description\" class=\"form-control{{ \$errors->has('description') ? ' is-invalid' : '' }}\" name=\"description\" rows=\"4\">{{ old('description', \$editable{$this->model_name}->description) }}</textarea>
            {!! \$errors->first('description', '<span class=\"invalid-feedback\" role=\"alert\">:message</span>') !!}
        </div>
        <input name=\"page\" value=\"{{ request('page') }}\" type=\"hidden\">
        <input name=\"q\" value=\"{{ request('q') }}\" type=\"hidden\">
        <input type=\"submit\" value=\"{{ __('{$this->lang_name}.update') }}\" class=\"btn btn-success\">
        <a href=\"{{ route('{$this->table_name}.index', Request::only('q', 'page')) }}\" class=\"btn btn-link\">{{ __('app.cancel') }}</a>
        @can('delete', \$editable{$this->model_name})
            <a href=\"{{ route('{$this->table_name}.index', ['action' => 'delete', 'id' => \$editable{$this->model_name}->id] + Request::only('page', 'q')) }}\" id=\"del-{$this->lang_name}-{{ \$editable{$this->model_name}->id }}\" class=\"btn btn-danger float-right\">{{ __('app.delete') }}</a>
        @endcan
    </form>
@endcan
@endif
@if (Request::get('action') == 'delete' && \$editable{$this->model_name})
@can('delete', \$editable{$this->model_name})
    <div class=\"card\">
        <div class=\"card-header\">{{ __('{$this->lang_name}.delete') }}</div>
        <div class=\"card-body\">
            <label class=\"form-label text-primary\">{{ __('{$this->lang_name}.name') }}</label>
            <p>{{ \$editable{$this->model_name}->name }}</p>
            <label class=\"form-label text-primary\">{{ __('{$this->lang_name}.description') }}</label>
            <p>{{ \$editable{$this->model_name}->description }}</p>
            {!! \$errors->first('{$this->lang_name}_id', '<span class=\"invalid-feedback\" role=\"alert\">:message</span>') !!}
        </div>
        <hr style=\"margin:0\">
        <div class=\"card-body text-danger\">{{ __('{$this->lang_name}.delete_confirm') }}</div>
        <div class=\"card-footer\">
            <form method=\"POST\" action=\"{{ route('{$this->table_name}.destroy', \$editable{$this->model_name}) }}\" accept-charset=\"UTF-8\" onsubmit=\"return confirm(&quot;{{ __('app.delete_confirm') }}&quot;)\" class=\"del-form float-right\" style=\"display: inline;\">
                {{ csrf_field() }} {{ method_field('delete') }}
                <input name=\"{$this->lang_name}_id\" type=\"hidden\" value=\"{{ \$editable{$this->model_name}->id }}\">
                <input name=\"page\" value=\"{{ request('page') }}\" type=\"hidden\">
                <input name=\"q\" value=\"{{ request('q') }}\" type=\"hidden\">
                <button type=\"submit\" class=\"btn btn-danger\">{{ __('app.delete_confirm_button') }}</button>
            </form>
            <a href=\"{{ route('{$this->table_name}.index', Request::only('q', 'page')) }}\" class=\"btn btn-link\">{{ __('app.cancel') }}</a>
        </div>
    </div>
@endcan
@endif
";
        $this->assertEquals($formViewContent, file_get_contents($formViewPath));
    }

    /** @test */
    public function it_not_gives_warning_message_if_default_layout_view_does_exists()
    {
        $defaultLayoutView = config('simple-crud.default_layout_view');
        $this->generateDefaultLayoutView($defaultLayoutView);

        $this->artisan('make:crud-simple', ['name' => $this->model_name, '--no-interaction' => true]);

        $this->assertNotRegExp("/{$defaultLayoutView} view does not exists./", app(Kernel::class)->output());
    }

    /** @test */
    public function it_gives_warning_message_if_default_layout_view_does_not_exists()
    {
        $this->artisan('make:crud-simple', ['name' => $this->model_name, '--no-interaction' => true]);
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
