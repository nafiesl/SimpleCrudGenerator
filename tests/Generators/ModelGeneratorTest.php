<?php

namespace Tests\Generators;

use Tests\TestCase;

class ModelGeneratorTest extends TestCase
{
    /** @test */
    public function it_creates_correct_model_class_content()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--no-interaction' => true]);

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
        \$title = __('app.show_detail_title', [
            'name' => \$this->name, 'type' => __('{$this->lang_name}.{$this->lang_name}'),
        ]);
        \$link = '<a href=\"'.route('{$this->table_name}.show', \$this).'\"';
        \$link .= ' title=\"'.\$title.'\">';
        \$link .= \$this->name;
        \$link .= '</a>';

        return \$link;
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
    public function it_creates_correct_namespaced_model_class_content()
    {
        $this->artisan('make:crud', ['name' => 'Entities/References/Category', '--no-interaction' => true]);

        $modelPath = app_path('Entities/References/Category.php');
        $this->assertFileExists($modelPath);
        $modelClassContent = "<?php

namespace App\Entities\References;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected \$fillable = ['name', 'description', 'creator_id'];

    public function getNameLinkAttribute()
    {
        \$title = __('app.show_detail_title', [
            'name' => \$this->name, 'type' => __('category.category'),
        ]);
        \$link = '<a href=\"'.route('categories.show', \$this).'\"';
        \$link .= ' title=\"'.\$title.'\">';
        \$link .= \$this->name;
        \$link .= '</a>';

        return \$link;
    }

    public function creator()
    {
        return \$this->belongsTo(User::class);
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($modelPath));

        // tearDown
        $this->removeFileOrDir(resource_path('views/categories'));
        $this->removeFileOrDir(resource_path("lang/en/category.php"));
    }
}
