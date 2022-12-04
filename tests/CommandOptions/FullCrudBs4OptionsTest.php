<?php

namespace Tests\CommandOptions;

use Illuminate\Contracts\Console\Kernel;
use Tests\TestCase;

class FullCrudBs4OptionsTest extends TestCase
{
    /** @test */
    public function it_can_generate_views_with_bootstrap4_for_full_crud()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--bs4' => true]);

        $this->assertStringNotContainsString("{$this->model_name} model already exists.", app(Kernel::class)->output());

        $this->assertFileExists(app_path('Models/'.$this->model_name.'.php'));
        $this->assertFileExists(app_path("Http/Controllers/{$this->model_name}Controller.php"));

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);

        $this->assertFileExists(resource_path("views/{$this->table_name}/index.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/create.blade.php"));
        $this->assertFileExists(resource_path("views/{$this->table_name}/edit.blade.php"));
        $this->assertFileDoesNotExist(resource_path("views/{$this->table_name}/forms.blade.php"));

        $localeConfig = config('app.locale');
        $this->assertFileExists(base_path("lang/{$localeConfig}/{$this->lang_name}.php"));

        $this->assertFileExists(base_path("routes/web.php"));
        $this->assertFileExists(app_path("Policies/{$this->model_name}Policy.php"));
        $this->assertFileExists(database_path("factories/{$this->model_name}Factory.php"));
        $this->assertFileExists(base_path("tests/Unit/Models/{$this->model_name}Test.php"));
        $this->assertFileExists(base_path("tests/Feature/Manage{$this->model_name}Test.php"));
    }

    /** @test */
    public function it_creates_correct_index_view_content_with_bootstrap4()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--bs4' => true]);

        $indexViewPath = resource_path("views/{$this->table_name}/index.blade.php");
        $this->assertFileExists($indexViewPath);
        $indexViewContent = "@extends('layouts.app')

@section('title', __('{$this->lang_name}.list'))

@section('content')
<div class=\"mb-3\">
    <div class=\"float-right\">
        @can('create', new {$this->full_model_name})
            <a href=\"{{ route('{$this->table_name}.create') }}\" class=\"btn btn-success\">{{ __('{$this->lang_name}.create') }}</a>
        @endcan
    </div>
    <h1 class=\"page-title\">{{ __('{$this->lang_name}.list') }} <small>{{ __('app.total') }} : {{ \${$this->collection_model_var_name}->total() }} {{ __('{$this->lang_name}.{$this->lang_name}') }}</small></h1>
</div>

<div class=\"row\">
    <div class=\"col-md-12\">
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
                        <th>{{ __('{$this->lang_name}.title') }}</th>
                        <th>{{ __('{$this->lang_name}.description') }}</th>
                        <th class=\"text-center\">{{ __('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\${$this->collection_model_var_name} as \$key => \${$this->single_model_var_name})
                    <tr>
                        <td class=\"text-center\">{{ \${$this->collection_model_var_name}->firstItem() + \$key }}</td>
                        <td>{!! \${$this->single_model_var_name}->title_link !!}</td>
                        <td>{{ \${$this->single_model_var_name}->description }}</td>
                        <td class=\"text-center\">
                            @can('view', \${$this->single_model_var_name})
                                <a href=\"{{ route('{$this->table_name}.show', \${$this->single_model_var_name}) }}\" id=\"show-{$this->lang_name}-{{ \${$this->single_model_var_name}->id }}\">{{ __('app.show') }}</a>
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
    public function it_creates_correct_show_view_content_with_bootstrap4()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--bs4' => true]);

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
                        <tr><td>{{ __('{$this->lang_name}.title') }}</td><td>{{ \${$this->single_model_var_name}->title }}</td></tr>
                        <tr><td>{{ __('{$this->lang_name}.description') }}</td><td>{{ \${$this->single_model_var_name}->description }}</td></tr>
                    </tbody>
                </table>
            </div>
            <div class=\"card-footer\">
                @can('update', \${$this->single_model_var_name})
                    <a href=\"{{ route('{$this->table_name}.edit', \${$this->single_model_var_name}) }}\" id=\"edit-{$this->lang_name}-{{ \${$this->single_model_var_name}->id }}\" class=\"btn btn-warning\">{{ __('{$this->lang_name}.edit') }}</a>
                @endcan
                <a href=\"{{ route('{$this->table_name}.index') }}\" class=\"btn btn-link\">{{ __('{$this->lang_name}.back_to_index') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection
";
        $this->assertEquals($showFormViewContent, file_get_contents($showFormViewPath));
    }

    /** @test */
    public function it_creates_correct_create_view_content_with_bootstrap4()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--bs4' => true]);

        $createFormViewPath = resource_path("views/{$this->table_name}/create.blade.php");
        $this->assertFileExists($createFormViewPath);
        $createFormViewContent = "@extends('layouts.app')

@section('title', __('{$this->lang_name}.create'))

