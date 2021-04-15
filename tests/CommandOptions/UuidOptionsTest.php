<?php

namespace Tests\CommandOptions;

use Tests\TestCase;

class UuidOptionsTest extends TestCase
{
    /** @test */
    public function it_creates_correct_migration_class_content_for_uuid_primary_key()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--uuid' => true, '--no-interaction' => true]);

        $migrationFilePath = database_path('migrations/'.date('Y_m_d_His').'_create_'.$this->table_name.'_table.php');
        $this->assertFileExists($migrationFilePath);
        $modelClassContent = "<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create{$this->plural_model_name}Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{$this->table_name}', function (Blueprint \$table) {
            \$table->uuid('id');
            \$table->string('title', 60);
            \$table->string('description')->nullable();
            \$table->foreignId('creator_id')->constrained('users')->onDelete('restrict');
            \$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{$this->table_name}');
    }
}
";
        $this->assertEquals($modelClassContent, file_get_contents($migrationFilePath));
    }
    /** @test */
    public function it_creates_correct_model_factory_content_for_uuid_primary_key()
    {
        $this->artisan('make:crud', ['name' => $this->model_name, '--uuid' => true, '--no-interaction' => true]);

        $modelFactoryPath = database_path('factories/'.$this->model_name.'Factory.php');
        $this->assertFileExists($modelFactoryPath);
        $modelFactoryContent = "<?php

namespace Database\Factories;

use App\Models\User;
use {$this->full_model_name};
use Illuminate\Database\Eloquent\Factories\Factory;

class {$this->model_name}Factory extends Factory
{
    protected \$model = {$this->model_name}::class;

    public function definition()
    {
        return [
            'id'       => \$this->faker->uuid,
            'title'       => \$this->faker->word,
            'description' => \$this->faker->sentence,
            'creator_id'  => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
";
        $this->assertEquals($modelFactoryContent, file_get_contents($modelFactoryPath));
    }
    /** @test */
    public function it_creates_correct_model_class_content_for_uuid_primary_key()
    {
        config(['auth.providers.users.model' => 'App\Models\User']);
        $this->artisan('make:crud', ['name' => $this->model_name, '--uuid' => true, '--no-interaction' => true]);

        $modelPath = app_path('Models/'.$this->model_name.'.php');
        $this->assertFileExists($modelPath);
        $modelClassContent = "<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class {$this->model_name} extends Model
{
    use HasFactory;

    public \$incrementing = false;

    protected \$keyType = 'string';

    protected \$fillable = ['id', 'title', 'description', 'creator_id'];

    public function getTitleLinkAttribute()
    {
        \$title = __('app.show_detail_title', [
            'title' => \$this->title, 'type' => __('{$this->lang_name}.{$this->lang_name}'),
        ]);
        \$link = '<a href=\"'.route('{$this->table_name}.show', \$this).'\"';
        \$link .= ' title=\"'.\$title.'\">';
        \$link .= \$this->title;
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
}