@section('content')
<div class=\"row justify-content-center\">
    <div class=\"col-md-6\">
        <div class=\"card\">
            <div class=\"card-header\">{{ __('{$this->lang_name}.create') }}</div>
            <form method=\"POST\" action=\"{{ route('{$this->table_name}.store') }}\" accept-charset=\"UTF-8\">
                {{ csrf_field() }}
                <div class=\"card-body\">
                    <div class=\"form-group\">
                        <label for=\"title\" class=\"form-label\">{{ __('{$this->lang_name}.title') }} <span class=\"form-required\">*</span></label>
                        <input id=\"title\" type=\"text\" class=\"form-control{{ \$errors->has('title') ? ' is-invalid' : '' }}\" name=\"title\" value=\"{{ old('title') }}\" required>
                        {!! \$errors->first('title', '<span class=\"invalid-feedback\" role=\"alert\">:message</span>') !!}
                    </div>
                    <div class=\"form-group\">
                        <label for=\"description\" class=\"form-label\">{{ __('{$this->lang_name}.description') }}</label>
                        <textarea id=\"description\" class=\"form-control{{ \$errors->has('description') ? ' is-invalid' : '' }}\" name=\"description\" rows=\"4\">{{ old('description') }}</textarea>
                        {!! \$errors->first('description', '<span class=\"invalid-feedback\" role=\"alert\">:message</span>') !!}
                    </div>
                </div>
                <div class=\"card-footer\">
                    <input type=\"submit\" value=\"{{ __('app.create') }}\" class=\"btn btn-success\">
                    <a href=\"{{ route('{$this->table_name}.index') }}\" class=\"btn btn-link\">{{ __('app.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
";
        $this->assertEquals($createFormViewContent, file_get_contents($createFormViewPath));
    }

    /** @test */
    public function it_creates_correct_edit_view_content_with_bootstrap4()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--bs4' => true]);

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
                    <label class=\"form-label text-primary\">{{ __('{$this->lang_name}.title') }}</label>
                    <p>{{ \${$this->single_model_var_name}->title }}</p>
                    <label class=\"form-label text-primary\">{{ __('{$this->lang_name}.description') }}</label>
                    <p>{{ \${$this->single_model_var_name}->description }}</p>
                    {!! \$errors->first('{$this->lang_name}_id', '<span class=\"invalid-feedback\" role=\"alert\">:message</span>') !!}
                </div>
                <hr style=\"margin:0\">
                <div class=\"card-body text-danger\">{{ __('{$this->lang_name}.delete_confirm') }}</div>
                <div class=\"card-footer\">
                    <form method=\"POST\" action=\"{{ route('{$this->table_name}.destroy', \${$this->single_model_var_name}) }}\" accept-charset=\"UTF-8\" onsubmit=\"return confirm(&quot;{{ __('app.delete_confirm') }}&quot;)\" class=\"del-form float-right\" style=\"display: inline;\">
                        {{ csrf_field() }} {{ method_field('delete') }}
                        <input name=\"{$this->lang_name}_id\" type=\"hidden\" value=\"{{ \${$this->single_model_var_name}->id }}\">
                        <button type=\"submit\" class=\"btn btn-danger\">{{ __('app.delete_confirm_button') }}</button>
                    </form>
                    <a href=\"{{ route('{$this->table_name}.edit', \${$this->single_model_var_name}) }}\" class=\"btn btn-link\">{{ __('app.cancel') }}</a>
                </div>
            </div>
        @endcan
        @else
        <div class=\"card\">
            <div class=\"card-header\">{{ __('{$this->lang_name}.edit') }}</div>
            <form method=\"POST\" action=\"{{ route('{$this->table_name}.update', \${$this->single_model_var_name}) }}\" accept-charset=\"UTF-8\">
                {{ csrf_field() }} {{ method_field('patch') }}
                <div class=\"card-body\">
                    <div class=\"form-group\">
                        <label for=\"title\" class=\"form-label\">{{ __('{$this->lang_name}.title') }} <span class=\"form-required\">*</span></label>
                        <input id=\"title\" type=\"text\" class=\"form-control{{ \$errors->has('title') ? ' is-invalid' : '' }}\" name=\"title\" value=\"{{ old('title', \${$this->single_model_var_name}->title) }}\" required>
                        {!! \$errors->first('title', '<span class=\"invalid-feedback\" role=\"alert\">:message</span>') !!}
                    </div>
                    <div class=\"form-group\">
                        <label for=\"description\" class=\"form-label\">{{ __('{$this->lang_name}.description') }}</label>
                        <textarea id=\"description\" class=\"form-control{{ \$errors->has('description') ? ' is-invalid' : '' }}\" name=\"description\" rows=\"4\">{{ old('description', \${$this->single_model_var_name}->description) }}</textarea>
                        {!! \$errors->first('description', '<span class=\"invalid-feedback\" role=\"alert\">:message</span>') !!}
                    </div>
                </div>
                <div class=\"card-footer\">
                    <input type=\"submit\" value=\"{{ __('{$this->lang_name}.update') }}\" class=\"btn btn-success\">
                    <a href=\"{{ route('{$this->table_name}.show', \${$this->single_model_var_name}) }}\" class=\"btn btn-link\">{{ __('app.cancel') }}</a>
                    @can('delete', \${$this->single_model_var_name})
                        <a href=\"{{ route('{$this->table_name}.edit', [\${$this->single_model_var_name}, 'action' => 'delete']) }}\" id=\"del-{$this->lang_name}-{{ \${$this->single_model_var_name}->id }}\" class=\"btn btn-danger float-right\">{{ __('app.delete') }}</a>
                    @endcan
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
";
        $this->assertEquals($editFormViewContent, file_get_contents($editFormViewPath));
    }
}
